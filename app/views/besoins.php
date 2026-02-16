<?php include('inc/header.php'); ?>
<?php include('inc/sidebar.php'); ?>

<main class="bngrc-content flex-grow-1">
    <div class="container-fluid py-4">

        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h4 mb-1">Besoins</h1>
                <div class="text-secondary small">Saisie et liste des besoins récents</div>
            </div>
        </div>

        <div class="card bngrc-card mb-3">
            <div class="card-header bngrc-cardhead">
                <div class="fw-semibold"><i class="bi bi-plus-circle me-2"></i>Saisie besoin</div>
            </div>
            <div class="card-body">
                <form method="POST" action="/besoins" class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Ville</label>
                        <select class="form-select" name="ville_id" required>
                            <?php foreach ($villes as $v): ?>
                                <option value="<?= (int) $v['id_ville'] ?>">
                                    <?= htmlspecialchars($v['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Article</label>
                        <select class="form-select" name="article_id" required>
                            <?php foreach ($articles as $a): ?>
                                <option value="<?= (int) $a['id_article'] ?>">
                                    <?= htmlspecialchars($a['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Quantité / Montant</label>
                        <input class="form-control" type="number" step="0.01" name="quantite" required>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle me-1"></i>Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card bngrc-card">
            <div class="card-header bngrc-cardhead">
                <div class="fw-semibold"><i class="bi bi-list-check me-2"></i>Besoins récents</div>
            </div>
            <div class="table-responsive">
                <table class="table bngrc-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Ville</th>
                            <th>Article</th>
                            <th class="text-end">Quantité</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($besoins as $b): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars((string) $b['ville']) ?></td>
                                <td><?= htmlspecialchars((string) $b['article']) ?></td>
                                <td class="text-end"><?= htmlspecialchars((string) $b['quantite']) ?></td>
                                <td class="text-secondary"><?= htmlspecialchars((string) $b['date_saisie']) ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($besoins)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">Aucun besoin enregistré.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<?php include('inc/footer.php'); ?>
