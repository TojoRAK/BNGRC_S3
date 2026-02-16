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
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin — Catégories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/theme.css" rel="stylesheet">
</head>
<body class="tt-page">

    <nav class="navbar tt-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand text-white d-flex align-items-center gap-2" href="/admin/dash">
                <span class="badge badge-soft-info">TT</span>
                <span class="fw-semibold">Takalo-takalo</span>
                <span class="text-muted-2 small">Admin</span>
            </a>
            <div class="d-flex gap-2">
                <a class="btn btn-tt-ghost btn-sm" href="/">Frontoffice</a>
                <a class="btn btn-outline-light btn-sm" href="/admin/logout">Déconnexion</a>
            </div>
        </div>
    </nav>

    <main class="tt-main">
        <div class="container py-4">
            <div class="admin-shell">

                <aside class="tt-surface p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="fw-semibold">Admin</div>
                            <div class="text-muted-2 small">Takalo-takalo</div>
                        </div>
                        <span class="badge badge-soft">v1 UI</span>
                    </div>
                    <div class="d-grid gap-2">
                        <a class="btn btn-tt-ghost text-start" href="/admin/dash">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                        <a class="btn btn-tt-ghost text-start active" href="/admin/categories">
                            <i class="bi bi-tags me-2"></i>Catégories
                        </a>
                        <a class="btn btn-tt-ghost text-start" href="/">
                            <i class="bi bi-box-seam me-2"></i>Voir frontoffice
                        </a>
                    </div>
                    <hr class="border-opacity-25 my-3">
                    <div class="small text-muted-2">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Connecté</span>
                            <span class="badge badge-soft-success">Admin</span>
                        </div>
                        <a href="/admin/logout" class="btn btn-sm btn-outline-light w-100">
                            <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                        </a>
                    </div>
                </aside>

                <section class="tt-surface p-3 p-md-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h1 class="h4 mb-1">Catégories</h1>
                            <div class="text-muted-2 small">Gérez les catégories de l'application.</div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-tt-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalCategoryForm">
                                <i class="bi bi-plus-circle me-1"></i>Ajouter
                            </button>
                            <button class="btn btn-tt-ghost" data-tt-action="reorder">
                                <i class="bi bi-list-ol me-1"></i>Réordonner
                            </button>
                        </div>
                    </div>

                    <hr class="border-opacity-25 my-4">

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Slug</th>
                                    <th>Statut</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="categoriesTableBody">
                                <tr>
                                    <td colspan="4" class="text-center text-muted-2 py-3">
                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                        Chargement des catégories…
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
        </div>
    </main>

    <!-- Add / Edit modal -->
    <div class="modal fade" id="modalCategoryForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content tt-card">
                <div class="modal-header border-0">
                    <!-- id="modalCategoryTitle" lets JS update between Add / Edit -->
                    <h5 class="modal-title" id="modalCategoryTitle">Ajouter une catégorie</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="categoryForm" class="modal-body" novalidate>
                    <input type="hidden" id="categoryId" name="id">
                    <div class="mb-3">
                        <label class="form-label" for="categoryName">Nom</label>
                        <input id="categoryName" name="name" class="form-control" required
                               placeholder="Nom de la catégorie">
                    </div>
                    <button type="submit" class="btn btn-tt-primary w-100">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm delete modal -->
    <div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content tt-card">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="tt-empty">
                        <div class="icon"><i class="bi bi-exclamation-triangle text-danger"></i></div>
                        <div class="fw-semibold mt-2">Cette action est irréversible.</div>
                        <div class="text-muted-2 small mt-1">La catégorie sera définitivement supprimée.</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn btn-tt-ghost" data-bs-dismiss="modal">Annuler</button>
                    <button id="confirmDeleteBtn" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="ttToastContainer"></div>
    <script nonce="<?= htmlspecialchars($nonce) ?>" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script nonce="<?= htmlspecialchars($nonce) ?>" src="/assets/js/categories.js"></script>
</body>
</html>