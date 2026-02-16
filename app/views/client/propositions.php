<!doctype html>
<html lang="fr" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Échanges — Takalo-takalo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../assets/css/theme.css" rel="stylesheet">
</head>

<body class="tt-page">

    <!-- components/header_client.html
     Dynamic placeholders (Flight later):
     - {{user.name}} / {{user.avatarUrl}} / {{isLoggedIn}}
-->
    <?php include('inc/header.php') ?>

    <main class="tt-main">
        <div class="container py-4">
            <div class="tt-hero p-3 p-md-4 mb-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h1 class="h4 mb-1">Gestion des échanges</h1>
                        <div class="text-muted-2">Offres reçues et envoyées, statuts, actions.</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="historique" class="btn btn-tt-ghost" >Historique des échanges AT</a>
                        <button class="btn btn-tt-ghost" data-tt-action="refresh"><i
                                class="bi bi-arrow-repeat me-1"></i>Rafraîchir</button>
                    </div>
                </div>
            </div>

            <div class="tt-surface p-3 p-md-4">
                <ul class="nav nav-pills gap-2" id="offersTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active btn btn-tt-ghost" id="tab-received" data-bs-toggle="pill"
                            data-bs-target="#pane-received" type="button" role="tab">
                            <i class="bi bi-inbox me-1"></i>Reçues <span class="badge badge-soft ms-1">2</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link btn btn-tt-ghost" id="tab-sent" data-bs-toggle="pill"
                            data-bs-target="#pane-sent" type="button" role="tab">
                            <i class="bi bi-send me-1"></i>Envoyées <span class="badge badge-soft ms-1">1</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="pane-received" role="tabpanel">
                        <?php foreach ($propositions as $prop) {
                            if ($_SESSION['user']['id'] == $prop['owner_id']) { ?>
                                <div class="tt-card p-3 mb-3">
                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                                        <div class="d-flex gap-3">
                                            <img src="../assets/img/placeholder.jpg" width="90" height="70"
                                                style="object-fit:cover;border-radius:12px;border:1px solid var(--tt-border)"
                                                alt="">
                                            <div>
                                                <div class="fw-semibold">Demande pour : <?= $prop['wanted_title'] ?></div>
                                                <div class="text-muted-2 small">Proposé : <?= $prop['offered_title'] ?> • par
                                                    <span class="fw-semibold"
                                                        style="color:var(--tt-text)"><?= $prop['requester_name'] ?></span>
                                                </div>
                                                <div class="mt-2 d-flex gap-2 flex-wrap">
                                                    <span class="badge badge-soft-warning"><?= $prop['status'] ?></span>
                                                    <span class="badge badge-soft">Il y a 2h</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 align-self-md-center">
                                            <?php if ($prop['status'] == 'PENDING') { ?>
                                                <button class="btn btn-tt-primary" data-tt-action="accept"
                                                    data-proposition-id="<?= $prop['id'] ?>">Accepter</button>
                                                <button class="btn btn-outline-danger" data-tt-action="reject"
                                                    data-proposition-id="<?= $prop['id'] ?>">Refuser</button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div>

                    <div class="tab-pane fade" id="pane-sent" role="tabpanel">
                        <?php foreach ($propositions as $prop) {
                            if ($_SESSION['user']['id'] == $prop['requester_id']) { ?>
                                <div class="tt-card p-3 mb-3">
                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                                        <div class="d-flex gap-3">
                                            <img src="../assets/img/placeholder.jpg" width="90" height="70"
                                                style="object-fit:cover;border-radius:12px;border:1px solid var(--tt-border)"
                                                alt="">
                                            <div>
                                                <div class="fw-semibold">Offre envoyée <?= $prop['offered_title'] ?></div>
                                                <div class="text-muted-2 small">Pour : <?= $prop['wanted_title'] ?> • à <span
                                                        class="fw-semibold"
                                                        style="color:var(--tt-text)"><?= $prop['owner_name'] ?></span>

                                                </div>
                                                <div class="mt-2 d-flex gap-2 flex-wrap">
                                                    <span class="badge badge-soft-warning"><?= $prop['status'] ?></span>
                                                    <span class="badge badge-soft">Il y a 2h</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 align-self-md-center">
                                            <?php if ($prop['status'] == 'PENDING') { ?>
                                                <button class="btn btn-outline-danger" data-tt-action="cancel"
                                                    data-proposition-id="<?= $prop['id'] ?>">Annuler</button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                        <div class="tt-empty">
                            <div class="icon"><i class="bi bi-send-slash"></i></div>
                            <div class="fw-semibold mt-2">Astuce</div>
                            <div class="text-muted-2 small mt-1">Décommente l’empty state si tu veux simuler “aucune
                                offre envoyée”.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- components/footer.html -->
    <?php include('inc/footer.php') ?>
    <script>document.getElementById('year') && (document.getElementById('year').textContent = new Date().getFullYear());</script>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="ttToastContainer"></div>
    <!-- components/modals.html -->
    <div class="modal fade" id="modalQuickPost" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content tt-card">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Ajouter un objet (démo)</h5>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="modal-body needs-validation" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Titre</label>
                        <input class="form-control" required placeholder="Ex: Casque audio">
                        <div class="invalid-feedback">Titre requis.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valeur estimée</label>
                        <input type="number" class="form-control" required min="1" placeholder="Ex: 120000">
                        <div class="invalid-feedback">Valeur invalide.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catégorie</label>
                        <select class="form-select" required>
                            <option value="">Choisir…</option>
                            <option>Électronique</option>
                            <option>Maison</option>
                            <option>Mode</option>
                            <option>Loisirs</option>
                        </select>
                        <div class="invalid-feedback">Catégorie requise.</div>
                    </div>
                    <button class="btn btn-tt-primary w-100">Enregistrer (simulé)</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content tt-card">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="tt-empty">
                        <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
                        <div class="fw-semibold mt-2">Cette action est irréversible.</div>
                        <div class="text-muted-2 small mt-1">Suppression simulée (pas de backend).</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn btn-tt-ghost" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-danger" data-tt-action="delete-confirm">Supprimer</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/propositions.js"></script>
</body>

</html>