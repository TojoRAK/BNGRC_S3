/* Takalo-takalo Admin — Categories JS (Bootstrap 5) */
(() => {

    window.toast = (message, variant = 'info') => {
        const toastContainer = document.getElementById('ttToastContainer');
        if (!toastContainer) return alert(message);
        const variants = { success: 'text-bg-success', warning: 'text-bg-warning', danger: 'text-bg-danger', info: 'text-bg-info' };
        const klass = variants[variant] || variants.info;
        const el = document.createElement('div');
        el.className = `toast align-items-center ${klass} border-0`;
        el.role = 'alert';
        el.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
        toastContainer.appendChild(el);
        const t = new bootstrap.Toast(el, { delay: 3200 });
        t.show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    };

    async function apiFetch(url, { method = 'GET', body = null } = {}) {
        const opts = { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' } };
        if (body !== null) opts.body = JSON.stringify(body);
        const res = await fetch(url, opts);
        const data = await res.json();
        return { ok: res.ok, status: res.status, data };
    }

    function escapeHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }

    async function loadCategories() {
        const tbody = document.getElementById('categoriesTableBody');
        if (!tbody) return;
        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted-2 py-3"><span class="spinner-border spinner-border-sm me-2"></span>Chargement…</td></tr>`;
        try {
            const { data } = await apiFetch('/admin/categories/list');
            if (!data.ok || !data.data || !data.data.length) {
                tbody.innerHTML = `<tr><td colspan="4" class="py-2"><div class="tt-empty"><div class="icon"><i class="bi bi-tags"></i></div><div class="fw-semibold mt-2">Aucune catégorie</div><div class="text-muted-2 small mt-1">Ajoutez votre première catégorie.</div></div></td></tr>`;
                return;
            }
            tbody.innerHTML = '';
            data.data.forEach(cat => {
                const slug = cat.slug ?? cat.name.toLowerCase().replace(/\s+/g, '-');
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="fw-semibold">${escapeHtml(cat.name)}</td>
                    <td><code class="text-muted-2">${escapeHtml(slug)}</code></td>
                    <td><span class="badge badge-soft-success">Actif</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-tt-ghost me-1 edit-category-btn" data-id="${cat.id}" data-name="${escapeHtml(cat.name)}">
                            <i class="bi bi-pencil me-1"></i>Éditer
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-category-btn" data-id="${cat.id}">
                            <i class="bi bi-trash me-1"></i>Supprimer
                        </button>
                    </td>`;
                tbody.appendChild(tr);
            });
        } catch (err) {
            console.error(err);
            toast('Erreur lors du chargement des catégories', 'danger');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {

        const categoryForm      = document.getElementById('categoryForm');
        const categoryIdInput   = document.getElementById('categoryId');
        const categoryNameInput = document.getElementById('categoryName');
        const modalTitle        = document.getElementById('modalCategoryTitle');
        const modalCategoryEl   = document.getElementById('modalCategoryForm');
        const modalCategory     = modalCategoryEl ? new bootstrap.Modal(modalCategoryEl) : null;

        
        if (modalCategoryEl) {
            modalCategoryEl.addEventListener('show.bs.modal', () => {
                if (!categoryIdInput.value) {
                    categoryForm.reset();
                    if (modalTitle) modalTitle.textContent = 'Ajouter une catégorie';
                }
            });
           
            modalCategoryEl.addEventListener('hidden.bs.modal', () => {
                categoryIdInput.value = '';
                categoryForm.reset();
            });
        }

        
        if (categoryForm) {
            categoryForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id   = categoryIdInput.value.trim();
                const name = categoryNameInput.value.trim();
                if (!name) { toast('Nom requis', 'warning'); return; }

                const submitBtn = categoryForm.querySelector('[type="submit"]');
                if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement…'; }

                try {
                    const url = id ? `/admin/categories/${id}` : '/admin/categories';
                    const { data } = await apiFetch(url, { method: 'POST', body: { name } });
                    if (data.ok) {
                        toast(data.message, 'success');
                        modalCategory?.hide();
                        loadCategories();
                    } else {
                        toast(data.errors?.name || data.message || 'Erreur', 'warning');
                    }
                } catch (err) {
                    console.error(err);
                    toast('Erreur serveur', 'danger');
                } finally {
                    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = 'Enregistrer'; }
                }
            });
        }

        
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.edit-category-btn');
            if (!btn) return;
            categoryIdInput.value   = btn.dataset.id;
            categoryNameInput.value = btn.dataset.name;
            if (modalTitle) modalTitle.textContent = 'Éditer la catégorie';
            modalCategory?.show();
        });

        
        const modalDeleteEl    = document.getElementById('modalConfirmDelete');
        const modalDelete      = modalDeleteEl ? new bootstrap.Modal(modalDeleteEl) : null;
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        let deleteId = null;

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.delete-category-btn');
            if (!btn) return;
            deleteId = btn.dataset.id;
            modalDelete?.show();
        });

        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', async () => {
                if (!deleteId) return;
                confirmDeleteBtn.disabled = true;
                confirmDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Suppression…';
                try {
                    const { data } = await apiFetch(`/admin/categories/${deleteId}`, { method: 'DELETE' });
                    if (data.ok) {
                        toast(data.message, 'success');
                        modalDelete?.hide();
                        loadCategories();
                    } else {
                        toast(data.message || 'Erreur', 'warning');
                    }
                } catch (err) {
                    console.error(err);
                    toast('Erreur serveur', 'danger');
                } finally {
                    confirmDeleteBtn.disabled = false;
                    confirmDeleteBtn.innerHTML = 'Supprimer';
                    deleteId = null;
                }
            });
        }

        loadCategories();
    });

})();