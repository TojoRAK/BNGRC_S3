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

        <!-- Page header -->
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h4 mb-1">Tableau de bord</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-sm mb-0">
                        <li class="breadcrumb-item"><a href="dashboard.php">Accueil</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tableau de bord</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <div class="card shadow-sm bngrc-stat">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-secondary small">Total des dons enregistrés</div>
                                <div class="h4 mb-0"><?= $dons ?></div>
                            </div>
                            <div class="fs-2 text-primary"><i class="bi bi-box2-heart"></i></div>
                        </div>
                        <div class="mt-2 small text-secondary">
                            <i class="bi bi-arrow-up-right"></i> +6% vs semaine précédente (fake)
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card shadow-sm bngrc-stat" style="border-left-color: var(--bngrc-accent);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-secondary small">Besoins totaux estimés</div>
                                <div class="h4 mb-0"><?= $besoins ?></div>
                            </div>
                            <div class="fs-2 text-success"><i class="bi bi-clipboard2-check"></i></div>
                        </div>
                        <div class="mt-2 small text-secondary">
                            Prix unitaires fixes — quantités variables
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card shadow-sm bngrc-stat" style="border-left-color:#fd7e14;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-secondary small">Reste à couvrir</div>
                                <div class="h4 mb-0"><?= $reste ?></div>
                            </div>
                            <div class="fs-2 text-warning"><i class="bi bi-exclamation-triangle"></i></div>
                        </div>
                        <div class="mt-2">
                            <span class="badge text-bg-warning">Priorité</span>
                            <span class="badge text-bg-light border">3 villes urgentes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-end">
                    <div class="me-auto">
                        <div class="fw-semibold mb-1">Filtres</div>
                        <div class="text-secondary small">Affiner par région, ville et type de besoin (fake UI)</div>
                    </div>
                    <form action="filtrer" method="get" class="w-100">
                        <div class="row g-2 w-100">
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-secondary">Région</label>
                                <select class="form-select" name="region">
                                    <option value="">Toutes</option>
                                    <?php foreach ($regions as $r) { ?>
                                        <option value="<?= (int) $r['id_region'] ?>" <?= (($filters['region'] ?? '') !== '' && (int) ($filters['region'] ?? 0) === (int) $r['id_region']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($r['name']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-secondary">Ville</label>
                                <select class="form-select" name="ville">
                                    <option value="">Toutes</option>
                                    <?php foreach ($villes as $v) { ?>
                                        <option value="<?= (int) $v['id_ville'] ?>" <?= (($filters['ville'] ?? '') !== '' && (int) ($filters['ville'] ?? 0) === (int) $v['id_ville']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($v['name']) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-secondary">Type de besoin</label>
                                <select class="form-select" name="besoin" disabled>
                                    <option selected>Tous</option>
                                    <option>Nature</option>
                                    <option>Matériaux</option>
                                    <option>Argent</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
                                <a href="./" class="btn btn-outline-secondary btn-sm">Réinitialiser</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-body d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <div class="fw-semibold"><i class="bi bi-building me-2"></i>Villes — besoins & dons attribués</div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-funnel"></i> Réinitialiser</button>
                    <a class="btn btn-primary btn-sm" href="dispatch.php"><i class="bi bi-play-fill"></i> Simuler
                        dispatch</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 bngrc-table">
                    <thead class="table-light">
                        <tr>
                            <th>Ville</th>
                            <th>Besoins (nature)</th>
                            <th>Besoins (matériaux)</th>
                            <th>Besoins (argent)</th>
                            <th>Dons attribués</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $detail) { ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= $detail['name'] ?></div>
                                    <div class="text-secondary small">Région <?= $detail['region'] ?></div>
                                </td>
                                <td class="fw-semibold"><?= formatDeviseAr($detail['besoins_nature_montant']) ?></td>
                                <td class="fw-semibold">
                                    <?= formatDeviseAr($detail['besoins_materiaux_montant']) ?>
                                </td>
                                <!-- <td><span class="badge text-bg-light border">—</span></td> -->
                                <td class="fw-semibold">
                                    <?= formatDeviseAr($detail['besoins_argent_montant']) ?>
                                </td>
                                <td style="min-width:160px;">
                                    <?= formatDeviseAr($detail['dons_attribues_montant'] != null ? $detail['dons_attribues_montant'] : 0) ?>

                                </td>
                                <td class="text-end">
                                    <a href="villes.php" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip"
                                        title="Voir la ville"><i class="bi bi-eye"></i></a>
                                    <a href="besoins.php" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip"
                                        title="Voir besoins"><i class="bi bi-clipboard2-check"></i></a>
                                </td>
                            </tr>
                        <?php } ?>


                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-body d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <div class="text-secondary small">
                    Dernière mise à jour : 16 fév 2026 (fake)
                </div>
                <a href="villes.php" class="btn btn-outline-secondary btn-sm">Voir toutes les villes</a>
            </div>
        </div>


    </div>
</main>

<?php include('inc/footer.php'); ?>