<?php
namespace app\models;

use PDO;

class ObjetImageModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

  
    public function getImagesByObjet($objetId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM objet_image
            WHERE objet_id = ?
            ORDER BY is_main DESC, id ASC
        ");
        $stmt->execute([$objetId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function getMainImage($objetId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM objet_image
            WHERE objet_id = ? AND is_main = 1
            LIMIT 1
        ");
        $stmt->execute([$objetId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addImage($objetId, $path, $isMain = 0)
    {
        // If this is set as main, unset other main images
        if ($isMain) {
            $this->unsetMainImage($objetId);
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO objet_image (objet_id, path, is_main)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$objetId, $path, $isMain]);
        return $this->pdo->lastInsertId();
    }

  
    public function setMainImage($imageId, $objetId)
    {
        // Unset current main
        $this->unsetMainImage($objetId);

        // Set new main
        $stmt = $this->pdo->prepare("
            UPDATE objet_image
            SET is_main = 1
            WHERE id = ? AND objet_id = ?
        ");
        $stmt->execute([$imageId, $objetId]);
        return $stmt->rowCount() > 0;
    }

   
    private function unsetMainImage($objetId)
    {
        $stmt = $this->pdo->prepare("
            UPDATE objet_image
            SET is_main = 0
            WHERE objet_id = ?
        ");
        $stmt->execute([$objetId]);
    }

  
    public function deleteImage($imageId)
    {
        $stmt = $this->pdo->prepare("
            SELECT path FROM objet_image WHERE id = ?
        ");
        $stmt->execute([$imageId]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            // Delete file from filesystem
            $filePath = $_SERVER['DOCUMENT_ROOT'] . $image['path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete from database
            $stmt = $this->pdo->prepare("DELETE FROM objet_image WHERE id = ?");
            $stmt->execute([$imageId]);
            return $stmt->rowCount() > 0;
        }

        return false;
    }

  
    public function deleteImagesByObjet($objetId)
    {
        $images = $this->getImagesByObjet($objetId);
        
        foreach ($images as $image) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . $image['path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $stmt = $this->pdo->prepare("DELETE FROM objet_image WHERE objet_id = ?");
        $stmt->execute([$objetId]);
        return $stmt->rowCount();
    }

  
    public function getImageCount($objetId)
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count FROM objet_image
            WHERE objet_id = ?
        ");
        $stmt->execute([$objetId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}