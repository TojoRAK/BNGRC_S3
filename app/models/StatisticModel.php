<?php

namespace app\models;

use PDO;

class StatisticModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function countTotalObjet()
    {
        $stmt = $this->pdo->query("SELECT count(id) nb FROM objet");

        return $stmt->fetchColumn();
    }

    public function countTotalUsers()
    {
        $stmt = $this->pdo->query("SELECT count(id) nb FROM users");

        return $stmt->fetchColumn();
    }

    public function countTotalEchange()
    {
        $stmt = $this->pdo->query("SELECT count(id) nb FROM echange");

        return $stmt->fetchColumn();
    }

    
}
