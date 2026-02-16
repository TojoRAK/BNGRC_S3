<?php

namespace app\models;

use PDO;

class UserModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getUserByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createDefaultAdminIfNotExists()
    {
        $count = $this->db
            ->query("SELECT COUNT(*) FROM Users WHERE role='ADMIN'")
            ->fetchColumn();

        if ($count == 0) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("
                INSERT INTO Users (username, email, password_hash, role)
                VALUES ('admin','admin@test.com', ?, 'ADMIN')
            ");

            $stmt->execute([$hash]);
        }
    }
}

