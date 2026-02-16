<nav class="navbar navbar-expand-lg tt-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="../client/index.html">
                <span class="tt-brand-badge fw-bold">TT</span>
                <div class="d-flex flex-column lh-sm">
                    <span class="fw-semibold" style="color:var(--tt-text)">Takalo-takalo</span>
                    <span class="text-muted-2 small d-none d-md-inline">Plateforme d’échange d’objets</span>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
                <span class="navbar-toggler-icon" style="filter: invert(.85)"></span>
            </button>

            <div class="collapse navbar-collapse" id="topNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2 mt-3 mt-lg-0">
                    <li class="nav-item"><a class="nav-link tt-nav-link" href="/index">Objets</a></li>
                    <li class="nav-item"><a class="nav-link tt-nav-link" href="/my-objets">Mes objets</a>
                    </li>
                    <li class="nav-item"><a class="nav-link tt-nav-link" href="/propositions/list">Échanges</a></li>
                    <li class="nav-item"><a class="nav-link tt-nav-link" href="../client/profile.html">Profil</a></li>

                    <li class="nav-item d-flex align-items-center gap-2 ms-lg-2">
                        <button class="btn btn-tt-ghost btn-tt-icon" data-tt-theme-toggle title="Basculer thème">
                            <i class="bi bi-sun"></i>
                        </button>

                        <button class="btn btn-tt-primary btn-sm px-3" data-bs-toggle="modal"
                            data-bs-target="#modalQuickPost">
                            <i class="bi bi-plus-circle me-1"></i>Ajouter
                        </button>

                        <a class="btn btn-tt-ghost btn-sm" href="/auth/logout">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Connexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
