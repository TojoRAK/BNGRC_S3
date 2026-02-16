

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalMakeOffer');
    
    if (!modal) {
        return; // Modal n'existe pas sur cette page
    }
    
    const select = document.getElementById('myObjectSelect');
    const form = modal.querySelector('form');
    const messageTextarea = document.getElementById('offerMessage');
    
    // Charger les objets de l'utilisateur quand le modal s'ouvre
    modal.addEventListener('show.bs.modal', function() {
        loadUserObjects();
    });
    
    // Gérer la soumission du formulaire
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }
            
            submitOffer();
        });
    }
    

    function loadUserObjects() {
        if (!select) return;
        
        // Afficher un état de chargement
        select.innerHTML = '<option value="">Chargement...</option>';
        select.disabled = true;
        
        fetch('/objets/list')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                select.disabled = false;
                
                if (data.ok && data.data && data.data.length > 0) {
                    select.innerHTML = '<option value="">— Sélectionner —</option>';
                    
                    data.data.forEach(obj => {
                        const option = document.createElement('option');
                        option.value = obj.id;
                        const formattedValue = new Intl.NumberFormat('fr-MG').format(obj.estimated_value);
                        option.textContent = `${obj.title} (${formattedValue} Ar)`;
                        select.appendChild(option);
                    });
                } else {
                    select.innerHTML = '<option value="">Aucun objet disponible</option>';
                    showToast('Vous devez d\'abord ajouter des objets pour proposer un échange.', 'warning');
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des objets:', error);
                select.disabled = false;
                select.innerHTML = '<option value="">Erreur de chargement</option>';
                showToast('Impossible de charger vos objets.', 'danger');
            });
    }
    
    /**
     * Soumettre la proposition d'échange
     */
    function submitOffer() {
        // Récupérer l'ID de l'objet demandé depuis l'attribut data
        const objetDemandeId = modal.dataset.objetId;

        const offeredId = parseInt(select.value);
        const wantedId = parseInt(objetDemandeId);

        if (!Number.isFinite(offeredId) || offeredId <= 0) {
            showToast('Veuillez sélectionner un objet à proposer.', 'warning');
            return;
        }
        if (!Number.isFinite(wantedId) || wantedId <= 0) {
            showToast("Objet demandé invalide.", 'danger');
            return;
        }
        
        const fd = new FormData();
        fd.append('offered_id', String(offeredId));
        fd.append('wanted_id', String(wantedId));
        fd.append('message', (messageTextarea?.value || '').trim());
        
        // Désactiver le bouton de soumission
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Envoi...';
        
        fetch('/trade/request', {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                // Fermer le modal
                const bsModal = bootstrap.Modal.getInstance(modal);
                bsModal.hide();
                
                // Afficher un message de succès
                showToast('Proposition envoyée avec succès !', 'success');
                
                // Réinitialiser le formulaire
                form.reset();
                form.classList.remove('was-validated');
            } else {
                showToast(data.message || 'Erreur lors de l\'envoi de la proposition', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Erreur lors de l\'envoi de la proposition', 'danger');
        })
        .finally(() => {
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    }
    
    
    function showToast(message, type = 'info') {
        // Vérifier si la fonction toast existe dans app.js
        if (typeof toast === 'function') {
            toast(message, type);
        } else {
            // Fallback si toast n'existe pas
            alert(message);
        }
    }
});


document.addEventListener('click', function(e) {
    const shareBtn = e.target.closest('[data-tt-action="share"]');
    
    if (shareBtn) {
        e.preventDefault();
        
        // Utiliser l'API Web Share si disponible
        if (navigator.share) {
            navigator.share({
                title: document.title,
                url: window.location.href
            })
            .then(() => console.log('Partage réussi'))
            .catch(err => console.log('Erreur de partage:', err));
        } else {
            // Fallback: copier le lien dans le presse-papiers
            const url = window.location.href;
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url)
                    .then(() => {
                        if (typeof toast === 'function') {
                            toast('Lien copié dans le presse-papiers !', 'success');
                        } else {
                            alert('Lien copié : ' + url);
                        }
                    })
                    .catch(err => {
                        console.error('Erreur de copie:', err);
                        prompt('Copiez ce lien:', url);
                    });
            } else {
                // Fallback ultime
                prompt('Copiez ce lien:', url);
            }
        }
    }
});