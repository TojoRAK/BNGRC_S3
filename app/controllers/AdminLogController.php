<?php

namespace app\controllers;

use app\models\CategorieModel;
use app\models\UserModel;
use Exception;
use Flight;
use flight\Engine;

class AdminLogController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function doLogin()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $pwd   = isset($_POST['password']) ? trim($_POST['password']) : '';

        try {
            if ($email === '' || $pwd === '') {
                $_SESSION['flash_error'] = "Veuillez remplir l'email et le mot de passe.";
                Flight::redirect('/admin/login');
                return;
            }

            $model = new UserModel(Flight::db());
            $user  = $model->checkLoginAdmin($email, $pwd);

            if (!$user) {
                $_SESSION['flash_error'] = "Email ou mot de passe incorrect.";
                Flight::redirect('/admin/login');
                return;
            }

            // session_regenerate_id(true);

            $_SESSION['admin'] = [
                'id'    => $user['id'],
                'name'  => ($user['name'] ?? 'Admin'),
                'role'  => ($user['role'] ?? 'ADMIN'),
                'email' => ($user['email'] ?? $email),
            ];

            unset($_SESSION['flash_error']);

            Flight::redirect('/admin/dash');
            return;
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Erreur serveur : " . $e->getMessage();
            Flight::redirect('/admin/login');
            return;
        }
    }

    public function doLogout()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        unset($_SESSION['admin']);

        session_destroy();

        // session_regenerate_id(true);

        Flight::redirect('/admin/login');
        return;
    }
}
