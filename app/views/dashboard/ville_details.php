<?php include('inc/header.php'); ?>
<?php include('inc/sidebar.php'); ?>
<?php
function formatDeviseAr($montant)
{
    return number_format((int) $montant, 0, ',', ' ') . ' Ar';
}
?>

<main class="bngrc-content flex-grow-1">
  <div class="container-fluid py-4">

    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
      <div>
        <h1 class="h4 mb-1">Détails des besoins — Ville <?= htmlspecialchars((string) ($ville['name'] ?? '')) ?></h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb breadcrumb-sm mb-0">
            <li class="breadcrumb-item"><a href="dashboard">Tableau de bord</a></li>
            <li class="breadcrumb-item active" aria-current="page">Détails ville</li>
          </ol>
        </nav>
      </div>
      <div>
        <a class="btn btn-outline-secondary btn-sm" href="dashboard"><i class="bi bi-arrow-left"></i> Retour</a>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-body d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="fw-semibold"><i class="bi bi-list-check me-2"></i>Besoins agrégés par article</div>
        <div class="text-secondary small">
          Total besoins: <span class="fw-semibold"><?php echo formatDeviseAr($totalMontant ?? 0); ?></span>
          <span class="mx-1">•</span>
          Total satisfaits: <span class="fw-semibold"><?php echo formatDeviseAr($totalSatisfait ?? 0); ?></span>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 bngrc-table">
          <thead class="table-light">
            <tr>
              <th>Type</th>
              <th>Article</th>
              <th class="text-end">PU</th>
              <th class="text-end">Quantité totale</th>
              <th class="text-end">Montant total</th>
              <th class="text-end">Qté satisfaite</th>
              <th class="text-end">Montant satisfait</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($details)) { ?>
              <tr>
                <td colspan="7" class="text-center text-secondary py-4">Aucun besoin trouvé pour cette ville.</td>
              </tr>
            <?php } ?>

            <?php foreach (($details ?? []) as $row) { ?>
              <tr>
                <td><span class="badge text-bg-light border"><?php echo htmlspecialchars((string) ($row['type_besoin'] ?? '')); ?></span></td>
                <td class="fw-semibold"><?php echo htmlspecialchars((string) ($row['article'] ?? '')); ?></td>
                <td class="text-end"><?php echo formatDeviseAr($row['pu'] ?? 0); ?></td>
                <td class="text-end"><?php echo number_format((float) ($row['quantite_total'] ?? 0), 2, ',', ' '); ?></td>
                <td class="text-end fw-semibold"><?php echo formatDeviseAr($row['montant_total'] ?? 0); ?></td>
                <td class="text-end"><?php echo number_format((float) ($row['quantite_satisfaite'] ?? 0), 2, ',', ' '); ?></td>
                <td class="text-end fw-semibold"><?php echo formatDeviseAr($row['montant_satisfait'] ?? 0); ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

      <div class="card-footer bg-body d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-secondary small">
          Ville: <?php echo htmlspecialchars((string) ($ville['name'] ?? '')); ?>
          <?php if (!empty($ville['region'])) { ?>
            <span class="mx-1">•</span>
            Région: <?php echo htmlspecialchars((string) ($ville['region'] ?? '')); ?>
          <?php } ?>
        </div>
        <a href="dashboard" class="btn btn-outline-secondary btn-sm">Retour tableau de bord</a>
      </div>
    </div>

  </div>
</main>

<?php include('inc/footer.php'); ?>
