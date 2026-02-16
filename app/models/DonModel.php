<?php

namespace app\models;

use PDO;
use PDOException;

class DonModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createDon($article_id, $quantite, $date_don, $source = null)
    {
        try {
            $sql = "INSERT INTO don (id_article, quantite, date_don, source) VALUES (:article, :quantite, :date_don, :source)";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':article' => $article_id,
                ':quantite' => $quantite,
                ':date_don' => $date_don,
                ':source' => $source
            ]);

            return true;
        } catch (PDOException $e) {
            throw new PDOException("Erreur insertion don : " . $e->getMessage());
        }
    }


    public function listDons($statut = null, $date_debut = null, $date_fin = null)
    {
        $sql = "SELECT d.id_don,a.name AS article,a.pu,d.quantite,d.date_don,d.source,d.statut FROM don d JOIN article a ON a.id_article = d.id_article WHERE 1=1";

        $params = [];

        if ($statut) {
            $sql .= " AND d.statut = :statut";
            $params[':statut'] = $statut;
        }

        if ($date_debut) {
            $sql .= " AND d.date_don >= :debut";
            $params[':debut'] = $date_debut;
        }

        if ($date_fin) {
            $sql .= " AND d.date_don <= :fin";
            $params[':fin'] = $date_fin;
        }

        $sql .= " ORDER BY d.date_don DESC LIMIT 20";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArticles()
    {
        return $this->pdo
            ->query("SELECT id_article, name, pu FROM article ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
