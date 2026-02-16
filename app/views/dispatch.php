<!doctype html>
<html lang="fr" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dispatch — BNGRC</title>

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- Theme -->
  <link href="assets/css/theme.css" rel="stylesheet">
</head>

<body class="bngrc-page">

  <!-- Topbar (statique, remplaçable plus tard par include header.php) -->
  <header class="bngrc-topbar border-bottom">
    <div class="container-fluid px-3 px-lg-4">
      <div class="d-flex align-items-center justify-content-between py-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bngrc-brand d-flex align-items-center gap-2">
            <span class="bngrc-logo">
              <i class="bi bi-box2-heart"></i>
            </span>
            <div class="lh-sm">
              <div class="fw-semibold">BNGRC</div>
              <small class="text-secondary">Collectes & Distributions</small>
            </div>
          </div>

          <span class="d-none d-md-inline text-secondary">/</span>
          <div class="d-none d-md-block">
            <div class="fw-semibold">Dispatch</div>
            <small class="text-secondary">Simulation & validation des attributions</small>
          </div>
        </div>

        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-question-circle me-1"></i> Aide
          </button>
          <button class="btn btn-outline-primary btn-sm">
            <i class="bi bi-person-circle me-1"></i> Admin
          </button>
        </div>
      </div>
    </div>
  </header>

  <main class="container-fluid px-3 px-lg-4 py-4">

    <!-- Hero / intro -->
    <div class="row g-3 align-items-stretch mb-3">
      <div class="col-12 col-xl-8">
        <div class="card bngrc-card h-100">
          <div class="card-body p-4">
            <div class="d-flex align-items-start justify-content-between gap-3">
              <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                  <span class="badge text-bg-primary-subtle bngrc-badge">
                    <i class="bi bi-diagram-3 me-1"></i> Module Dispatch
                  </span>
                  <span class="badge text-bg-light bngrc-badge border">
                    <i class="bi bi-clock-history me-1"></i> FIFO (date)
                  </span>
                </div>
                <h1 class="h4 mb-2">Simuler la répartition des dons</h1>
                <p class="text-secondary mb-0">
                  La simulation applique un ordre chronologique sur les dons (<strong>date_don</strong>),
                  puis attribue les quantités aux besoins par ordre de saisie (<strong>date_saisie</strong>).
                  Rien n’est enregistré tant que vous ne validez pas.
                </p>
              </div>

              <div class="d-flex flex-column gap-2">
                <button class="btn btn-primary">
                  <i class="bi bi-play-fill me-1"></i> Lancer la simulation
                </button>
                <button class="btn btn-outline-success">
                  <i class="bi bi-check2-circle me-1"></i> Valider le dispatch
                </button>
                <button class="btn btn-outline-danger">
                  <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser
                </button>
              </div>
            </div>

            <hr class="my-4">

            <!-- Filters (statique) -->
            <div class="row g-2">
              <div class="col-12 col-md-4">
                <label class="form-label text-secondary mb-1">Période</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                  <input type="text" class="form-control" value="16/02/2026 → 17/02/2026" disabled>
                </div>
              </div>

              <div class="col-12 col-md-4">
                <label class="form-label text-secondary mb-1">Type</label>
                <select class="form-select" disabled>
                  <option>Tout</option>
                  <option>Nature</option>
                  <option>Matériaux</option>
                  <option>Argent</option>
                </select>
              </div>

              <div class="col-12 col-md-4">
                <label class="form-label text-secondary mb-1">Ville (ciblage)</label>
                <select class="form-select" disabled>
                  <option>Toutes les villes</option>
                  <option>Antananarivo</option>
                  <option>Toamasina</option>
                  <option>Fianarantsoa</option>
                </select>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Summary -->
      <div class="col-12 col-xl-4">
        <div class="card bngrc-card h-100">
          <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="fw-semibold">Résumé de la simulation</div>
              <span class="badge text-bg-warning-subtle bngrc-badge">
                <i class="bi bi-eye me-1"></i> Aperçu
              </span>
            </div>

            <div class="row g-3">
              <div class="col-6">
                <div class="bngrc-metric">
                  <div class="text-secondary small">Dons disponibles</div>
                  <div class="fs-4 fw-semibold">12</div>
                  <div class="small text-secondary">dont 3 argent</div>
                </div>
              </div>
              <div class="col-6">
                <div class="bngrc-metric">
                  <div class="text-secondary small">Besoins ouverts</div>
                  <div class="fs-4 fw-semibold">18</div>
                  <div class="small text-secondary">sur 4 villes</div>
                </div>
              </div>

              <div class="col-6">
                <div class="bngrc-metric">
                  <div class="text-secondary small">Attributions</div>
                  <div class="fs-4 fw-semibold">27</div>
                  <div class="small text-secondary">lignes dispatch</div>
                </div>
              </div>
              <div class="col-6">
                <div class="bngrc-metric">
                  <div class="text-secondary small">Couverture</div>
                  <div class="fs-4 fw-semibold">68%</div>
                  <div class="small text-secondary">estimation</div>
                </div>
              </div>
            </div>

            <hr class="my-4">

            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="bi bi-info-circle text-primary"></i>
              <div class="small text-secondary">
                Les dons peuvent être attribués sur plusieurs villes. Un besoin peut être couvert par plusieurs dons.
              </div>
            </div>

            <div class="alert alert-light border mb-0">
              <div class="d-flex gap-2">
                <i class="bi bi-shield-lock"></i>
                <div class="small">
                  <div class="fw-semibold">Validation</div>
                  <div class="text-secondary">
                    (Plus tard) la validation sera réservée à un compte admin.
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Two panels: Dons / Besoins -->
    <div class="row g-3 mb-3">
      <!-- Dons queue -->
      <div class="col-12 col-lg-6">
        <div class="card bngrc-card">
          <div class="card-header bg-transparent border-bottom py-3">
            <div class="d-flex align-items-center justify-content-between">
              <div class="fw-semibold">
                <i class="bi bi-inbox me-1"></i> File des dons (ordre de traitement)
              </div>
              <span class="badge text-bg-secondary-subtle bngrc-badge">FIFO</span>
            </div>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="ps-3">Don</th>
                    <th>Article</th>
                    <th class="text-end">Quantité</th>
                    <th>Date</th>
                    <th class="text-end pe-3">État</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">DON-0012</div>
                      <div class="text-secondary small">Association Aina</div>
                    </td>
                    <td>
                      <span class="badge text-bg-success-subtle bngrc-badge me-2">Nature</span>
                      Riz (kg)
                    </td>
                    <td class="text-end fw-semibold">500</td>
                    <td class="text-secondary">16/02/2026 14:00</td>
                    <td class="text-end pe-3">
                      <span class="badge text-bg-warning-subtle bngrc-badge">
                        <i class="bi bi-hourglass-split me-1"></i> Non dispatché
                      </span>
                    </td>
                  </tr>

                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">DON-0013</div>
                      <div class="text-secondary small">Entreprise X</div>
                    </td>
                    <td>
                      <span class="badge text-bg-info-subtle bngrc-badge me-2">Argent</span>
                      Argent
                    </td>
                    <td class="text-end fw-semibold">1 000 000</td>
                    <td class="text-secondary">16/02/2026 15:10</td>
                    <td class="text-end pe-3">
                      <span class="badge text-bg-warning-subtle bngrc-badge">
                        <i class="bi bi-hourglass-split me-1"></i> Non dispatché
                      </span>
                    </td>
                  </tr>

                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">DON-0014</div>
                      <div class="text-secondary small">ONG Build</div>
                    </td>
                    <td>
                      <span class="badge text-bg-primary-subtle bngrc-badge me-2">Matériaux</span>
                      Tôle (unité)
                    </td>
                    <td class="text-end fw-semibold">40</td>
                    <td class="text-secondary">16/02/2026 15:00</td>
                    <td class="text-end pe-3">
                      <span class="badge text-bg-warning-subtle bngrc-badge">
                        <i class="bi bi-hourglass-split me-1"></i> Non dispatché
                      </span>
                    </td>
                  </tr>

                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">DON-0015</div>
                      <div class="text-secondary small">Donateur privé</div>
                    </td>
                    <td>
                      <span class="badge text-bg-success-subtle bngrc-badge me-2">Nature</span>
                      Huile (L)
                    </td>
                    <td class="text-end fw-semibold">120</td>
                    <td class="text-secondary">16/02/2026 16:20</td>
                    <td class="text-end pe-3">
                      <span class="badge text-bg-warning-subtle bngrc-badge">
                        <i class="bi bi-hourglass-split me-1"></i> Non dispatché
                      </span>
                    </td>
                  </tr>

                </tbody>
              </table>
            </div>
          </div>

          <div class="card-footer bg-transparent border-top py-3">
            <div class="d-flex align-items-center justify-content-between">
              <div class="text-secondary small">
                <i class="bi bi-lightning-charge me-1"></i> Conseil : grouper l’affichage par article améliore la lecture.
              </div>
              <button class="btn btn-outline-secondary btn-sm" disabled>
                <i class="bi bi-funnel me-1"></i> Filtrer
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Besoins queue -->
      <div class="col-12 col-lg-6">
        <div class="card bngrc-card">
          <div class="card-header bg-transparent border-bottom py-3">
            <div class="d-flex align-items-center justify-content-between">
              <div class="fw-semibold">
                <i class="bi bi-list-check me-1"></i> File des besoins (ordre de couverture)
              </div>
              <span class="badge text-bg-secondary-subtle bngrc-badge">date_saisie</span>
            </div>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="ps-3">Ville</th>
                    <th>Article</th>
                    <th class="text-end">Besoin</th>
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
                    <td>
                      <span class="badge text-bg-success-subtle bngrc-badge me-2">Nature</span>
                      Riz (kg)
                    </td>
                    <td class="text-end fw-semibold">300</td>
                    <td class="text-end">
                      <span class="badge text-bg-danger-subtle bngrc-badge">300</span>
                    </td>
                    <td class="text-secondary pe-3">16/02/2026 13:16</td>
                  </tr>

                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">Antananarivo</div>
                      <div class="text-secondary small">Analamanga</div>
                    </td>
                    <td>
                      <span class="badge text-bg-success-subtle bngrc-badge me-2">Nature</span>
                      Riz (kg)
                    </td>
                    <td class="text-end fw-semibold">800</td>
                    <td class="text-end">
                      <span class="badge text-bg-warning-subtle bngrc-badge">600</span>
                    </td>
                    <td class="text-secondary pe-3">16/02/2026 13:10</td>
                  </tr>

                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">Toamasina</div>
                      <div class="text-secondary small">Atsinanana</div>
                    </td>
                    <td>
                      <span class="badge text-bg-primary-subtle bngrc-badge me-2">Matériaux</span>
                      Tôle (unité)
                    </td>
                    <td class="text-end fw-semibold">60</td>
                    <td class="text-end">
                      <span class="badge text-bg-warning-subtle bngrc-badge">20</span>
                    </td>
                    <td class="text-secondary pe-3">16/02/2026 13:13</td>
                  </tr>

                  <tr>
                    <td class="ps-3">
                      <div class="fw-semibold">Antananarivo</div>
                      <div class="text-secondary small">Analamanga</div>
                    </td>
                    <td>
                      <span class="badge text-bg-info-subtle bngrc-badge me-2">Argent</span>
                      Argent
                    </td>
                    <td class="text-end fw-semibold">1 500 000</td>
                    <td class="text-end">
                      <span class="badge text-bg-danger-subtle bngrc-badge">1 500 000</span>
                    </td>
                    <td class="text-secondary pe-3">16/02/2026 13:12</td>
                  </tr>

                </tbody>
              </table>
            </div>
          </div>

          <div class="card-footer bg-transparent border-top py-3">
            <div class="d-flex align-items-center justify-content-between">
              <div class="text-secondary small">
                <i class="bi bi-exclamation-triangle me-1"></i> Les badges “Restant” illustrent la couverture après simulation.
              </div>
              <button class="btn btn-outline-secondary btn-sm" disabled>
                <i class="bi bi-search me-1"></i> Rechercher
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Result dispatch table -->
    <div class="card bngrc-card">
      <div class="card-header bg-transparent border-bottom py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div>
            <div class="fw-semibold">
              <i class="bi bi-table me-1"></i> Résultat de la simulation (aperçu)
            </div>
            <div class="text-secondary small">
              Chaque ligne représente une attribution : <strong>don → ville → quantité attribuée</strong>.
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm" disabled>
              <i class="bi bi-filetype-csv me-1"></i> Export CSV
            </button>
            <button class="btn btn-outline-secondary btn-sm" disabled>
              <i class="bi bi-printer me-1"></i> Imprimer
            </button>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Don</th>
                <th>Article</th>
                <th>Ville attribuée</th>
                <th class="text-end">Attribué</th>
                <th class="text-end">Reste don</th>
                <th class="pe-3">Note</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="ps-3">
                  <div class="fw-semibold">DON-0012</div>
                  <div class="text-secondary small">16/02/2026 14:00</div>
                </td>
                <td>
                  <span class="badge text-bg-success-subtle bngrc-badge me-2">Nature</span>
                  Riz (kg)
                </td>
                <td>
                  <div class="fw-semibold">Fianarantsoa</div>
                  <div class="text-secondary small">Besoin saisi plus tôt</div>
                </td>
                <td class="text-end fw-semibold">300</td>
                <td class="text-end">
                  <span class="badge text-bg-light border bngrc-badge">200</span>
                </td>
                <td class="text-secondary pe-3 small">
                  Couvre totalement le besoin “Riz (kg)” de Fianarantsoa.
                </td>
              </tr>

              <tr>
                <td class="ps-3">
                  <div class="fw-semibold">DON-0012</div>
                  <div class="text-secondary small">16/02/2026 14:00</div>
                </td>
                <td>
                  <span class="badge text-bg-success-subtle bngrc-badge me-2">Nature</span>
                  Riz (kg)
                </td>
                <td>
                  <div class="fw-semibold">Antananarivo</div>
                  <div class="text-secondary small">Besoin restant</div>
                </td>
                <td class="text-end fw-semibold">200</td>
                <td class="text-end">
                  <span class="badge text-bg-light border bngrc-badge">0</span>
                </td>
                <td class="text-secondary pe-3 small">
                  Affectation partielle au besoin “Riz (kg)”.
                </td>
              </tr>

              <tr>
                <td class="ps-3">
                  <div class="fw-semibold">DON-0014</div>
                  <div class="text-secondary small">16/02/2026 15:00</div>
                </td>
                <td>
                  <span class="badge text-bg-primary-subtle bngrc-badge me-2">Matériaux</span>
                  Tôle (unité)
                </td>
                <td>
                  <div class="fw-semibold">Toamasina</div>
                  <div class="text-secondary small">Priorité par saisie</div>
                </td>
                <td class="text-end fw-semibold">40</td>
                <td class="text-end">
                  <span class="badge text-bg-light border bngrc-badge">0</span>
                </td>
                <td class="text-secondary pe-3 small">
                  Reste besoin “Tôle” : 20 unités.
                </td>
              </tr>

              <tr>
                <td class="ps-3">
                  <div class="fw-semibold">DON-0013</div>
                  <div class="text-secondary small">16/02/2026 15:10</div>
                </td>
                <td>
                  <span class="badge text-bg-info-subtle bngrc-badge me-2">Argent</span>
                  Argent
                </td>
                <td>
                  <div class="fw-semibold">Antananarivo</div>
                  <div class="text-secondary small">Besoin argent</div>
                </td>
                <td class="text-end fw-semibold">1 000 000</td>
                <td class="text-end">
                  <span class="badge text-bg-light border bngrc-badge">0</span>
                </td>
                <td class="text-secondary pe-3 small">
                  Besoin restant “Argent” : 500 000 Ar.
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

      <div class="card-footer bg-transparent border-top py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="text-secondary small">
            <i class="bi bi-database-check me-1"></i> Aucune écriture BD tant que “Valider le dispatch” n’est pas confirmé.
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" disabled>
              <i class="bi bi-chevron-left"></i>
            </button>
            <button class="btn btn-outline-secondary btn-sm" disabled>
              <i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer (statique, remplaçable plus tard par include footer.php) -->
    <footer class="py-4">
      <div class="text-center text-secondary small">
        © BNGRC — Prototype UI | v0.1
      </div>
    </footer>

  </main>

  <!-- Bootstrap bundle (OK même sans JS custom) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
