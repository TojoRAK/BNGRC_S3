<?php
/* inc/header.php — shared <head> + top navbar + layout wrapper
   Default theme: light. To enable dark mode quickly:
   - change data-bs-theme="light" to data-bs-theme="dark" below
*/
?>
<!doctype html>
<html lang="fr" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BNGRC — Dons & Distributions</title>

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- Theme overrides -->
  <link href="assets/css/theme.css" rel="stylesheet">
</head>

<body class="bngrc-page">
  <!-- Top header -->
  <nav class="navbar navbar-expand-lg bg-body border-bottom sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
        <span class="badge text-bg-primary rounded-pill"><i class="bi bi-heart-pulse"></i></span>
        <span class="fw-semibold">BNGRC</span>
        <span class="text-secondary d-none d-sm-inline">— Dons & Distributions</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#bngrcTopNav"
              aria-controls="bngrcTopNav" aria-expanded="false" aria-label="Menu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="bngrcTopNav">
        <form class="d-flex ms-lg-3 my-2 my-lg-0" role="search">
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input class="form-control" type="search" placeholder="Rechercher (ville, don, article…)" aria-label="Rechercher">
          </div>
        </form>

        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
          <li class="nav-item d-none d-lg-block">
            <span class="text-secondary small">
              <i class="bi bi-geo-alt"></i> Région: <strong>Atsinanana</strong>
            </span>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="rapports.php" data-bs-toggle="tooltip" title="Rapports & exports">
              <i class="bi bi-file-earmark-bar-graph"></i>
              <span class="d-lg-none ms-1">Rapports</span>
            </a>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
              <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary-subtle text-secondary"
                    style="width:32px;height:32px;">
                <i class="bi bi-person"></i>
              </span>
              <span class="d-none d-lg-inline">Admin</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Paramètres</a></li>
              <li><a class="dropdown-item" href="#"><i class="bi bi-person-badge me-2"></i>Profil</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="login.php"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- App layout -->
  <div class="bngrc-app d-flex">
