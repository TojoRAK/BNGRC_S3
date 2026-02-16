<?php

namespace app\models;

use PDO;

class TradeOffer
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function requestObject($requesterId, $wantedId, $offeredId, $message = null)
    {
        if ((int) $wantedId === (int) $offeredId) {
            throw new \InvalidArgumentException('Les objets doivent être différents.');
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, owner_user_id FROM objet WHERE id IN (?, ?)' 
            );
            $stmt->execute([(int) $wantedId, (int) $offeredId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($rows) !== 2) {
                throw new \RuntimeException('Objet introuvable (wanted/offered).');
            }

            $ownersByObjectId = [];
            foreach ($rows as $row) {
                $ownersByObjectId[(int) $row['id']] = (int) $row['owner_user_id'];
            }

            if (!array_key_exists((int) $wantedId, $ownersByObjectId)) {
                throw new \RuntimeException('Objet demandé introuvable.');
            }
            if (!array_key_exists((int) $offeredId, $ownersByObjectId)) {
                throw new \RuntimeException('Objet proposé introuvable.');
            }

            $ownerId = $ownersByObjectId[(int) $wantedId];
            $offeredOwnerId = $ownersByObjectId[(int) $offeredId];

            if ((int) $offeredOwnerId !== (int) $requesterId) {
                throw new \RuntimeException("L'objet proposé n'appartient pas au demandeur.");
            }

            $insert = $this->pdo->prepare(
                "INSERT INTO proposition (requester_id, owner_id, wanted_id, offered_id, message, status)
                 VALUES (?, ?, ?, ?, ?, 'PENDING')"
            );
            $insert->execute([
                (int) $requesterId,
                (int) $ownerId,
                (int) $wantedId,
                (int) $offeredId,
                $message,
            ]);

            $id = (int) $this->pdo->lastInsertId();
            $this->pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
