<?php

namespace app\controllers;

use app\models\UserModel;
use Flight;

class UserController
{
    public function doLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = trim($_POST['role'] ?? '');

        if ($email === '' || $password === '' || $role === '') {
            $_SESSION['flash_error'] = "Tous les champs sont obligatoires.";
            Flight::redirect('/');
            return;
        }

        $model = new UserModel(Flight::db());


        $model->createDefaultAdminIfNotExists();

        $user = $model->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['flash_error'] = "Identifiants incorrects.";
            Flight::redirect('/');
            return;
        }


        if ($user['role'] !== $role) {
            $_SESSION['flash_error'] = "Rôle incorrect.";
            Flight::redirect('/');
            return;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        if ($user['role'] === 'ADMIN' || $user['role'] === 'CLIENT') {
            Flight::redirect('/dashboard');
        }
    }
}
