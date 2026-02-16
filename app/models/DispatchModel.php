<?php

namespace app\models;

use PDO;
use PDOException;

class DispatchModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    

    
}
