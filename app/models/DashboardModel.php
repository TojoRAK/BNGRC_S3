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


}