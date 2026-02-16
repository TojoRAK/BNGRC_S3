<?php
namespace app\models;

use PDO;

class DashboardModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function getTotalDons()
    {
        $sql = "SELECT SUM(article.pu * don.quantite) FROM don JOIN article ON don.id_article = article.id_article";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }
    public function getBesoinsTotaux()
    {
        $sql = "SELECT SUM(a.pu * bv.quantite) FROM besoin_ville bv JOIN article a ON bv.id_article = a.id_article";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }
    public function formatDeviseAr($montant)
    {
        return number_format((int) $montant, 0, ',', ' ') . ' Ar';
    }
    public function getDetails()
    {
        $sql = "SELECT * FROM v_details_ville";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRegions()
    {
        $sql = "SELECT id_region, name FROM region ORDER BY name";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVilles(?int $regionId )
    {
        if ($regionId === null) {
            $sql = "SELECT id_ville, id_region, name FROM ville ORDER BY name";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $sql = "SELECT id_ville, id_region, name FROM ville WHERE id_region = ? ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$regionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function filter(?int $regionId, ?int $villeId, $besoin)
    {
        $sql = "SELECT * FROM v_details_ville v WHERE 1=1\n";
        $params = [];

        if ($regionId !== null) {
            $sql .= " AND v.id_region = ?\n";
            $params[] = $regionId;
        }
        if ($villeId !== null) {
            $sql .= " AND v.id_ville = ?\n";
            $params[] = $villeId;
        }
        // if (!empty($besoin) && $besoin != null) {
        //     $sql .= "AND id_ville = ?";
        // }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRegionById(int $regionId): ?array
    {
        $sql = "SELECT id_region, name FROM region WHERE id_region = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$regionId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getBesoinsDetailsByRegion(int $regionId): array
    {
        $sql = "
            SELECT
                tb.name AS type_besoin,
                a.name AS article,
                a.pu AS pu,
                SUM(bv.quantite) AS quantite_total,
                SUM(bv.quantite * a.pu) AS montant_total
            FROM besoin_ville bv
            JOIN ville v ON v.id_ville = bv.id_ville
            JOIN article a ON a.id_article = bv.id_article
            JOIN type_besoin tb ON tb.id_type = a.id_type
            WHERE v.id_region = ?
            GROUP BY tb.name, a.id_article, a.name, a.pu
            ORDER BY tb.name, a.name
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$regionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}