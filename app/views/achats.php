<?php include('inc/header.php'); ?>
<?php include('inc/sidebar.php'); ?>

<?php
function formatDeviseAr($montant)
{
    return number_format((int)$montant, 0, ',', ' ') . ' Ar';
}
?>

<main class="bngrc-content flex-grow-1">
    <div class="container-fluid py-4">

        <!-- Total Argent -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Total Argent Disponible</h5>
                        <span class="h5 mb-0 text-primary"><?= formatDeviseAr($totalArgent) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire Achat -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-body">
                        <h5 class="mb-0">Effectuer un Achat</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/achats" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Ville</label>
                                <select name="ville_id" class="form-select" required>
                                    <option value="">-- Sélectionner Ville --</option>
                                    <?php foreach ($villes as $v): ?>
                                        <option value="<?= $v['id_ville'] ?>"><?= htmlspecialchars($v['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Article</label>
                                <select name="article_id" class="form-select" required>
                                    <option value="">-- Sélectionner Article --</option>
                                    <?php foreach ($articles as $a): ?>
                                        <option value="<?= $a['id_article'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantité</label>
                                <input type="number" step="0.01" name="quantite" class="form-control" required>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Acheter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Besoins Restants -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-body">
                        <h5 class="mb-0">Besoins Restants par Ville</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ville</th>
                                    <th>Article</th>
                                    <th>PU</th>
                                    <th>Besoin Initial</th>
                                    <th>Quantité Déjà Achetée</th>
                                    <th>Restant</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($besoins as $b): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($b['ville']) ?></td>
                                        <td><?= htmlspecialchars($b['article']) ?></td>
                                        <td><?= number_format($b['pu'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= $b['besoin_initial'] ?></td>
                                        <td><?= $b['quantite_achetee'] ?></td>
                                        <td><?= $b['restant'] ?></td>
                                        <td>
                                            <form method="POST" action="/achats" class="d-flex">
                                                <input type="hidden" name="ville_id" value="<?= $b['id_ville'] ?>">
                                                <input type="hidden" name="article_id" value="<?= $b['id_article'] ?>">

                                                <input type="number"
                                                    name="quantite"
                                                    step="0.01"
                                                    max="<?= $b['restant'] ?>"
                                                    class="form-control form-control-sm me-2"
                                                    required>

                                                <button class="btn btn-sm btn-success">
                                                    Acheter
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des Achats -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-body">
                        <h5 class="mb-0">Historique des Achats</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Achat</th>
                                    <th>Ville</th>
                                    <th>Article</th>
                                    <th>PU</th>
                                    <th>Quantité</th>
                                    <th>Total HT</th>
                                    <th>Total TTC</th>
                                    <th>Taux Frais (%)</th>
                                    <th>Date Achat</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($achats as $a): ?>
                                    <tr>
                                        <td><?= $a['id_achat'] ?></td>
                                        <td><?= htmlspecialchars($a['ville']) ?></td>
                                        <td><?= htmlspecialchars($a['article']) ?></td>
                                        <td><?= number_format($a['pu'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= $a['quantite_achetee'] ?></td>
                                        <td><?= formatDeviseAr($a['total_ht']) ?></td>
                                        <td><?= formatDeviseAr($a['total_ttc']) ?></td>
                                        <td><?= $a['taux_frais'] ?></td>
                                        <td><?= $a['date_achat'] ?></td>
                                        <td>
                                            <?php if (!$a['deja_dispatche']): ?>
                                                <form method="POST" action="/dispatch/validateAchat">
                                                    <input type="hidden" name="id_achat" value="<?= $a['id_achat'] ?>">
                                                    <button class="btn btn-sm btn-warning">Dispatcher</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge bg-success">Dispatché</span>
                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include('inc/footer.php'); ?>