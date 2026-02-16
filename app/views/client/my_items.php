<!doctype html>
<html lang="fr" data-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mes objets — Takalo-takalo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="../assets/css/theme.css" rel="stylesheet">
</head>

<body class="tt-page">

  <?php include('inc/header.php') ?>

  <main class="tt-main">
    <div class="container py-4">
      <div class="tt-hero p-3 p-md-4 mb-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
          <div>
            <h1 class="h4 mb-1">Mes objets</h1>
            <div class="text-muted-2">Ajoute, édite, supprime, gère tes photos.</div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-tt-primary" data-bs-toggle="modal" data-bs-target="#modalItemForm">
              <i class="bi bi-plus-circle me-1"></i>Ajouter un objet
            </button>
          </div>
        </div>
      </div>

      <div class="tt-surface p-3 p-md-4">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Objet</th>
                <th>Catégorie</th>
                <th>Valeur</th>
                <th>Date</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody id="objectsTableBody">
              <tr>
                <td colspan="5" class="text-center text-muted">
                  <span class="spinner-border spinner-border-sm me-2"></span>
                  Chargement…
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>

  <!-- Modal Add/Edit Object -->
  <div class="modal fade" id="modalItemForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content tt-card">
        <div class="modal-header border-0">
          <h5 class="modal-title">Ajouter / Éditer un objet</h5>
          <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form id="objectForm" class="modal-body needs-validation" novalidate>
          <input type="hidden" id="objectId" value="">

          <div class="row g-3">
            <div class="col-md-7">
              <label class="form-label">Titre</label>
              <input id="objectTitle" class="form-control" required placeholder="Ex: Clavier mécanique">
              <div class="invalid-feedback">Titre requis.</div>
            </div>

            <div class="col-md-5">
              <label class="form-label">Valeur estimée (Ar)</label>
              <input id="objectValue" type="number" class="form-control" required min="1" placeholder="Ex: 120000">
              <div class="invalid-feedback">Valeur invalide.</div>
            </div>

            <div class="col-md-12">
              <label class="form-label">Catégorie</label>
              <select id="objectCategory" class="form-select" required>
                <option value="">Choisir…</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Catégorie requise.</div>
            </div>

            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea id="objectDescription" class="form-control" rows="3" placeholder="Détails, état, accessoires…"></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Photos (maximum 8)</label>
              <input type="file" id="objectImages" class="form-control" multiple accept="image/*">
              <div class="form-text text-muted-2">Formats acceptés: JPG, PNG, GIF, WEBP. Taille max: 5MB par image.</div>
            </div>

            <div class="col-12">
              <div class="row g-2" id="previewGrid">
                <div class="text-muted-2 small">Aperçu des photos…</div>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-tt-primary w-100">Enregistrer</button>
            <button class="btn btn-tt-ghost" type="button" data-bs-dismiss="modal">Annuler</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Confirm Modal -->
  <div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content tt-card">
        <div class="modal-header border-0">
          <h5 class="modal-title">Confirmer la suppression</h5>
          <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="tt-empty text-center">
            <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="fw-semibold mt-2">Cette action est irréversible.</div>
            <div class="text-muted-2 small mt-1">L'objet et toutes ses photos seront supprimés définitivement.</div>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button class="btn btn-tt-ghost" data-bs-dismiss="modal">Annuler</button>
          <button id="confirmDeleteBtn" class="btn btn-danger">Supprimer</button>
        </div>
      </div>
    </div>
  </div>

  <?php include('inc/footer.php') ?>
  
  <div class="toast-container position-fixed bottom-0 end-0 p-3" id="ttToastContainer"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/app.js"></script>
  <script src="../assets/js/objects.js"></script>

</body>

</html>