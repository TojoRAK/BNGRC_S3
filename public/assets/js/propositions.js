(() => {
  async function postJson(url, body = null) {
    const opts = {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    };

    if (body !== null) {
      opts.headers['Content-Type'] = 'application/json';
      opts.body = JSON.stringify(body);
    }

    const res = await fetch(url, opts);
    let data;
    try {
      data = await res.json();
    } catch {
      data = { ok: false, message: 'Réponse JSON invalide' };
    }

    if (!res.ok && data && typeof data.ok === 'undefined') {
      data.ok = false;
      data.message = data.message || `Erreur HTTP ${res.status}`;
    }

    return data;
  }

  function setButtonsDisabled(container, disabled) {
    container.querySelectorAll('button[data-tt-action="accept"], button[data-tt-action="reject"], button[data-tt-action="cancel"]').forEach((b) => {
      b.disabled = disabled;
    });
  }

  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('button[data-tt-action]');
    if (!btn) return;

    const action = btn.getAttribute('data-tt-action');
    if (action !== 'accept' && action !== 'reject' && action !== 'cancel') return;

    const propositionId = btn.getAttribute('data-proposition-id');
    if (!propositionId) {
      window.toast?.('ID de proposition manquant.', 'warning');
      return;
    }

    const card = btn.closest('.tt-card') || btn.parentElement;
    if (card) setButtonsDisabled(card, true);

    try {
      const endpoint = `/propositions/${encodeURIComponent(propositionId)}/${action}`;
      const result = await postJson(endpoint);

      if (result && result.ok) {
        const msg = action === 'accept'
          ? 'Proposition acceptée.'
          : (action === 'reject' ? 'Proposition refusée.' : 'Proposition annulée.');
        window.toast?.(msg, 'success');
        window.location.reload();
        return;
      }

      window.toast?.(result?.message || 'Action impossible.', 'danger');
    } catch (err) {
      window.toast?.(err?.message || 'Erreur réseau.', 'danger');
    } finally {
      if (card) setButtonsDisabled(card, false);
    }
  });
})();
