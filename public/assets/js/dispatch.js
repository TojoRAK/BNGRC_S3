document.addEventListener('DOMContentLoaded', () => {
  const sections = document.querySelectorAll('[data-dispatch-collapse]');

  sections.forEach((section) => {
    const sectionId = section.getAttribute('id');
    if (!sectionId) {
      return;
    }

    const trigger = document.querySelector(`[data-bs-target="#${sectionId}"]`);
    if (!trigger) {
      return;
    }

    const icon = trigger.querySelector('i');
    const label = trigger.querySelector('[data-dispatch-btn-label]');

    const syncState = () => {
      const isOpen = section.classList.contains('show');

      if (label) {
        label.textContent = isOpen ? 'Masquer' : 'Afficher';
      }

      if (icon) {
        icon.classList.toggle('bi-chevron-up', isOpen);
        icon.classList.toggle('bi-chevron-down', !isOpen);
      }

      trigger.classList.toggle('collapsed', !isOpen);
      trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    };

    section.addEventListener('shown.bs.collapse', syncState);
    section.addEventListener('hidden.bs.collapse', syncState);
    syncState();
  });
});
