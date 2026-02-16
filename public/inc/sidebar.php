<?php /* inc/sidebar.php — left navigation */ ?>
<aside class="bngrc-sidebar border-end bg-body d-none d-lg-block">
  <div class="p-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div class="small text-secondary">Navigation</div>
      <span class="badge text-bg-light border"><i class="bi bi-shield-check"></i> ONG</span>
    </div>

    <div class="list-group list-group-flush">
      <a href="dashboard.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
        <i class="bi bi-speedometer2"></i> Tableau de bord
      </a>
      <a href="villes.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
        <i class="bi bi-building"></i> Villes
      </a>
      <a href="/besoins" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
        <i class="bi bi-clipboard2-check"></i> Besoins
      </a>
      <a href="/dons" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
        <i class="bi bi-box2-heart"></i> Dons
      </a>
      <a href="dispatch.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
        <i class="bi bi-diagram-3"></i> Dispatch (simulation)
      </a>
      <a href="rapports.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
        <i class="bi bi-file-earmark-bar-graph"></i> Rapports
      </a>
      <a href="settings.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
        <i class="bi bi-gear"></i> Paramètres
      </a>
    </div>

    <hr class="my-3">

    <div class="small text-secondary mb-2">Raccourcis</div>
    <div class="d-grid gap-2">
      <a class="btn btn-primary" href="dispatch.php"><i class="bi bi-play-fill me-1"></i>Simuler le dispatch</a>
      <a class="btn btn-outline-secondary" href="/besoins"><i class="bi bi-plus-circle me-1"></i>Saisir un besoin</a>
    </div>

    <div class="mt-3 small text-secondary">
      <i class="bi bi-info-circle"></i>
      Astuce : pour activer le thème sombre, changez <code>data-bs-theme</code> dans <code>inc/header.php</code>.
    </div>
  </div>
</aside>

<!-- Mobile sidebar via offcanvas -->
<div class="d-lg-none">
  <div class="offcanvas offcanvas-start" tabindex="-1" id="bngrcOffcanvas" aria-labelledby="bngrcOffcanvasLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="bngrcOffcanvasLabel"><i class="bi bi-list"></i> Navigation</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer"></button>
    </div>
    <div class="offcanvas-body">
      <div class="list-group">
        <a href="dashboard.php" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i>Tableau de bord</a>
        <a href="villes.php" class="list-group-item list-group-item-action"><i class="bi bi-building me-2"></i>Villes</a>
        <a href="besoins.php" class="list-group-item list-group-item-action"><i class="bi bi-clipboard2-check me-2"></i>Besoins</a>
        <a href="dons.php" class="list-group-item list-group-item-action"><i class="bi bi-box2-heart me-2"></i>Dons</a>
        <a href="dispatch.php" class="list-group-item list-group-item-action"><i class="bi bi-diagram-3 me-2"></i>Dispatch</a>
        <a href="rapports.php" class="list-group-item list-group-item-action"><i class="bi bi-file-earmark-bar-graph me-2"></i>Rapports</a>
        <a href="settings.php" class="list-group-item list-group-item-action"><i class="bi bi-gear me-2"></i>Paramètres</a>
      </div>
    </div>
  </div>

  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1040;">
    <button class="btn btn-primary shadow" type="button" data-bs-toggle="offcanvas" data-bs-target="#bngrcOffcanvas" aria-controls="bngrcOffcanvas">
      <i class="bi bi-list"></i>
    </button>
  </div>
</div>
