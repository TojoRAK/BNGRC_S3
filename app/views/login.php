<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
}
?>
<!doctype html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — BNGRC</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/theme.css" rel="stylesheet">
</head>

<body class="bngrc-app">
    <div class="container" style="max-width: 520px;">
        <div class="py-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bngrc-logo mb-3">
                    <i class="bi bi-box2-heart"></i>
                </div>
                <h1 class="h4 mb-1">Connexion</h1>
                <div class="text-secondary small">Accès BNGRC — Dons & Distributions</div>
            </div>

            <?php if (!empty($_SESSION['flash_error'])) { ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars((string) $_SESSION['flash_error']); ?>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php } ?>

            <div class="card bngrc-card">
                <div class="card-body">
                    <form method="POST" action="/login" class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Mot de passe</label>
                            <input class="form-control" type="password" name="password" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Se connecter en tant que</label>
                            <select class="form-select" name="role" required>
                                <option value="">-- Choisir --</option>
                                <option value="CLIENT">Client</option>
                                <option value="ADMIN">Admin</option>
                            </select>
                        </div>

                        <div class="col-12 d-grid">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-box-arrow-in-right me-1"></i>Se connecter</button>
                        </div>
                    </form>
                </div>

                <div class="card-footer bngrc-cardfoot">
                    <div class="text-secondary small">
                        <strong>Admin par défaut si aucun admin en base :</strong>
                        <div>Email: admin@test.com</div>
                        <div>Password: admin123</div>
                        <div>Role: ADMIN</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
