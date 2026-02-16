<?php

namespace app\controllers;

use app\models\CategorieModel;
use app\models\UserModel;
use Exception;
use Flight;
use flight\Engine;

class AuthClient
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function doLogin()
    {
        header('Content-Type: application/json; charset=utf-8');
        $email = trim($_POST['email']);
        $pwd = trim($_POST['password']);

        try {

            $model = new UserModel(Flight::db());

            $errors = "";
            $user = $model->checkLoginClient($email, $pwd);

            if (!$user) {
                $errors = "Mot de passe incorrect";
            }

            if (!empty($errors)) {
                echo json_encode([
                    'ok' => false,
                    'errors' => $errors
                ]);
                return;
            }
            echo json_encode(
                [
                    'ok' => true,
                ]
            );
            // session_start();
            $_SESSION['user'] = $user;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'errors' => $e->getMessage()
            ]);
        }
    }


    function validateInputAndLogin()
    {
        header('Content-Type: application/json; charset=utf-8');
        $input = [
            'nom' => UserModel::post_trim('nom'),
            'prenom' => UserModel::post_trim('prenom'),
            'email' => UserModel::post_trim('email'),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
        ];
        $model = new UserModel(Flight::db());
        $result = $model->validateInput($input);
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            echo json_encode([
                'ok' => $result['ok'],
                'errors' => $result['errors'],
                'values' => $result['values'],
            ]);
            return;
        }

        if ($result['ok']) {
            $model->registerUser($input);
            Flight::redirect('/');
            return;
        }

        Flight::render('client/register', [
            'errors' => $result['errors'],
            'values' => $result['values'],
        ]);
    }
    public function doLogout()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        unset($_SESSION['user']);

        session_regenerate_id(true);

        session_destroy();


        Flight::redirect('/');
        return;
    }

}