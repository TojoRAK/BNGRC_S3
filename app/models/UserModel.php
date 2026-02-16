<?php

namespace app\models;

use PDO;
use PDOException;

class UserModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function checkLoginClient($email, $pwd)
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, email, password_hash FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (!password_verify($pwd, $user['password_hash'])) {
            return false;
        }

        return $user;
    }



    public function checkLoginAdmin($email, $pwd)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ? and role=\"ADMIN\" LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            if (!password_verify($pwd, $user['password_hash'])) {
                return false;
            }
        }
        return $user;
    }
    static function post_trim($key)
    {
        return isset($_POST[$key]) ? trim($_POST[$key]) : '';
    }
    public function validateInput($input)
    {
        $errors = [
            'nom' => '',
            'prenom' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
        ];

        $values = [
            'nom' => trim($input['nom'] ?? ''),
            'prenom' => trim($input['prenom'] ?? ''),
            'email' => trim($input['email'] ?? ''),
        ];

        $password = $input['password'] ?? '';
        $confirm = $input['confirm_password'] ?? '';

        if (mb_strlen($values['nom']) < 2) {
            $errors['nom'] = "Le nom doit contenir au moins 2 caractères.";
        }

        if (mb_strlen($values['prenom']) < 2) {
            $errors['prenom'] = "Le prénom doit contenir au moins 2 caractères.";
        }

        if ($values['email'] === '') {
            $errors['email'] = "L'email est obligatoire.";
        } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "L'email n'est pas valide (ex: nom@domaine.com).";
        }

        if (strlen($password) < 8) {
            $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
        }

        if (strlen($confirm) < 8) {
            $errors['confirm_password'] = "Veuillez confirmer le mot de passe (min 8 caractères).";
        } elseif ($password !== $confirm) {
            $errors['confirm_password'] = "Les mots de passe ne correspondent pas.";
            if ($errors['password'] === '') {
                $errors['password'] = "Vérifiez le mot de passe et sa confirmation.";
            }
        }


        if ($this->pdo && $errors['email'] === '') {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$values['email']]);
            if ($stmt->fetch()) {
                $errors['email'] = "Cet email est déjà utilisé.";
            }
        }

        $ok = true;
        foreach ($errors as $msg) {
            if ($msg !== '') {
                $ok = false;
                break;
            }
        }

        return ['ok' => $ok, 'errors' => $errors, 'values' => $values];
    }
    public function registerUser($input)
    {
        $hash = password_hash($input['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name,created_at,password_hash,email) VALUES (?,NOW(),?,?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$input['nom'] . "" . $input['prenom'], $hash, $input['email']]);

    }

}