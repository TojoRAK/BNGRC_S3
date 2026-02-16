<!doctype html>
<html lang="fr" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Objets — Takalo-takalo</title>
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
                        <h1 class="h4 mb-1">Objets disponibles</h1>
                        <div class="text-muted-2">Découvre les objets des autres utilisateurs et propose un échange.
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <form action="" method="get" id="filterForm">
                            <div class="input-group" style="min-width: 260px;">
                                <span class="input-group-text bg-transparent"
                                    style="border-color:var(--tt-border); color:var(--tt-text)">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input class="form-control" placeholder="Rechercher un objet…" id="titre" name="title" value="<?= htmlspecialchars($_GET['title'] ?? '') ?>">
                            </div>
                            <select class="form-select" name="category_id" id="category_id">
                                <option value="">Toutes catégories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ((string)($cat['id']) === (string)($_GET['category_id'] ?? '')) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-tt-ghost" data-tt-action="filter">Filtrer</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tt-surface p-3 p-md-4">
                <?php if (empty($objets)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <div class="mt-2 text-muted">Aucun objet disponible pour le moment</div>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($objets as $objet): ?>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="tt-card p-3 h-100 d-flex flex-column">

                                    <!-- Image -->
                                    <div class="mb-3">
                                        <?php
                                        $imagePath = !empty($objet['main_image'])
                                            ? htmlspecialchars($objet['main_image'])
                                            : '../assets/img/placeholder.jpg';
                                        $imageCount = $objet['image_count'] ?? 0;
                                        ?>
                                        <div class="position-relative">
                                            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($objet['title']) ?>"
                                                class="w-100 rounded"
                                                style="aspect-ratio: 4/3; object-fit: cover; border: 1px solid var(--tt-border)">
                                            <?php if ($imageCount > 1): ?>
                                                <span class="badge badge-soft position-absolute top-0 end-0 m-2">
                                                    <i class="bi bi-images"></i> <?= $imageCount ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <div class="fw-semibold mb-1">
                                        <?= htmlspecialchars($objet['title']) ?>
                                    </div>

                                    <!-- Category -->
                                    <div class="text-muted-2 small mb-2">
                                        <i class="bi bi-tag"></i> <?= htmlspecialchars($objet['category_name']) ?>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-2 text-muted-2 small flex-grow-1">
                                        <?php
                                        $description = $objet['description'] ?? '';
                                        echo nl2br(htmlspecialchars(substr($description, 0, 80)));
                                        if (strlen($description) > 80)
                                            echo '...';
                                        ?>
                                    </div>

                                    <!-- Value -->
                                    <div class="mb-2">
                                        <span class="badge badge-soft">
                                            <?= number_format($objet['estimated_value'], 0, ',', ' ') ?> Ar
                                        </span>
                                    </div>

                                    <!-- Date -->
                                    <div class="text-muted-2 small mb-3">
                                        <i class="bi bi-calendar3"></i> <?= date('d/m/Y', strtotime($objet['created_at'])) ?>
                                    </div>

                                    <!-- Action button -->
                                    <div class="mt-auto">
                                        <a class="btn btn-tt-primary w-100" href="/objet/<?= $objet['id'] ?>">
                                            <i class="bi bi-eye me-1"></i>Voir les détails
                                        </a>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include('inc/footer.php') ?>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="ttToastContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
</body>

</html>