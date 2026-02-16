<?php
// Helpers
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function nf($n): string {
  if ($n === null) return '';
  $n = (float)$n;
  // format FR simple
  return number_format($n, 0, ',', ' ');
}

$coverage = (float)($stats['coverage_percent'] ?? 0);
$coverage = max(0, min(100, $coverage));
?>
<!doctype html>
<html lang="fr" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dispatch — BNGRC</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <link href="/assets/css/theme.css" rel="stylesheet">
</head>

<body class="bngrc-app">

  <div class="bngrc-shell">

    <!-- Sidebar -->
    <aside class="bngrc-sidebar">
      <div class="bngrc-sidehead">
        <a href="/" class="d-flex align-items-center gap-2 text-decoration-none">
          <span class="bngrc-logo"><i class="bi bi-box2-heart"></i></span>
          <span class="lh-sm">
            <span class="d-block fw-semibold">BNGRC</span>
            <span class="d-block text-secondary small">Collectes & Dispatch</span>
          </span>
        </a>
      </div>

      <div class="px-3 pb-3">
        <div class="bngrc-sidecard">
          <div class="small text-secondary mb-2">Règles</div>
          <div class="d-flex flex-wrap gap-2">
            <span class="badge bngrc-pill"><i class="bi bi-clock-history me-1"></i> Dons : FIFO (date)</span>
            <span class="badge bngrc-pill"><i class="bi bi-list-check me-1"></i> Besoins : FIFO (saisie)</span>
          </div>
        </div>
      </div>

      <nav class="bngrc-nav px-2">
        <a class="bngrc-navlink" href="/dashboard"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
        <a class="bngrc-navlink" href="/dons"><i class="bi bi-inbox"></i><span>Collectes</span></a>
        <a class="bngrc-navlink active" href="/dispatch"><i class="bi bi-diagram-3"></i><span>Dispatch</span></a>
        <a class="bngrc-navlink" href="/besoins"><i class="bi bi-truck"></i><span>Besoins</span></a>
        <a class="bngrc-navlink" href="/villes"><i class="bi bi-geo-alt"></i><span>Villes</span></a>

        <div class="bngrc-navsep"></div>

        <a class="bngrc-navlink" href="/settings"><i class="bi bi-gear"></i><span>Paramètres</span></a>
        <a class="bngrc-navlink" href="/help"><i class="bi bi-question-circle"></i><span>Aide</span></a>
      </nav>

      <div class="mt-auto p-3">
        <div class="bngrc-user">
          <div class="bngrc-avatar"><i class="bi bi-person"></i></div>
          <div class="lh-sm">
            <div class="fw-semibold">Admin</div>
            <div class="text-secondary small">Session active</div>
          </div>
          <button class="btn btn-sm btn-outline-secondary ms-auto" type="button" disabled>
            <i class="bi bi-box-arrow-right"></i>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <div class="bngrc-main">

      <!-- Topbar -->
      <header class="bngrc-topbar">
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
            <i class="bi bi-list"></i>
          </button>

          <div class="me-auto">
            <div class="fw-semibold">Simulation de dispatch</div>
            <div class="text-secondary small">
              Répartition automatique des dons vers les besoins (FIFO). Aucune écriture BD en simulation.
            </div>
          </div>

          <div class="d-flex gap-2">
            <form method="post" action="/dispatch/simulate" class="d-inline">
              <button class="btn btn-sm btn-primary" type="submit">
                <i class="bi bi-play-fill me-1"></i> Simuler
              </button>
            </form>

            <!-- Placeholder validation (à coder plus tard) -->
            <button class="btn btn-sm btn-success" type="button" disabled>
              <i class="bi bi-check2-circle me-1"></i> Valider
            </button>

            <form method="post" action="/dispatch/reset" class="d-inline">
              <button class="btn btn-sm btn-outline-danger" type="submit">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
              </button>
            </form>
          </div>
        </div>
      </header>

      <main class="bngrc-content">

        <?php if (empty($allocations)): ?>
          <div class="alert alert-light border d-flex align-items-start gap-2">
            <i class="bi bi-lightbulb"></i>
            <div>
              <div class="fw-semibold">Aucun résultat de simulation</div>
              <div class="text-secondary">Clique sur <strong>Simuler</strong> pour générer la répartition (FIFO).</div>
            </div>
          </div>
        <?php endif; ?>

        <!-- KPI row -->
        <div class="row g-3 mb-3">
          <div class="col-12 col-md-4">
            <div class="card bngrc-card">
              <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-secondary small">Dons disponibles</div>
                  <div class="fs-3 fw-semibold"><?= (int)($stats['nb_dons'] ?? count($dons)) ?></div>
                  <div class="text-secondary small">FIFO par date</div>
                </div>
                <div class="bngrc-kpiicon"><i class="bi bi-inbox"></i></div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <div class="card bngrc-card">
              <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                  <div class="text-secondary small">Besoins ouverts</div>
                  <div class="fs-3 fw-semibold"><?= (int)($stats['nb_besoins'] ?? count($besoins)) ?></div>
                  <div class="text-secondary small">FIFO par saisie</div>
                </div>
                <div class="bngrc-kpiicon"><i class="bi bi-clipboard2-check"></i></div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <div class="card bngrc-card">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <div class="text-secondary small">Couverture estimée</div>
                    <div class="fs-3 fw-semibold"><?= nf($coverage) ?>%</div>
                  </div>
                  <div class="bngrc-kpiicon"><i class="bi bi-graph-up-arrow"></i></div>
                </div>
                <div class="mt-3">
                  <div class="progress bngrc-progress" role="progressbar" aria-valuenow="<?= (int)$coverage ?>" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width: <?= (int)$coverage ?>%"></div>
                  </div>
                  <div class="d-flex justify-content-between text-secondary small mt-1">
                    <span>0%</span><span>100%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Two columns -->
        <div class="row g-3 mb-3" id="dispatchAccordion">

          <!-- Dons -->
          <div class="col-12 col-xl-6">
            <div class="card bngrc-card">
              <div class="card-header bngrc-cardhead">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="fw-semibold"><i class="bi bi-inbox me-1"></i> Dons à traiter</div>
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bngrc-pill">FIFO</span>
                    <button
                      class="btn btn-sm btn-outline-secondary collapsed dispatch-accordion-btn"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#dispatchDonsCollapse"
                      aria-expanded="false"
                      aria-controls="dispatchDonsCollapse"
                    >
                      <i class="bi bi-chevron-down me-1"></i>
                      <span data-dispatch-btn-label>Afficher</span>
                    </button>
                  </div>
                </div>
              </div>

              <div id="dispatchDonsCollapse" class="collapse" data-bs-parent="#dispatchAccordion" data-dispatch-collapse>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table bngrc-table align-middle mb-0">
                    <thead>
                      <tr>
                        <th class="ps-3">Don</th>
                        <th>Article</th>
                        <th class="text-end">Quantité</th>
                        <th>Date</th>
                        <th class="text-end pe-3">Statut</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($dons as $d): ?>
                        <tr>
                          <td class="ps-3">
                            <div class="fw-semibold">DON-<?= str_pad((string)$d['id_don'], 4, '0', STR_PAD_LEFT) ?></div>
                            <div class="text-secondary small"><?= e($d['source'] ?? '—') ?></div>
                          </td>
                          <td>
                            <span class="badge bngrc-tag bngrc-tag-nature me-2">Article</span>
                            <?= e($d['article_name'] ?? ('ID #' . (int)$d['id_article'])) ?>
                          </td>
                          <td class="text-end fw-semibold"><?= nf($d['quantite']) ?></td>
                          <td class="text-secondary"><?= e($d['date_don']) ?></td>
                          <td class="text-end pe-3">
                            <span class="badge bngrc-status bngrc-status-wait"><?= e($d['status'] ?? '—') ?></span>
                          </td>
                        </tr>
                      <?php endforeach; ?>

                      <?php if (empty($dons)): ?>
                        <tr>
                          <td colspan="5" class="text-center text-secondary py-4">Aucun don disponible.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="card-footer bngrc-cardfoot">
                <div class="text-secondary small">
                  <i class="bi bi-info-circle me-1"></i> Tri: <strong>date_don</strong> puis <strong>id_don</strong>.
                </div>
              </div>
              </div>
            </div>
          </div>

          <!-- Besoins -->
          <div class="col-12 col-xl-6">
            <div class="card bngrc-card">
              <div class="card-header bngrc-cardhead">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="fw-semibold"><i class="bi bi-list-check me-1"></i> Besoins à couvrir</div>
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bngrc-pill">FIFO</span>
                    <button
                      class="btn btn-sm btn-outline-secondary collapsed dispatch-accordion-btn"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#dispatchBesoinsCollapse"
                      aria-expanded="false"
                      aria-controls="dispatchBesoinsCollapse"
                    >
                      <i class="bi bi-chevron-down me-1"></i>
                      <span data-dispatch-btn-label>Afficher</span>
                    </button>
                  </div>
                </div>
              </div>

              <div id="dispatchBesoinsCollapse" class="collapse" data-bs-parent="#dispatchAccordion" data-dispatch-collapse>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table bngrc-table align-middle mb-0">
                    <thead>
                      <tr>
                        <th class="ps-3">Ville</th>
                        <th>Article</th>
                        <th class="text-end">Quantité</th>
                        <th class="pe-3">Saisie</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($besoins as $b): ?>
                        <tr>
                          <td class="ps-3">
                            <div class="fw-semibold"><?= e($b['ville_name'] ?? ('Ville #' . (int)$b['id_ville'])) ?></div>
                            <div class="text-secondary small">Besoin #<?= (int)$b['id_besoin'] ?></div>
                          </td>
                          <td>
                            <span class="badge bngrc-tag bngrc-tag-mat me-2">Article</span>
                            <?= e($b['article_name'] ?? ('ID #' . (int)$b['id_article'])) ?>
                          </td>
                          <td class="text-end fw-semibold"><?= nf($b['quantite']) ?></td>
                          <td class="text-secondary pe-3"><?= e($b['date_saisie']) ?></td>
                        </tr>
                      <?php endforeach; ?>

                      <?php if (empty($besoins)): ?>
                        <tr>
                          <td colspan="4" class="text-center text-secondary py-4">Aucun besoin ouvert.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="card-footer bngrc-cardfoot">
                <div class="text-secondary small">
                  <i class="bi bi-info-circle me-1"></i> Tri: <strong>date_saisie</strong> puis <strong>id_besoin</strong>.
                </div>
              </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Result -->
        <div class="card bngrc-card">
          <div class="card-header bngrc-cardhead">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
              <div>
                <div class="fw-semibold"><i class="bi bi-table me-1"></i> Résultat (simulation)</div>
                <div class="text-secondary small">Chaque ligne = attribution (don → besoin → ville → quantité).</div>
              </div>
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" type="button" disabled><i class="bi bi-filetype-csv me-1"></i> CSV</button>
                <button class="btn btn-sm btn-outline-secondary" type="button" disabled><i class="bi bi-printer me-1"></i> Print</button>
              </div>
            </div>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table bngrc-table align-middle mb-0">
                <thead>
                  <tr>
                    <th class="ps-3">Don</th>
                    <th>Besoin</th>
                    <th>Ville</th>
                    <th class="text-end">Attribué</th>
                    <th class="text-end">Reste don</th>
                    <th class="pe-3">Reste besoin</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($allocations as $a): ?>
                    <tr>
                      <td class="ps-3 fw-semibold">DON-<?= str_pad((string)$a['id_don'], 4, '0', STR_PAD_LEFT) ?></td>
                      <td class="fw-semibold">BES-<?= str_pad((string)$a['id_besoin'], 4, '0', STR_PAD_LEFT) ?></td>
                      <td><?= e($a['ville_name'] ?? ('Ville #' . (int)$a['id_ville'])) ?></td>
                      <td class="text-end fw-semibold"><?= nf($a['attribue']) ?></td>
                      <td class="text-end"><span class="badge bngrc-pill"><?= nf($a['reste_don']) ?></span></td>
                      <td class="pe-3"><span class="badge bngrc-pill"><?= nf($a['reste_besoin']) ?></span></td>
                    </tr>
                  <?php endforeach; ?>

                  <?php if (empty($allocations)): ?>
                    <tr>
                      <td colspan="6" class="text-center text-secondary py-4">
                        Lance la simulation pour voir les attributions.
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="card-footer bngrc-cardfoot">
            <div class="text-secondary small">
              <i class="bi bi-shield-lock me-1"></i> Simulation = aucune écriture BD. Validation à implémenter ensuite.
            </div>
          </div>
        </div>

        <footer class="py-4">
          <div class="text-center text-secondary small">© BNGRC — UI prototype</div>
        </footer>
      </main>
    </div>
  </div>

  <!-- Mobile sidebar (offcanvas) -->
  <div class="offcanvas offcanvas-start bngrc-offcanvas" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title d-flex align-items-center gap-2">
        <span class="bngrc-logo"><i class="bi bi-box2-heart"></i></span>
        <span>BNGRC</span>
      </h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer"></button>
    </div>
    <div class="offcanvas-body">
      <div class="d-grid gap-2 mb-3">
        <form method="post" action="/dispatch/simulate">
          <button class="btn btn-primary w-100" type="submit"><i class="bi bi-play-fill me-1"></i> Simuler</button>
        </form>
        <button class="btn btn-success w-100" type="button" disabled><i class="bi bi-check2-circle me-1"></i> Valider</button>
        <form method="post" action="/dispatch/reset">
          <button class="btn btn-outline-danger w-100" type="submit"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</button>
        </form>
      </div>

      <nav class="bngrc-nav">
        <a class="bngrc-navlink" href="/dashboard"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
        <a class="bngrc-navlink" href="/dons"><i class="bi bi-inbox"></i><span>Collectes</span></a>
        <a class="bngrc-navlink active" href="/dispatch"><i class="bi bi-diagram-3"></i><span>Dispatch</span></a>
        <a class="bngrc-navlink" href="/besoins"><i class="bi bi-truck"></i><span>Besoins</span></a>
        <a class="bngrc-navlink" href="/villes"><i class="bi bi-geo-alt"></i><span>Villes</span></a>
      </nav>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/dispatch.js"></script>
</body>
</html>
