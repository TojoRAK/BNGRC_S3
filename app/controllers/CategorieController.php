<?php

namespace app\controllers;

use Flight;
use app\models\CategorieModel;

class CategorieController
{
    private $app;

    public function __construct($app = null)
    {
        $this->app = $app;
    }

    private function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if (!$raw) return [];
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getAllCategories()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $model      = new CategorieModel(Flight::db());
            $categories = $model->getAllCategories();

            echo json_encode([
                'ok'    => true,
                'data'  => $categories,
                'count' => count($categories),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'ok'      => false,
                'message' => 'Erreur lors de la récupération des catégories',
                'error'   => $e->getMessage(),
            ]);
        }
    }


    public function getCategory($id)
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!is_numeric($id) || $id <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'ID de catégorie invalide']);
            return;
        }

        try {
            $model    = new CategorieModel(Flight::db());
            $category = $model->getCategoryById($id);

            if (!$category) {
                http_response_code(404);
                echo json_encode(['ok' => false, 'message' => 'Catégorie non trouvée']);
                return;
            }

            echo json_encode(['ok' => true, 'data' => $category]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'ok'      => false,
                'message' => 'Erreur lors de la récupération de la catégorie',
                'error'   => $e->getMessage(),
            ]);
        }
    }

   
    public function createCategory()
    {
        header('Content-Type: application/json; charset=utf-8');

        // Accept application/json body
        $body   = $this->getJsonBody();
        $name   = trim($body['name'] ?? $_POST['name'] ?? '');
        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Nom de catégorie requis';
        } elseif (strlen($name) > 80) {
            $errors['name'] = 'Le nom ne doit pas dépasser 80 caractères';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'errors' => $errors]);
            return;
        }

        try {
            $model           = new CategorieModel(Flight::db());
            $existingCategory = $model->getCategoryByName($name);

            if ($existingCategory) {
                http_response_code(409);
                echo json_encode([
                    'ok'     => false,
                    'errors' => ['name' => 'Cette catégorie existe déjà'],
                ]);
                return;
            }

            $categoryId = $model->createCategory(['name' => $name]);
            $category   = $model->getCategoryById($categoryId);

            http_response_code(201);
            echo json_encode([
                'ok'      => true,
                'message' => 'Catégorie créée avec succès',
                'data'    => $category,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'ok'      => false,
                'message' => 'Erreur lors de la création de la catégorie',
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function updateCategory($id)
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!is_numeric($id) || $id <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'ID de catégorie invalide']);
            return;
        }

        // Accept application/json body
        $body   = $this->getJsonBody();
        $name   = trim($body['name'] ?? $_POST['name'] ?? '');
        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Nom de catégorie requis';
        } elseif (strlen($name) > 80) {
            $errors['name'] = 'Le nom ne doit pas dépasser 80 caractères';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'errors' => $errors]);
            return;
        }

        try {
            $model = new CategorieModel(Flight::db());

            if (!$model->categoryExists($id)) {
                http_response_code(404);
                echo json_encode(['ok' => false, 'message' => 'Catégorie non trouvée']);
                return;
            }

            $existingCategory = $model->getCategoryByName($name);
            if ($existingCategory && $existingCategory['id'] != $id) {
                http_response_code(409);
                echo json_encode([
                    'ok'     => false,
                    'errors' => ['name' => 'Cette catégorie existe déjà'],
                ]);
                return;
            }

            $success = $model->updateCategory($id, ['name' => $name]);

            if ($success) {
                $category = $model->getCategoryById($id);
                echo json_encode([
                    'ok'      => true,
                    'message' => 'Catégorie mise à jour avec succès',
                    'data'    => $category,
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'ok'      => false,
                    'message' => 'Échec de la mise à jour de la catégorie',
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'ok'      => false,
                'message' => 'Erreur lors de la mise à jour de la catégorie',
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function deleteCategory($id)
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!is_numeric($id) || $id <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'ID de catégorie invalide']);
            return;
        }

        try {
            $model = new CategorieModel(Flight::db());

            if (!$model->categoryExists($id)) {
                http_response_code(404);
                echo json_encode(['ok' => false, 'message' => 'Catégorie non trouvée']);
                return;
            }

            $model->deleteCategory($id);

            echo json_encode([
                'ok'      => true,
                'message' => 'Catégorie supprimée avec succès',
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'ok'      => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}