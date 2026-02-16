/* Takalo-takalo UI minimal JS (Bootstrap 5)
   - Theme toggle (light/dark) with localStorage
   - Bootstrap form validation helper
   - Toast helper
   - Multi-photo preview helper
*/
(() => {
  const root = document.documentElement;

  // --- Theme ---
  const THEME_KEY = "tt_theme";
  function setTheme(theme){
    root.setAttribute("data-theme", theme);
    localStorage.setItem(THEME_KEY, theme);
    updateThemeIcon(theme);
  }
  function getTheme(){
    const saved = localStorage.getItem(THEME_KEY);
    if (saved === "light" || saved === "dark") return saved;
    const prefersLight = window.matchMedia && window.matchMedia("(prefers-color-scheme: light)").matches;
    return prefersLight ? "light" : "dark";
  }
  function updateThemeIcon(theme){
    document.querySelectorAll("[data-tt-theme-toggle]").forEach(btn => {
      const icon = btn.querySelector("i");
      const label = btn.querySelector("[data-tt-theme-label]");
      if (icon) icon.className = theme === "light" ? "bi bi-moon-stars" : "bi bi-sun";
      if (label) label.textContent = theme === "light" ? "Sombre" : "Clair";
      btn.setAttribute("aria-label", theme === "light" ? "Activer le thème sombre" : "Activer le thème clair");
    });
  }
  function toggleTheme(){
    const current = root.getAttribute("data-theme") || "dark";
    setTheme(current === "dark" ? "light" : "dark");
    toast(`Thème : ${root.getAttribute("data-theme")}`, "info");
  }

  // init theme
  setTheme(getTheme());
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-tt-theme-toggle]");
    if (!btn) return;
    e.preventDefault();
    toggleTheme();
  });

  // --- Toast helper ---
  window.toast = (message, variant = "info") => {
    const container = document.getElementById('ttToastContainer');
    if (!container) return alert(message);

    const variants = {
      success: "text-bg-success",
      warning: "text-bg-warning",
      danger:  "text-bg-danger",
      info:    "text-bg-info",
      dark:    "text-bg-dark"
    };
    const klass = variants[variant] || variants.info;

    const el = document.createElement('div');
    el.className = `toast align-items-center ${klass} border-0`;
    el.role = "alert";
    el.ariaLive = "assertive";
    el.ariaAtomic = "true";
    el.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>`;
    container.appendChild(el);

    const t = new bootstrap.Toast(el, { delay: 3200 });
    t.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
  };

  // --- Bootstrap validation style ---
  document.querySelectorAll('form.needs-validation').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        toast("Veuillez corriger les erreurs du formulaire.", "warning");
      } else {
        event.preventDefault();
        toast("Action simulée (pas de backend).", "info");
      }
      form.classList.add('was-validated');
    }, false);
  });

  // --- Multi-photo preview ---
  document.querySelectorAll('[data-tt-multiphoto]').forEach((input) => {
    input.addEventListener('change', () => {
      const targetId = input.getAttribute('data-tt-multiphoto');
      const target = document.getElementById(targetId);
      if (!target) return;

      target.innerHTML = "";
      const files = Array.from(input.files || []);
      if (!files.length) {
        target.innerHTML = `<div class="text-muted-2 small">Aperçu des photos…</div>`;
        return;
      }

      files.slice(0, 8).forEach(file => {
        const url = URL.createObjectURL(file);
        const col = document.createElement('div');
        col.className = "col-6 col-md-3";
        col.innerHTML = `
          <div class="tt-card p-2 h-100">
            <img src="${url}" class="w-100" style="aspect-ratio:1/1;object-fit:cover;border-radius:12px;border:1px solid rgba(148,163,184,.16)" alt="preview">
            <div class="small text-muted-2 mt-2 text-truncate">${file.name}</div>
          </div>`;
        target.appendChild(col);
      });
    });
  });

  // Demo actions
  document.querySelectorAll('[data-tt-action]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const action = btn.getAttribute('data-tt-action');
      toast(`Action: ${action} (simulée)`, "info");
    });
  });
})();
