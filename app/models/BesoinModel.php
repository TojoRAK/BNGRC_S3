<?php

namespace app\models;

use PDO;
use PDOException;

class BesoinModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createBesoin($ville_id, $article_id, $quantite, $date_saisie = null)
    {
        try {
            $sql = "INSERT INTO besoin_ville (id_ville, id_article, quantite, date_saisie)
                    VALUES (:ville, :article, :quantite, :date_saisie)";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':ville' => $ville_id,
                ':article' => $article_id,
                ':quantite' => $quantite,
                ':date_saisie' => $date_saisie ?? date('Y-m-d H:i:s')
            ]);

            return true;
        } catch (PDOException $e) {
            throw new PDOException("Erreur insertion besoin : " . $e->getMessage());
        }
    }


    public function listBesoins($ville_id = null, $date_debut = null, $date_fin = null)
    {
        $sql = "SELECT b.id_besoin,
                       v.name AS ville,
                       a.name AS article,
                       a.pu,
                       b.quantite,
                       b.date_saisie
                FROM besoin_ville b
                JOIN ville v ON v.id_ville = b.id_ville
                JOIN article a ON a.id_article = b.id_article
                WHERE 1=1";

        $params = [];

        if ($ville_id) {
            $sql .= " AND b.id_ville = :ville";
            $params[':ville'] = $ville_id;
        }

        if ($date_debut) {
            $sql .= " AND b.date_saisie >= :debut";
            $params[':debut'] = $date_debut;
        }

        if ($date_fin) {
            $sql .= " AND b.date_saisie <= :fin";
            $params[':fin'] = $date_fin;
        }

        $sql .= " ORDER BY b.date_saisie DESC LIMIT 20";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVilles()
    {
        return $this->pdo
            ->query("SELECT id_ville, name FROM ville ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticles()
    {
        return $this->pdo
            ->query("SELECT id_article, name, pu FROM article ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
