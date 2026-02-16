<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

// (Optionnel) préremplir email admin par défaut
$defaultEmail = "admin@takalo.tld";
?>
<!doctype html>
<html lang="fr" data-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin — Connexion</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="../assets/css/theme.css" rel="stylesheet">
</head>

<body class="tt-page">

  <nav class="navbar tt-navbar sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="/admin/dash">
        <span class="tt-brand-badge fw-bold">TT</span>
        <div class="d-flex flex-column lh-sm">
          <span class="fw-semibold" style="color:var(--tt-text)">Takalo-takalo</span>
          <span class="text-muted-2 small">Admin</span>
        </div>
      </a>

      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-tt-ghost btn-tt-icon" data-tt-theme-toggle title="Basculer thème">
          <i class="bi bi-sun"></i>
        </button>
        <a class="btn btn-tt-ghost btn-sm" href="/"><i class="bi bi-box-seam me-1"></i>Frontoffice</a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="d-flex justify-content-center">
      <div class="tt-surface p-4 p-md-5 w-100" style="max-width: 520px;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="tt-chip"><i class="bi bi-shield-lock"></i> Admin</div>
            <h1 class="h4 mt-2 mb-1">Connexion backoffice</h1>
            <div class="text-muted-2">Accès réservé à l’administration.</div>
          </div>
          <a class="btn btn-tt-ghost btn-sm" href="/">Frontoffice</a>
        </div>

        <hr class="tt-divider my-4">

        <?php if ($error) { ?>
          <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
          </div>
        <?php } ?>

        <form class="needs-validation" novalidate method="POST" action="/admin/login">
          <div class="mb-3">
            <label class="form-label">Email admin</label>
            <input
              type="email"
              name="email"
              class="form-control"
              required
              value="<?= $defaultEmail ?>"
              placeholder="admin@takalo.tld">
            <div class="invalid-feedback">Email invalide.</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input
              type="password"
              name="password"
              class="form-control"
              required
              minlength="6"
              placeholder="admin1234">
            <div class="invalid-feedback">Minimum 6 caractères.</div>
          </div>

          <button class="btn btn-tt-primary w-100 py-2" type="submit">
            Se connecter
          </button>
        </form>
      </div>
    </div>
  </div>

  <footer class="tt-footer mt-4">
    <div class="container py-4">
      <div class="row g-3 align-items-center">
        <div class="col-md-6">
          <div class="d-flex align-items-center gap-2">
            <span class="tt-brand-badge fw-bold">TT</span>
            <div class="lh-sm">
              <div class="fw-semibold" style="color:var(--tt-text)">Takalo-takalo</div>
              <div class="text-muted-2 small">Échange simple • UI moderne • Responsive</div>
            </div>
          </div>
        </div>

        <div class="col-md-6 text-md-end">
          <div class="d-flex gap-3 justify-content-md-end flex-wrap">
            <a href="/" class="text-muted-2 small">Objets</a>
            <a href="/client/offers" class="text-muted-2 small">Échanges</a>
            <a href="/admin/login" class="text-muted-2 small">Admin</a>
          </div>
          <div class="text-muted-2 small mt-2">© <span id="year"></span> Takalo-takalo</div>
        </div>
      </div>
    </div>
  </footer>

  <!-- <script>
    document.getElementById('year') && (document.getElementById('year').textContent = new Date().getFullYear());

    // Bootstrap validation
    (() => {
      'use strict';
      const forms = document.querySelectorAll('.needs-validation');
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    })();
  </script> -->

  <div class="toast-container position-fixed bottom-0 end-0 p-3" id="ttToastContainer"></div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/app.js"></script>
</body>

</html>