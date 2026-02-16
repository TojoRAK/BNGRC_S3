<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion — Takalo-takalo</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="/assets/css/theme.css" rel="stylesheet">
</head>
<body class="tt-page">

  <div class="container py-4">
    <nav class="navbar tt-navbar rounded-4 px-3">
      <a class="navbar-brand text-white d-flex align-items-center gap-2" href="../client/index.html">
        <span class="badge badge-soft-info">TT</span><span class="fw-semibold">Takalo-takalo</span>
      </a>
      <div class="d-flex gap-2">
        <a class="btn btn-tt-ghost btn-sm" href="auth/register">Créer un compte</a>
      </div>
    </nav>

    <main class="tt-main d-flex align-items-center justify-content-center py-5">
      <div class="tt-surface p-4 p-md-5 w-100" style="max-width: 520px;">
        <div class="mb-3">
          <h1 class="h4 mb-1">Connexion</h1>
          <div class="text-muted-2">Accède à tes échanges et propositions.</div>
        </div>
        <span id="formStatus"></span>

        <form class="needs-validation" id="loginForm" novalidate>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" required placeholder="ex: lucas@mail.com" id="email" name="email">
            <div class="invalid-feedback">Email invalide.</div>
          </div>

          <div class="mb-3">
            <label class="form-label d-flex justify-content-between">
              <span>Mot de passe</span>
              <a href="#" class="small text-muted-2" onclick="toast('Lien reset (simulé)', 'info')">Mot de passe oublié ?</a>
            </label>
            <div class="input-group">
              <input type="password" class="form-control" required minlength="6" placeholder="••••••••" id="password" name="password">
              <button class="btn btn-tt-ghost" type="button" onclick="togglePwd(this)">
                <i class="bi bi-eye"></i>
              </button>
              <div class="invalid-feedback">Minimum 6 caractères.</div>
            </div>
          </div>

          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember">
              <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>
            <span class="tt-chip"><i class="bi bi-shield-lock"></i> Sécurisé</span>
          </div>

          <input type="submit" class="btn btn-tt-primary w-100 py-2">Se connecter</button>

          <div class="text-center mt-3 text-muted-2 small">
            Pas de compte ? <a class="text-white" href="auth/register">Créer un compte</a>
          </div>
        </form>
      </div>
    </main>
  </div>

  <div class="toast-container position-fixed bottom-0 end-0 p-3" id="ttToastContainer"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/app.js"></script>
  <script src="/assets/js/login.js"></script>
  
  <script>
    function togglePwd(btn){
      const input = btn.parentElement.querySelector('input');
      input.type = input.type === 'password' ? 'text' : 'password';
      btn.innerHTML = input.type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    }
    
  </script>
</body>
</html>
