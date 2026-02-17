<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f5f5f5;
        }

        .login-container {
            max-width: 500px;
            margin: 60px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .login-logo {
            display: block;
            margin: 0 auto 20px;
            width: 120px;
        }

        .user-photo {
            width: 80px;
            border-radius: 50%;
            margin-left: auto;
            display: block;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <img src="/assets/img/bngrc-logo.jpeg" class="login-logo" alt="Logo">
        <h4 class="text-center mb-4">Connexion</h4>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['flash_error'];
                                            unset($_SESSION['flash_error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Rôle</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="ADMIN">Admin</option>
                    <option value="CLIENT">Client</option>
                </select>
            </div>
            <div class="card-footer bngrc-cardfoot">
                <div class="text-secondary small">
                    <strong>Admin par défaut si aucun admin en base :</strong>
                    <div>Email: admin@test.com</div>
                    <div>Password: admin123</div>
                    <div>Role: ADMIN</div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Se connecter</button>

            <!-- Optional user photo on the right -->
            <!-- <img src="/assets/user-placeholder.png" class="user-photo mt-3" alt="User Photo"> -->
        </form>
    </div>
</body>

</html>