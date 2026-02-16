<?php

namespace app\models;

use PDO;

class PropositionModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPropositions($idUser)
    {
        $sql = "SELECT * FROM v_objet_proposition WHERE owner_id = ? OR requester_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUser, $idUser]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getHistorique()
    {
        $sql = "
            SELECT
                e.id AS echange_id,
                e.traded_at,
                vop.*
            FROM echange e
            JOIN v_objet_proposition vop ON vop.id = e.proposition_id
            ORDER BY e.traded_at DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}