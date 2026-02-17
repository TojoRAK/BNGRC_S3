<?php

namespace app\models;

use PDO;

class AchatModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getTauxFrais(): float
    {
        $stmt = $this->db->prepare("SELECT value FROM settings WHERE `key`='taux_frais_achat'");
        $stmt->execute();
        return (float)$stmt->fetchColumn();
    }

    public function getTotalArgentDons(): float
    {
        $stmt = $this->db->query("
        SELECT 
            COALESCE(SUM(d.quantite), 0) 
            - COALESCE((
                SELECT SUM(dp.quantite_attribuee)
                FROM dispatch dp
                JOIN don dn ON dp.id_don = dn.id_don
                JOIN article art2 ON art2.id_article = dn.id_article
                WHERE art2.pu = 1
                  AND dp.id_don IS NOT NULL
            ), 0)
        FROM don d
        JOIN article art ON art.id_article = d.id_article
        WHERE art.pu = 1
    ");
        return max(0.0, (float)$stmt->fetchColumn());
    }

    public function getVilles(): array
    {
        return $this->db->query("SELECT id_ville, name FROM ville ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticles(): array
    {
        return $this->db->query("
        SELECT id_article, name, pu 
        FROM article 
        ORDER BY name
    ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticlesForAchat(): array
    {
        return $this->db->query("
        SELECT id_article, name, pu 
        FROM article 
        WHERE pu > 1
        ORDER BY name
    ")->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getBesoinRestant($id_ville, $id_article): float
    {
        $stmt = $this->db->prepare("
        SELECT 
            b.quantite - COALESCE(SUM(al.quantite), 0) AS restant
        FROM besoin_ville b
        LEFT JOIN achat ach 
            ON ach.id_ville = b.id_ville
        LEFT JOIN achat_ligne al 
            ON al.id_achat = ach.id_achat 
            AND al.id_article = b.id_article
        WHERE b.id_ville = ? 
          AND b.id_article = ?
          AND b.status != 'satisfait'   -- add this
        GROUP BY b.id_besoin, b.quantite
    ");
        $stmt->execute([$id_ville, $id_article]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_sum($rows);
    }
    public function getArgentDonsFIFO(): array
    {
        return $this->db->query("
        SELECT 
            d.*,
            d.quantite - COALESCE((
                SELECT SUM(dp.quantite_attribuee)
                FROM dispatch dp
                WHERE dp.id_don = d.id_don
            ), 0) AS quantite_disponible
        FROM don d
        JOIN article art ON art.id_article = d.id_article
        WHERE art.pu = 1
          AND d.statut != 'DISPATCHE'
        ORDER BY d.date_don, d.id_don
    ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAchatsHistorique(): array
    {
        $sql = "
        SELECT 
            ach.id_achat,
            v.name AS ville,
            art.name AS article,
            art.pu,
            al.quantite AS quantite_achetee,
            ach.total_ht,
            ach.total_ttc,
            ach.taux_frais,
            ach.date_achat,
            CASE WHEN COUNT(d.id_dispatch) > 0 THEN 1 ELSE 0 END AS deja_dispatche
        FROM achat ach
        JOIN achat_ligne al ON al.id_achat = ach.id_achat
        JOIN ville v ON v.id_ville = ach.id_ville
        JOIN article art ON art.id_article = al.id_article
        LEFT JOIN dispatch d ON d.id_achat = ach.id_achat
        GROUP BY 
            ach.id_achat, v.name, art.name, art.pu,
            al.quantite, ach.total_ht, ach.total_ttc,
            ach.taux_frais, ach.date_achat
        ORDER BY ach.date_achat DESC
    ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getBesoinsSummary(): array
    {
        $sql = "
        SELECT 
            b.id_ville,
            b.id_article,
            v.name AS ville,
            art.name AS article,
            art.pu,
            b.quantite AS besoin_initial,

            COALESCE(SUM(al.quantite), 0) AS quantite_achetee,

            b.quantite - COALESCE(SUM(al.quantite), 0) AS restant

        FROM besoin_ville b
        JOIN ville v ON v.id_ville = b.id_ville
        JOIN article art ON art.id_article = b.id_article

        LEFT JOIN achat ach 
            ON ach.id_ville = b.id_ville

        LEFT JOIN achat_ligne al 
            ON al.id_achat = ach.id_achat 
            AND al.id_article = b.id_article

        WHERE art.pu > 1   
        GROUP BY b.id_ville, b.id_article

        HAVING restant > 0

        ORDER BY v.name, art.name
    ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function dispatcherAchat($id_achat)
    {
        $this->db->beginTransaction();

        $stmt = $this->db->prepare("
        SELECT ach.id_ville, al.id_article, al.quantite
        FROM achat ach
        JOIN achat_ligne al ON al.id_achat = ach.id_achat
        WHERE ach.id_achat = ?
    ");
        $stmt->execute([$id_achat]);
        $lignes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($lignes as $ligne) {
            $villeId      = $ligne['id_ville'];
            $articleId    = $ligne['id_article'];
            $qtyRemaining = (float)$ligne['quantite'];

            $stmtBesoin = $this->db->prepare("
            SELECT id_besoin, quantite
            FROM besoin_ville
            WHERE id_ville = ? AND id_article = ? AND status != 'satisfait'
            ORDER BY date_saisie ASC
            FOR UPDATE
        ");
            $stmtBesoin->execute([$villeId, $articleId]);
            $besoins = $stmtBesoin->fetchAll(PDO::FETCH_ASSOC);

            foreach ($besoins as $besoin) {
                if ($qtyRemaining <= 0) break;

                $stmtUsed = $this->db->prepare("
                SELECT COALESCE(SUM(quantite_attribuee), 0)
                FROM dispatch
                WHERE id_besoin = ?
            ");
                $stmtUsed->execute([$besoin['id_besoin']]);
                $used = (float)$stmtUsed->fetchColumn();

                $besoinRestant = $besoin['quantite'] - $used;
                if ($besoinRestant <= 0) continue;

                $attribue = min($qtyRemaining, $besoinRestant);

                // id_don = NULL because this comes from an achat, not a don
                $stmtInsert = $this->db->prepare("
                INSERT INTO dispatch 
                    (id_don, id_achat, id_ville, id_besoin, quantite_attribuee, date_dispatch)
                VALUES 
                    (NULL, ?, ?, ?, ?, NOW())
            ");
                $stmtInsert->execute([
                    $id_achat,
                    $villeId,
                    $besoin['id_besoin'],
                    $attribue
                ]);

                $newStatus = ($attribue >= $besoinRestant) ? 'satisfait' : 'partiel';
                $stmtUpdate = $this->db->prepare("
                UPDATE besoin_ville SET status = ? WHERE id_besoin = ?
            ");
                $stmtUpdate->execute([$newStatus, $besoin['id_besoin']]);

                $qtyRemaining -= $attribue;
            }
        }

        $this->db->commit();
    }
}
