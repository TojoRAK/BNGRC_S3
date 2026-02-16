<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['admin'])) {
  header('Location: /');
  exit;
}
$nonce = Flight::app()->get('csp_nonce') ?? '';
?>

<!doctype html>
<html lang="fr" data-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin — Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="../assets/css/theme.css" rel="stylesheet">
</head>

<body class="tt-page">

  <!-- components/header_admin.html -->
  <nav class="navbar tt-navbar sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="../admin/dashboard.html">
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
        <a class="btn btn-outline-secondary btn-sm" href="/admin/logout"><i class="bi bi-box-arrow-right me-1"></i>Déconnexion</a>
      </div>
    </div>
  </nav>


  <main class="tt-main">
    <div class="container py-4">
      <div class="admin-shell">
        <!-- components/admin_sidebar.html -->
        <aside class="tt-surface p-3">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
              <div class="fw-semibold">Backoffice</div>
              <div class="text-muted-2 small">Takalo-takalo</div>
            </div>
            <span class="badge badge-soft">v1 UI</span>
          </div>

          <div class="d-grid gap-2">
            <a class="btn btn-tt-ghost text-start" href="/admin/dash"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            <a class="btn btn-tt-ghost text-start" href="/admin/categories"><i class="bi bi-tags me-2"></i>Catégories</a>
            <a class="btn btn-tt-ghost text-start" href="/"><i class="bi bi-box-seam me-2"></i>Frontoffice</a>
          </div>

          <hr class="tt-divider my-3">

          <div class="small text-muted-2">
            <div class="d-flex justify-content-between">
              <span>Connecté</span><span class="badge badge-soft-success">Admin</span>
            </div>
            <button class="btn btn-sm btn-tt-ghost w-100 mt-2" data-tt-action="logout">
              <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
            </button>
          </div>
        </aside>

        <section class="tt-surface p-3 p-md-4">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
              <h1 class="h4 mb-1">Dashboard</h1>
              <div class="text-muted-2">Vue globale (UI démo, chiffres fictifs).</div>
            </div>
            <button class="btn btn-tt-primary" data-tt-action="generate-report">
              <i class="bi bi-file-earmark-bar-graph me-1"></i>Générer rapport
            </button>
          </div>

          <hr class="tt-divider my-4">

          <div class="row g-3">
            <div class="col-md-6 col-xl-3">
              <div class="tt-card p-3">
                <div class="text-muted-2 small">Objets</div>
                <div class="d-flex align-items-end justify-content-between">
                  <div class="display-6 mb-0"><?= $nbObjet ?></div>
                  <!-- <span class="badge badge-soft-success">+12%</span> -->
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3">
              <div class="tt-card p-3">
                <div class="text-muted-2 small">Échanges</div>
                <div class="d-flex align-items-end justify-content-between">
                  <div class="display-6 mb-0"><?= $nbEchange ?></div>
                  <span class="badge badge-soft-info">actifs</span>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3">
              <div class="tt-card p-3">
                <div class="text-muted-2 small">Utilisateurs</div>
                <div class="d-flex align-items-end justify-content-between">
                  <div class="display-6 mb-0"><?= $nbUser ?></div>
                  <span class="badge badge-soft">total</span>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3">
              <div class="tt-card p-3">
                <div class="text-muted-2 small">Signalements</div>
                <div class="d-flex align-items-end justify-content-between">
                  <div class="display-6 mb-0">0</div>
                  <span class="badge badge-soft-warning">à traiter</span>
                </div>
              </div>
            </div>
          </div>

          <div class="tt-card p-3 mt-3">
            <div class="d-flex justify-content-between align-items-center">
              <div class="fw-semibold">Activité récente (en cours de production)</div>
              <button class="btn btn-sm btn-tt-ghost" data-tt-action="see-more">Voir plus</button>
            </div>
            <hr class="tt-divider my-3">
            <div class="d-flex flex-column gap-2">
              <div class="d-flex justify-content-between">
                <span class="text-muted-2">Nouvel objet ajouté</span>
                <span class="badge badge-soft">il y a 10 min</span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted-2">Échange accepté</span>
                <span class="badge badge-soft">il y a 2h</span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted-2">Catégorie modifiée</span>
                <span class="badge badge-soft">hier</span>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </main>

  <!-- components/footer.html -->
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
            <a href="../client/index.html" class="text-muted-2 small">Objets</a>
            <a href="../client/offers.html" class="text-muted-2 small">Échanges</a>
            <a href="/admin" class="text-muted-2 small">Admin</a>
            <a href="#" class="text-muted-2 small" onclick="toast('Lien CGU (démo)', 'info')">CGU</a>
          </div>
          <div class="text-muted-2 small mt-2">© <span id="year"></span> Takalo-takalo</div>
        </div>
      </div>
    </div>
  </footer>
  <script>
    document.getElementById('year') && (document.getElementById('year').textContent = new Date().getFullYear());
  </script>

  <div class="toast-container position-fixed bottom-0 end-0 p-3" id="ttToastContainer"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/app.js"></script>
</body>

</html>