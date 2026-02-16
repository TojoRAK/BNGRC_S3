// objects.js - Gestion des objets avec upload multiple d'images
(() => {
    // Variables globales
    let objectsData = [];
    let selectedFiles = [];
    let modal = null;
    let deleteModal = null;
    let deleteId = null;
    const filterForm = document.getElementById("filterForm");


    /**
     * Helper pour les requêtes API (JSON)
     */
    async function apiFetch(url, { method = 'GET', body = null } = {}) {
        const opts = {
            method,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
        };
        if (body) opts.body = JSON.stringify(body);
        const res = await fetch(url, opts);
        return await res.json();
    }

    /**
     * Helper pour les requêtes avec fichiers (FormData)
     */
    async function apiUpload(url, formData, method = 'POST') {
        const res = await fetch(url, {
            method: method,
            body: formData
            // Ne pas mettre Content-Type, le navigateur le fait automatiquement avec boundary
        });
        return await res.json();
    }

    /**
     * Formater une date
     */
    function formatDate(dateString) {
        const d = new Date(dateString);
        return d.toLocaleDateString('fr-FR');
    }

    /**
     * Formater une valeur monétaire
     */
    function formatCurrency(value) {
        return new Intl.NumberFormat('fr-MG').format(value);
    }

    /**
     * Escape HTML
     */
    function htmlEscape(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    /**
     * Afficher un toast
     */
    function showToast(message, type = 'info') {
        if (typeof toast === 'function') {
            toast(message, type);
        } else {
            alert(message);
        }
    }

    /**
     * Charger les catégories
     */
    async function loadCategories() {
        const select = document.getElementById('objectCategory');
        if (!select) return;

        try {
            const res = await apiFetch('/admin/categories/list');

            if (!res.ok || !res.data.length) {
                select.innerHTML = `<option value="">Aucune catégorie</option>`;
                return;
            }

            // Garder l'option par défaut, supprimer les autres
            select.querySelectorAll('option:not([value=""])').forEach(o => o.remove());

            res.data.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat.id;
                opt.textContent = cat.name;
                select.appendChild(opt);
            });
        } catch (error) {
            console.error('Erreur chargement catégories:', error);
        }
    }

    /**
     * Charger les objets de l'utilisateur
     */
    async function filtrer() {
    const params = new URLSearchParams(new FormData(filterForm));
    
    try {
        const response = await fetch(`/objets/list/filter?${params.toString()}`);
        const res = await response.json();

        if (!res.ok || !res.data.length) {
            displayEmpty();
            return;
        }

        objectsData = res.data;
        displayObjects(res.data);

    } catch (error) {
        console.error('Erreur chargement objets:', error);
    }
}

    async function loadObjects() {
        const tbody = document.getElementById('objectsTableBody');
        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="5" class="text-center"><span class="spinner-border spinner-border-sm me-2"></span>Chargement…</td></tr>';

        try {
            const res = await apiFetch('/objets/list');

            if (!res.ok || !res.data.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            <div class="py-4">
                                <i class="bi bi-inbox fs-1"></i>
                                <div class="mt-2">Aucun objet pour le moment</div>
                                <div class="small text-muted-2">Clique sur "Ajouter un objet" pour commencer</div>
                            </div>
                        </td>
                    </tr>`;
                objectsData = [];
                return;
            }

            objectsData = res.data;
            displayObjects(res.data);
        } catch (error) {
            console.error('Erreur chargement objets:', error);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Erreur de chargement</td></tr>';
        }
    }

    /**
     * Afficher les objets dans le tableau
     */
    function displayObjects(objects) {
        const tbody = document.getElementById('objectsTableBody');
        tbody.innerHTML = '';

        objects.forEach(obj => {
            const imagePath = obj.main_image || '../assets/img/placeholder.jpg';
            const imageCount = obj.image_count || 0;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <img src="${imagePath}" alt="${htmlEscape(obj.title)}" 
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid var(--tt-border)">
                        <div>
                            <div class="fw-semibold">${htmlEscape(obj.title)}</div>
                            <div class="text-muted-2 small">
                                ${obj.description ? htmlEscape(obj.description.substring(0, 50)) + '...' : ''}
                            </div>
                            <div class="text-muted-2 small">
                                <i class="bi bi-images"></i> ${imageCount} photo${imageCount > 1 ? 's' : ''}
                            </div>
                        </div>
                    </div>
                </td>
                <td>${htmlEscape(obj.category_name)}</td>
                <td><span class="badge badge-soft">${formatCurrency(obj.estimated_value)} Ar</span></td>
                <td class="text-muted-2 small">${formatDate(obj.created_at)}</td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-tt-ghost btn-sm edit-btn" data-id="${obj.id}" title="Éditer">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-tt-ghost btn-sm text-danger delete-btn" data-id="${obj.id}" title="Supprimer">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>`;
            tbody.appendChild(tr);
        });
    }

    /**
     * Configuration de l'aperçu des images
     */
    function setupImagePreview() {
        const fileInput = document.getElementById('objectImages');
        const previewGrid = document.getElementById('previewGrid');

        if (!fileInput || !previewGrid) return;

        fileInput.addEventListener('change', function (e) {
            selectedFiles = Array.from(e.target.files);
            displayImagePreviews(selectedFiles, previewGrid);
        });
    }

    /**
     * Afficher les aperçus des images sélectionnées
     */
    function displayImagePreviews(files, container) {
        container.innerHTML = '';

        if (files.length === 0) {
            container.innerHTML = '<div class="text-muted-2 small">Aperçu des photos…</div>';
            return;
        }

        files.slice(0, 8).forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function (e) {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-3';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="w-100 rounded" 
                             style="aspect-ratio: 1; object-fit: cover; border: 1px solid var(--tt-border)">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 remove-preview-btn" 
                                data-index="${index}" style="padding: 2px 6px;">
                            <i class="bi bi-x"></i>
                        </button>
                        ${index === 0 ? '<span class="badge badge-soft-info position-absolute bottom-0 start-0 m-1">Principal</span>' : ''}
                    </div>
                `;
                container.appendChild(col);
            };

            reader.readAsDataURL(file);
        });
    }

    /**
     * Retirer une image de l'aperçu
     */
    function removePreviewImage(index) {
        selectedFiles.splice(index, 1);
        const fileInput = document.getElementById('objectImages');
        const previewGrid = document.getElementById('previewGrid');

        // Update file input
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;

        displayImagePreviews(selectedFiles, previewGrid);
    }

    /**
     * Réinitialiser le formulaire
     */
    function resetForm() {
        const form = document.getElementById('objectForm');
        if (!form) return;

        form.reset();
        form.classList.remove('was-validated');

        selectedFiles = [];
        document.getElementById('objectId').value = '';

        const fileInput = document.getElementById('objectImages');
        if (fileInput) {
            fileInput.value = '';
        }

        const previewGrid = document.getElementById('previewGrid');
        if (previewGrid) {
            previewGrid.innerHTML = '<div class="text-muted-2 small">Aperçu des photos…</div>';
        }

        const modalTitle = document.querySelector('#modalItemForm .modal-title');
        if (modalTitle) {
            modalTitle.textContent = 'Ajouter un objet';
        }
    }
    //
    /**
     * Éditer un objet
     */
    async function editObject(id) {
        const obj = objectsData.find(o => o.id == id);
        if (!obj) {
            console.error('Objet non trouvé dans objectsData:', id);
            return;
        }

        console.log('Édition de l\'objet:', obj);

        document.getElementById('objectId').value = obj.id;
        document.getElementById('objectTitle').value = obj.title || '';
        document.getElementById('objectDescription').value = obj.description || '';
        document.getElementById('objectValue').value = obj.estimated_value || '';
        document.getElementById('objectCategory').value = obj.category_id || '';

        // Reset image preview (edit mode doesn't show existing images, only new ones)
        selectedFiles = [];
        const previewGrid = document.getElementById('previewGrid');
        if (previewGrid) {
            previewGrid.innerHTML = '<div class="text-muted-2 small">Ajouter de nouvelles photos (optionnel)</div>';
        }

        const modalTitle = document.querySelector('#modalItemForm .modal-title');
        if (modalTitle) {
            modalTitle.textContent = 'Éditer un objet';
        }

        modal?.show();
    }

    /**
     * Sauvegarder un objet (créer ou modifier)
     */
    async function saveObject(e) {
        e.preventDefault();

        const form = document.getElementById('objectForm');
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        const id = document.getElementById('objectId').value;
        const formData = new FormData();

        // Ajouter les champs texte
        formData.append('title', document.getElementById('objectTitle').value);
        formData.append('description', document.getElementById('objectDescription').value);
        formData.append('estimated_value', document.getElementById('objectValue').value);
        formData.append('category_id', document.getElementById('objectCategory').value);

        // Ajouter les images (utiliser le bon format pour PHP)
        const fileInput = document.getElementById('objectImages');
        if (fileInput && fileInput.files.length > 0) {
            // Convertir FileList en tableau et ajouter chaque fichier
            Array.from(fileInput.files).forEach((file, index) => {
                formData.append(`images[${index}]`, file);
            });
            console.log('Nombre d\'images à uploader:', fileInput.files.length);
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enregistrement...';

        try {
            const url = id ? `/objets/${id}` : '/objets';
            const res = await apiUpload(url, formData);

            console.log('Réponse du serveur:', res);

            if (res.ok) {
                showToast(res.message || 'Objet enregistré avec succès', 'success');
                modal?.hide();
                resetForm();
                await loadObjects(); // Recharger la liste
            } else {
                showToast(res.message || 'Erreur lors de l\'enregistrement', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur lors de l\'enregistrement', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    /**
     * Confirmer la suppression
     */
    function confirmDelete(id) {
        deleteId = id;
        deleteModal?.show();
    }

    /**
     * Supprimer un objet
     */
    async function deleteObject() {
        if (!deleteId) return;

        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const originalText = confirmBtn.innerHTML;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Suppression...';

        try {
            const res = await apiFetch(`/objets/${deleteId}`, { method: 'DELETE' });

            if (res.ok) {
                showToast(res.message || 'Objet supprimé', 'success');
                deleteModal?.hide();
                await loadObjects();
            } else {
                showToast(res.message || 'Erreur lors de la suppression', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur lors de la suppression', 'error');
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalText;
            deleteId = null;
        }
    }
    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            filtrer();
        });
    }
    document.addEventListener('DOMContentLoaded', () => {
        // Initialiser les modals
        const modalEl = document.getElementById('modalItemForm');
        modal = modalEl ? new bootstrap.Modal(modalEl) : null;

        const deleteModalEl = document.getElementById('modalConfirmDelete');
        deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;

        // Charger les données
        loadCategories();
        if (!window.location.search.includes("?")) {
            loadObjects();

        }

        // Setup image preview
        setupImagePreview();

        // Reset form when modal opens (for "Add" mode)
        modalEl?.addEventListener('show.bs.modal', function (e) {
            // Si ce n'est pas un bouton edit qui a déclenché le modal
            if (!e.relatedTarget || !e.relatedTarget.classList.contains('edit-btn')) {
                resetForm();
            }
        });

        // Form submit handler
        const form = document.getElementById('objectForm');
        form?.addEventListener('submit', saveObject);

        // Delete confirm handler
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        confirmDeleteBtn?.addEventListener('click', deleteObject);

        // Event delegation for edit, delete and remove preview buttons
        document.addEventListener('click', e => {
            const editBtn = e.target.closest('.edit-btn');
            const deleteBtn = e.target.closest('.delete-btn');
            const removePreviewBtn = e.target.closest('.remove-preview-btn');

            if (editBtn) {
                const id = editBtn.dataset.id;
                editObject(id);
            }

            if (deleteBtn) {
                const id = deleteBtn.dataset.id;
                confirmDelete(id);
            }

            if (removePreviewBtn) {
                const index = parseInt(removePreviewBtn.dataset.index);
                removePreviewImage(index);
            }
        });
    });
})();