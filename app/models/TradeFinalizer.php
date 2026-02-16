<?php

namespace app\models;

use PDO;

class TradeFinalizer
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function accept($propositionId, $actorUserId)
    {
        return $this->finalize((int) $propositionId, (int) $actorUserId, 'ACCEPTED');
    }

    public function reject($propositionId, $actorUserId)
    {
        return $this->finalize((int) $propositionId, (int) $actorUserId, 'REJECTED');
    }

    public function cancel($propositionId, $actorUserId)
    {
        return $this->finalize((int) $propositionId, (int) $actorUserId, 'CANCELLED');
    }

    private function finalize($propositionId, $actorUserId, $targetStatus)
    {
        if (!in_array($targetStatus, ['ACCEPTED', 'REJECTED', 'CANCELLED'], true)) {
            throw new \InvalidArgumentException('Statut cible invalide.');
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, requester_id, owner_id, wanted_id, offered_id, status
                 FROM proposition
                 WHERE id = ?
                 FOR UPDATE'
            );
            $stmt->execute([$propositionId]);
            $prop = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$prop) {
                throw new \RuntimeException('Proposition introuvable.');
            }

            if ($prop['status'] !== 'PENDING') {
                throw new \RuntimeException('Cette proposition n\'est plus modifiable.');
            }

            $requesterId = (int) $prop['requester_id'];
            $ownerId = (int) $prop['owner_id'];
            $wantedId = (int) $prop['wanted_id'];
            $offeredId = (int) $prop['offered_id'];

            if ($targetStatus === 'CANCELLED') {
                if ($actorUserId !== $requesterId) {
                    throw new \RuntimeException('Seul le demandeur peut annuler.');
                }
            } else {
                if ($actorUserId !== $ownerId) {
                    throw new \RuntimeException('Seul le propriétaire peut accepter/refuser.');
                }
            }

            if ($targetStatus === 'ACCEPTED') {
                $objStmt = $this->pdo->prepare(
                    'SELECT id, owner_user_id FROM objet WHERE id IN (?, ?) FOR UPDATE'
                );
                $objStmt->execute([$wantedId, $offeredId]);
                $objs = $objStmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($objs) !== 2) {
                    throw new \RuntimeException('Objet introuvable pour finaliser l\'échange.');
                }

                $owners = [];
                foreach ($objs as $row) {
                    $owners[(int) $row['id']] = (int) $row['owner_user_id'];
                }

                if (($owners[$wantedId] ?? null) !== $ownerId) {
                    throw new \RuntimeException('Le propriétaire de l\'objet demandé a changé.');
                }
                if (($owners[$offeredId] ?? null) !== $requesterId) {
                    throw new \RuntimeException('Le propriétaire de l\'objet proposé a changé.');
                }

                $swap = $this->pdo->prepare('UPDATE objet SET owner_user_id = ? WHERE id = ?');
                $swap->execute([$requesterId, $wantedId]);
                $swap->execute([$ownerId, $offeredId]);

                $histo = $this->pdo->prepare('INSERT INTO histo_propietaire (objet_id, user_id) VALUES (?, ?)');
                $histo->execute([$wantedId, $requesterId]);
                $histo->execute([$offeredId, $ownerId]);

                $trade = $this->pdo->prepare('INSERT INTO echange (proposition_id) VALUES (?)');
                $trade->execute([$propositionId]);
            }

            $update = $this->pdo->prepare(
                'UPDATE proposition
                 SET status = ?, responded_at = NOW()
                 WHERE id = ?'
            );
            $update->execute([$targetStatus, $propositionId]);

            $this->pdo->commit();
            return $update->rowCount() > 0;
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
