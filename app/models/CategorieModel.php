<?php

namespace app\models;

use PDO;
use PDOException;

class CategorieModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllCategories()
    {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCategory($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$data['name']]);
        return $this->pdo->lastInsertId();
    }

    public function updateCategory($id, $data)
    {
        $stmt = $this->pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$data['name'], $id]);
        return $stmt->rowCount() > 0;
    }

    public function deleteCategory($id)
    {
      
        $checkStmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM objet WHERE category_id = ?");
        $checkStmt->execute([$id]);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            throw new \Exception("Cannot delete category: it is being used by " . $result['count'] . " object(s)");
        }
        
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public function getCategoryByName($name)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function categoryExists($id)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

}