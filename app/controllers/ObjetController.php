<?php
namespace app\controllers;

use Flight;
use app\models\ObjetModel;
use app\models\ObjetImageModel;
use app\models\CategorieModel;

class ObjetController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user']['id'] ?? null;
        $objetModel = new ObjetModel(Flight::db());

        $title = trim((string) ($_GET['title'] ?? ''));
        $categoryId = (int) ($_GET['category_id'] ?? 0);

        if ($title !== '' || $categoryId > 0) {
            $objets = $objetModel->getAvailableObjectsFiltered($userId, $title, $categoryId);
        } else {
            $objets = $objetModel->getAllObjectsExceptUser($userId);
        }

        // Get categories
        $categorieModel = new CategorieModel(Flight::db());
        $categories = $categorieModel->getAllCategories();

        Flight::render('client/index', [
            'objets' => $objets,
            'categories' => $categories
        ]);
    }
    //
    public function show($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $objetModel = new ObjetModel(Flight::db());
        $objet = $objetModel->getObjectById($id);

        if (!$objet) {
            Flight::halt(404, "Objet non trouvé");
        }

        // Get images
        $imageModel = new ObjetImageModel(Flight::db());
        $images = $imageModel->getImagesByObjet($id);

        Flight::render('client/item_detail', [
            'objet' => $objet,
            'images' => $images
        ]);
    }

    public function myObjets()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::redirect('/');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $model = new ObjetModel(Flight::db());
        $objets = $model->getObjectsByUser($userId);

        // Get categories for the form
        $categorieModel = new CategorieModel(Flight::db());
        $categories = $categorieModel->getAllCategories();

        Flight::render('client/my_items', [
            'objets' => $objets,
            'categories' => $categories
        ]);
    }

    public function filter()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $model = new ObjetModel(Flight::db());
        $userId = $_SESSION['user']['id'] ?? null;

        $title = trim((string) ($_GET['title'] ?? ''));
        $category = (int) ($_GET['category_id'] ?? 0);
        $data = $model->getAvailableObjectsFiltered($userId, $title, $category);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok' => true,
            'data' => $data
        ]);
    }
    public function list()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::json(['ok' => false, 'message' => 'Non authentifié'], 401);
            return;
        }

        $model = new ObjetModel(Flight::db());
        $userId = $_SESSION['user']['id'];
        $data = $model->getObjectsByUser($userId);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok' => true,
            'data' => $data
        ]);
    }

    public function getOne($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::json(['ok' => false, 'message' => 'Non authentifié'], 401);
            return;
        }

        $model = new ObjetModel(Flight::db());
        $objet = $model->getObjectById($id);

        if (!$objet) {
            Flight::json(['ok' => false, 'message' => 'Objet non trouvé'], 404);
            return;
        }

        Flight::json(['ok' => true, 'data' => $objet]);
    }

    public function create()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::json(['ok' => false, 'message' => 'Non authentifié'], 401);
            return;
        }

        try {
            $model = new ObjetModel(Flight::db());

            // Get POST data
            $input = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'estimated_value' => $_POST['estimated_value'] ?? 0,
                'category_id' => $_POST['category_id'] ?? 0,
                'owner_user_id' => $_SESSION['user']['id']
            ];

            // Create the object
            $objetId = $model->createObject($input);

            // Handle image uploads
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $uploadedCount = $this->handleImageUploads($objetId, $_FILES['images']);
            }

            Flight::json([
                'ok' => true,
                'message' => 'Objet ajouté avec succès',
                'objet_id' => $objetId,
                'images_uploaded' => $uploadedCount ?? 0
            ]);
        } catch (\Exception $e) {
            Flight::json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::json(['ok' => false, 'message' => 'Non authentifié'], 401);
            return;
        }

        try {
            $model = new ObjetModel(Flight::db());

            // Get POST data
            $input = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'estimated_value' => $_POST['estimated_value'] ?? 0,
                'category_id' => $_POST['category_id'] ?? 0
            ];

            $model->updateObject($id, $input);

            // Handle new image uploads if any
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $uploadedCount = $this->handleImageUploads($id, $_FILES['images']);
            }

            Flight::json([
                'ok' => true,
                'message' => 'Objet mis à jour avec succès',
                'images_uploaded' => $uploadedCount ?? 0
            ]);
        } catch (\Exception $e) {
            Flight::json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::json(['ok' => false, 'message' => 'Non authentifié'], 401);
            return;
        }

        try {
            // Delete images first
            $imageModel = new ObjetImageModel(Flight::db());
            $imageModel->deleteImagesByObjet($id);

            // Delete object
            $model = new ObjetModel(Flight::db());
            $model->deleteObject($id);

            Flight::json(['ok' => true, 'message' => 'Objet supprimé']);
        } catch (\Exception $e) {
            Flight::json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }


    private function handleImageUploads($objetId, $files)
    {
        $imageModel = new ObjetImageModel(Flight::db());
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/objets/';

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        $uploadedCount = 0;

        // Check if files is an array or single file
        if (is_array($files['name'])) {
            // Multiple files
            $totalFiles = count($files['name']);

            for ($i = 0; $i < $totalFiles; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                // Validate file type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $files['tmp_name'][$i]);
                finfo_close($finfo);

                if (!in_array($mimeType, $allowedTypes)) {
                    continue; // Skip invalid file
                }

                // Validate file size
                if ($files['size'][$i] > $maxSize) {
                    continue; // Skip file too large
                }

                // Generate unique filename
                $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $filename = uniqid('objet_' . $objetId . '_') . '.' . $extension;
                $filePath = $uploadDir . $filename;

                // Move uploaded file
                if (move_uploaded_file($files['tmp_name'][$i], $filePath)) {
                    // Save to database
                    $relativePath = '/uploads/objets/' . $filename;

                    // First image is main if no main image exists
                    $isMain = ($uploadedCount === 0 && $imageModel->getImageCount($objetId) === 0) ? 1 : 0;

                    $imageModel->addImage($objetId, $relativePath, $isMain);
                    $uploadedCount++;
                }
            }
        } else {
            // Single file
            if ($files['error'] === UPLOAD_ERR_OK) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $files['tmp_name']);
                finfo_close($finfo);

                if (in_array($mimeType, $allowedTypes) && $files['size'] <= $maxSize) {
                    $extension = pathinfo($files['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('objet_' . $objetId . '_') . '.' . $extension;
                    $filePath = $uploadDir . $filename;

                    if (move_uploaded_file($files['tmp_name'], $filePath)) {
                        $relativePath = '/uploads/objets/' . $filename;
                        $isMain = ($imageModel->getImageCount($objetId) === 0) ? 1 : 0;
                        $imageModel->addImage($objetId, $relativePath, $isMain);
                        $uploadedCount++;
                    }
                }
            }
        }

        return $uploadedCount;
    }

    public function getImages($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $imageModel = new ObjetImageModel(Flight::db());
        $images = $imageModel->getImagesByObjet($id);

        Flight::json(['ok' => true, 'data' => $images]);
    }


    public function deleteImage($imageId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::json(['ok' => false, 'message' => 'Non authentifié'], 401);
            return;
        }

        $imageModel = new ObjetImageModel(Flight::db());
        $result = $imageModel->deleteImage($imageId);

        Flight::json(['ok' => $result, 'message' => $result ? 'Image supprimée' : 'Erreur']);
    }


    public function setMainImage($imageId, $objetId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::json(['ok' => false, 'message' => 'Non authentifié'], 401);
            return;
        }

        $imageModel = new ObjetImageModel(Flight::db());
        $result = $imageModel->setMainImage($imageId, $objetId);

        Flight::json(['ok' => $result, 'message' => $result ? 'Image principale définie' : 'Erreur']);
    }
}