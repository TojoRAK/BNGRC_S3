<!doctype html>
<html lang="fr" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dispatch — BNGRC</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <link href="assets/css/theme.css" rel="stylesheet">
</head>

<body class="bngrc-app">

  <!-- Topbar -->
  <header class="bngrc-topbar">
    <div class="container-fluid px-3 px-lg-4">
      <div class="d-flex align-items-center justify-content-between py-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bngrc-mark">
            <i class="bi bi-box2-heart"></i>
          </div>
          <div class="lh-sm">
            <div class="fw-semibold">BNGRC</div>
            <div class="text-secondary small">Dispatch des dons</div>
          </div>

          <span class="d-none d-md-inline text-secondary">•</span>

          <div class="d-none d-md-flex align-items-center gap-2">
            <span class="badge bngrc-pill">
              <i class="bi bi-clock-history me-1"></i> Dons : FIFO (date)
            </span>
            <span class="badge bngrc-pill">
              <i class="bi bi-list-check me-1"></i> Besoins : FIFO (saisie)
            </span>
          </div>
        </div>

        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-question-circle me-1"></i> Aide
          </button>
          <button class="btn btn-sm btn-outline-primary">
            <i class="bi bi-person-circle me-1"></i> Admin
          </button>
        </div>
      </div>
    </div>
  </header>

  <main class="container-fluid px-3 px-lg-4 py-4">

    <!-- Title + actions -->
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
      <div>
        <h1 class="h4 mb-1">Simulation de dispatch</h1>
        <div class="text-secondary">
          Aperçu de répartition automatique des dons vers les besoins par ville (données statiques).
        </div>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-primary">
          <i class="bi bi-play-fill me-1"></i> Simuler
        </button>
        <button class="btn btn-success">
          <i class="bi bi-check2-circle me-1"></i> Valider
        </button>
        <button class="btn btn-outline-danger">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
        </button>
      </div>
    </div>

    <!-- KPI row -->
    <div class="row g-3 mb-3">
      <div class="col-12 col-md-4">
        <div class="card bngrc-card">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="text-secondary small">Dons disponibles</div>
              <div class="fs-3 fw-semibold">4</div>
              <div class="text-secondary small">Nature + Matériaux + Argent</div>
            </div>
            <div class="bngrc-icon">
              <i class="bi bi-inbox"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="card bngrc-card">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="text-secondary small">Besoins ouverts</div>
              <div class="fs-3 fw-semibold">7</div>
              <div class="text-secondary small">Sur 3 villes</div>
            </div>
            <div class="bngrc-icon">
              <i class="bi bi-clipboard2-check"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="card bngrc-card">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-secondary small">Couverture estimée</div>
                <div class="fs-3 fw-semibold">68%</div>
              </div>
              <div class="bngrc-icon">
                <i class="bi bi-graph-up-arrow"></i>
              </div>
            </div>
            <div class="mt-3">
              <div class="progress bngrc-progress" role="progressbar" aria-label="Couverture" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: 68%"></div>
              </div>
              <div class="d-flex justify-content-between text-secondary small mt-1">
                <span>0%</span><span>100%</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters (static) -->
    <div class="card bngrc-card mb-3">
      <div class="card-body">
        <div class="row g-2 align-items-end">
          <div class="col-12 col-lg-4">
            <label class="form-label text-secondary small mb-1">Période</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
              <input class="form-control" value="16/02/2026 → 17/02/2026" disabled>
            </div>
          </div>
          <div class="col-12 col-lg-3">
            <label class="form-label text-secondary small mb-1">Type</label>
            <select class="form-select" disabled>
              <option>Tout</option>
              <option>Nature</option>
              <option>Matériaux</option>
              <option>Argent</option>
            </select>
          </div>
          <div class="col-12 col-lg-3">
            <label class="form-label text-secondary small mb-1">Ville</label>
            <select class="form-select" disabled>
              <option>Toutes</option>
              <option>Antananarivo</option>
              <option>Toamasina</option>
              <option>Fianarantsoa</option>
            </select>
          </div>
          <div class="col-12 col-lg-2 d-grid">
            <button class="btn btn-outline-secondary" disabled>
              <i class="bi bi-funnel me-1"></i> Appliquer
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Two columns -->
    <div class="row g-3 mb-3">
      <!-- Dons -->
      <div class="col-12 col-xl-6">
        <div class="card bngrc-card">
          <div class="card-header bngrc-cardhead">
            <div class="d-flex align-items-center justify-content-between">
              <div class="fw-semibold">
                <i class="bi bi-inbox me-1"></i> Dons à traiter
              </div>
              <span class="badge bngrc-pill">FIFO</span>
            </div>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive bngrc-tablewrap">
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
                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">DON-0012</div>
                      <div class="text-secondary small">Association Aina</div>
                    </td>
                    <td><span class="badge bngrc-tag bngrc-tag-nature me-2">Nature</span> Riz (kg)</td>
                    <td class="text-end fw-semibold">500</td>
                    <td class="text-secondary">16/02 14:00</td>
                    <td class="text-end pe-3"><span class="badge bngrc-status bngrc-status-wait">Non dispatché</span></td>
                  </tr>

                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">DON-0014</div>
                      <div class="text-secondary small">ONG Build</div>
                    </td>
                    <td><span class="badge bngrc-tag bngrc-tag-mat me-2">Matériaux</span> Tôle (unité)</td>
                    <td class="text-end fw-semibold">40</td>
                    <td class="text-secondary">16/02 15:00</td>
                    <td class="text-end pe-3"><span class="badge bngrc-status bngrc-status-wait">Non dispatché</span></td>
                  </tr>

                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">DON-0013</div>
                      <div class="text-secondary small">Entreprise X</div>
                    </td>
                    <td><span class="badge bngrc-tag bngrc-tag-cash me-2">Argent</span> Argent</td>
                    <td class="text-end fw-semibold">1 000 000</td>
                    <td class="text-secondary">16/02 15:10</td>
                    <td class="text-end pe-3"><span class="badge bngrc-status bngrc-status-wait">Non dispatché</span></td>
                  </tr>

                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">DON-0015</div>
                      <div class="text-secondary small">Donateur privé</div>
                    </td>
                    <td><span class="badge bngrc-tag bngrc-tag-nature me-2">Nature</span> Huile (L)</td>
                    <td class="text-end fw-semibold">120</td>
                    <td class="text-secondary">16/02 16:20</td>
                    <td class="text-end pe-3"><span class="badge bngrc-status bngrc-status-wait">Non dispatché</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="card-footer bngrc-cardfoot">
            <div class="text-secondary small">
              <i class="bi bi-info-circle me-1"></i> Ordonnés par <strong>date_don</strong> (puis id).
            </div>
          </div>
        </div>
      </div>

      <!-- Besoins -->
      <div class="col-12 col-xl-6">
        <div class="card bngrc-card">
          <div class="card-header bngrc-cardhead">
            <div class="d-flex align-items-center justify-content-between">
              <div class="fw-semibold">
                <i class="bi bi-list-check me-1"></i> Besoins à couvrir
              </div>
              <span class="badge bngrc-pill">FIFO</span>
            </div>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive bngrc-tablewrap">
              <table class="table bngrc-table align-middle mb-0">
                <thead>
                  <tr>
                    <th class="ps-3">Ville</th>
                    <th>Article</th>
                    <th class="text-end">Restant</th>
                    <th class="pe-3">Saisie</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">Fianarantsoa</div>
                      <div class="text-secondary small">Haute Matsiatra</div>
                    </td>
                    <td><span class="badge bngrc-tag bngrc-tag-nature me-2">Nature</span> Riz (kg)</td>
                    <td class="text-end fw-semibold">
                      <span class="badge bngrc-need bngrc-need-high">300</span>
                    </td>
                    <td class="text-secondary pe-3">16/02 13:16</td>
                  </tr>

                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">Antananarivo</div>
                      <div class="text-secondary small">Analamanga</div>
                    </td>
                    <td><span class="badge bngrc-tag bngrc-tag-nature me-2">Nature</span> Riz (kg)</td>
                    <td class="text-end fw-semibold">
                      <span class="badge bngrc-need bngrc-need-mid">600</span>
                    </td>
                    <td class="text-secondary pe-3">16/02 13:10</td>
                  </tr>

                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">Toamasina</div>
                      <div class="text-secondary small">Atsinanana</div>
                    </td>
                    <td><span class="badge bngrc-tag bngrc-tag-mat me-2">Matériaux</span> Tôle (unité)</td>
                    <td class="text-end fw-semibold">
                      <span class="badge bngrc-need bngrc-need-mid">20</span>
                    </td>
                    <td class="text-secondary pe-3">16/02 13:13</td>
                  </tr>

                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">Antananarivo</div>
                      <div class="text-secondary small">Analamanga</div>
                    </td>
                    <td><span class="badge bngrc-tag bngrc-tag-cash me-2">Argent</span> Argent</td>
                    <td class="text-end fw-semibold">
                      <span class="badge bngrc-need bngrc-need-high">1 500 000</span>
                    </td>
                    <td class="text-secondary pe-3">16/02 13:12</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="card-footer bngrc-cardfoot">
            <div class="text-secondary small">
              <i class="bi bi-info-circle me-1"></i> Ordonnés par <strong>date_saisie</strong> (puis id).
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
            <div class="fw-semibold"><i class="bi bi-table me-1"></i> Résultat (aperçu)</div>
            <div class="text-secondary small">Chaque ligne = une attribution (don → ville → quantité).</div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-filetype-csv me-1"></i> CSV</button>
            <button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-printer me-1"></i> Print</button>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive bngrc-tablewrap">
          <table class="table bngrc-table align-middle mb-0">
            <thead>
              <tr>
                <th class="ps-3">Don</th>
                <th>Article</th>
                <th>Ville</th>
                <th class="text-end">Attribué</th>
                <th class="text-end">Reste don</th>
                <th class="pe-3">Commentaire</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="ps-3">
                  <div class="fw-semibold">DON-0012</div>
                  <div class="text-secondary small">16/02 14:00</div>
                </td>
                <td><span class="badge bngrc-tag bngrc-tag-nature me-2">Nature</span> Riz (kg)</td>
                <td class="fw-semibold">Fianarantsoa</td>
                <td class="text-end fw-semibold">300</td>
                <td class="text-end"><span class="badge bngrc-pill">200</span></td>
                <td class="text-secondary pe-3 small">Besoin le plus ancien sur “Riz”.</td>
              </tr>
              <tr>
                <td class="ps-3">
                  <div class="fw-semibold">DON-0012</div>
                  <div class="text-secondary small">16/02 14:00</div>
                </td>
                <td><span class="badge bngrc-tag bngrc-tag-nature me-2">Nature</span> Riz (kg)</td>
                <td class="fw-semibold">Antananarivo</td>
                <td class="text-end fw-semibold">200</td>
                <td class="text-end"><span class="badge bngrc-pill">0</span></td>
                <td class="text-secondary pe-3 small">Affectation partielle.</td>
              </tr>
              <tr>
                <td class="ps-3">
                  <div class="fw-semibold">DON-0014</div>
                  <div class="text-secondary small">16/02 15:00</div>
                </td>
                <td><span class="badge bngrc-tag bngrc-tag-mat me-2">Matériaux</span> Tôle (unité)</td>
                <td class="fw-semibold">Toamasina</td>
                <td class="text-end fw-semibold">40</td>
                <td class="text-end"><span class="badge bngrc-pill">0</span></td>
                <td class="text-secondary pe-3 small">Reste besoin tôle : 20.</td>
              </tr>
              <tr>
                <td class="ps-3">
                  <div class="fw-semibold">DON-0013</div>
                  <div class="text-secondary small">16/02 15:10</div>
                </td>
                <td><span class="badge bngrc-tag bngrc-tag-cash me-2">Argent</span> Argent</td>
                <td class="fw-semibold">Antananarivo</td>
                <td class="text-end fw-semibold">1 000 000</td>
                <td class="text-end"><span class="badge bngrc-pill">0</span></td>
                <td class="text-secondary pe-3 small">Besoin argent restant : 500 000.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card-footer bngrc-cardfoot">
        <div class="text-secondary small">
          <i class="bi bi-shield-lock me-1"></i> Plus tard : validation réservée admin (session).
        </div>
      </div>
    </div>

    <footer class="py-4">
      <div class="text-center text-secondary small">© BNGRC — UI prototype</div>
    </footer>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
