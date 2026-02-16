<!doctype html>
<html lang="fr" data-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($objet['title']) ?> — Takalo-takalo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="../assets/css/theme.css" rel="stylesheet">
</head>
<body class="tt-page">

  <?php include('inc/header.php') ?>

  <main class="tt-main">
    <div class="container py-4">
      <div class="row g-3">
        <div class="col-lg-7">
          <div class="tt-surface p-3 p-md-4">
            <div id="itemCarousel" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner rounded-4 overflow-hidden" style="border:1px solid var(--tt-border)">
                <?php if (!empty($images)): ?>
                  <?php foreach ($images as $index => $image): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                      <img src="<?= htmlspecialchars($image['path']) ?>" class="d-block w-100" style="aspect-ratio: 16/10; object-fit:cover" alt="<?= htmlspecialchars($objet['title']) ?>">
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="carousel-item active">
                    <img src="../assets/img/placeholder.jpg" class="d-block w-100" style="aspect-ratio: 16/10; object-fit:cover" alt="<?= htmlspecialchars($objet['title']) ?>">
                  </div>
                <?php endif; ?>
              </div>
              
              <?php if (count($images) > 1): ?>
              <!-- Indicators -->
              <div class="carousel-indicators">
                <?php foreach ($images as $index => $image): ?>
                  <button type="button" data-bs-target="#itemCarousel" data-bs-slide-to="<?= $index ?>" 
                          <?= $index === 0 ? 'class="active" aria-current="true"' : '' ?> 
                          aria-label="Slide <?= $index + 1 ?>"></button>
                <?php endforeach; ?>
              </div>
              
              <!-- Controls -->
              <button class="carousel-control-prev" type="button" data-bs-target="#itemCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Précédent</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#itemCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Suivant</span>
              </button>
              <?php endif; ?>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
              <span class="badge badge-soft"><?= htmlspecialchars($objet['category_name'] ?? 'Non catégorisé') ?></span>
              <span class="badge badge-soft-info">Bon état</span>
              <span class="badge badge-soft">Antananarivo</span>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="tt-surface p-3 p-md-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h1 class="h4 mb-1"><?= htmlspecialchars($objet['title']) ?></h1>
                <div class="text-muted-2">Publié par <a href="#" class="fw-semibold" style="color:var(--tt-text)">User_<?= $objet['owner_user_id'] ?></a></div>
              </div>
              <span class="badge badge-soft"><?= number_format($objet['estimated_value'], 0, ',', ' ') ?> Ar</span>
            </div>

            <hr class="tt-divider my-3">

            <div class="mb-3">
              <div class="fw-semibold mb-1">Description</div>
              <div class="text-muted-2"><?= nl2br(htmlspecialchars($objet['description'])) ?></div>
            </div>

            <div class="row g-2">
              <div class="col-6">
                <div class="tt-card p-3">
                  <div class="text-muted-2 small">Catégorie</div>
                  <div class="fw-semibold"><?= htmlspecialchars($objet['category_name'] ?? 'Non catégorisé') ?></div>
                </div>
              </div>
              <div class="col-6">
                <div class="tt-card p-3">
                  <div class="text-muted-2 small">Valeur</div>
                  <div class="fw-semibold"><?= number_format($objet['estimated_value'], 0, ',', ' ') ?> Ar</div>
                </div>
              </div>
            </div>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] !== $objet['owner_user_id']): ?>
            <div class="d-flex gap-2 mt-3">
              <button class="btn btn-tt-primary w-100 py-2" data-bs-toggle="modal" data-bs-target="#modalMakeOffer">
                <i class="bi bi-arrow-left-right me-1"></i>Proposer un échange
              </button>
              <button class="btn btn-tt-ghost btn-tt-icon" data-tt-action="share"><i class="bi bi-share"></i></button>
            </div>
            <?php else: ?>
            <div class="alert border-0 mt-3 mb-0" style="background: rgba(100,116,139,.12); color: var(--tt-text);">
              <i class="bi bi-info-circle me-1"></i> Ceci est votre objet.
            </div>
            <?php endif; ?>

            <div class="mt-3">
              <div class="text-muted-2 small">
                <i class="bi bi-calendar3 me-1"></i>
                Publié le <?= date('d/m/Y à H:i', strtotime($objet['created_at'])) ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal pour proposer un échange -->
  <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] !== $objet['owner_user_id']): ?>
  <div class="modal fade" id="modalMakeOffer" tabindex="-1" aria-hidden="true" data-objet-id="<?= $objet['id'] ?>">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content tt-card">
        <div class="modal-header border-0">
          <h5 class="modal-title">Proposer un échange</h5>
          <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form class="modal-body needs-validation" novalidate>
          <div class="row g-3">
            <div class="col-lg-6">
              <div class="tt-card p-3 h-100">
                <div class="fw-semibold">Objet demandé</div>
                <div class="text-muted-2 small"><?= htmlspecialchars($objet['title']) ?> • <?= number_format($objet['estimated_value'], 0, ',', ' ') ?> Ar</div>
                <img src="../assets/img/placeholder.jpg" class="w-100 mt-2" style="aspect-ratio: 16/10; object-fit:cover; border-radius:14px; border:1px solid var(--tt-border)" alt="">
              </div>
            </div>

            <div class="col-lg-6">
              <div class="tt-card p-3 h-100">
                <div class="fw-semibold">Choisir mon objet</div>
                <div class="text-muted-2 small mb-2">Sélectionne un de tes objets à proposer.</div>

                <select class="form-select" id="myObjectSelect" required>
                  <option value="">— Sélectionner —</option>
                  <!-- Ces options seront chargées dynamiquement via AJAX -->
                </select>
                <div class="invalid-feedback">Sélection requise.</div>

                <label class="form-label mt-3">Message (optionnel)</label>
                <textarea class="form-control" id="offerMessage" rows="3" placeholder="Proposition, détails, conditions…"></textarea>

                <div class="d-flex gap-2 mt-3">
                  <button class="btn btn-tt-primary w-100" type="submit" >Envoyer</button>
                  <button class="btn btn-tt-ghost" type="button" data-bs-dismiss="modal">Annuler</button>
                </div>
              </div>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php include('inc/footer.php') ?>
  
  <div class="toast-container position-fixed bottom-0 end-0 p-3" id="ttToastContainer"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/app.js"></script>
  <script src="../assets/js/detail.js"></script>
</body>
</html>