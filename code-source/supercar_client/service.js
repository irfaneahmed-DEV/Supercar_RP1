// ============================================================
//  service.js — Supercar · Logique des cartes services
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

  // ── Agrandir une carte ──
  window.expandCard = function(id) {
    const grid = document.getElementById('servicesGrid');
    const card = document.getElementById('card-' + id);
    if (!card) return;

    // Fermer toute carte déjà ouverte (sauf celle qu'on ouvre)
    document.querySelectorAll('.service-card.expanded').forEach(c => {
      if (c !== card) _collapse(c);
    });

    // Ouvrir la carte
    card.classList.add('expanded');

    // Scroll doux vers la carte après la transition
    setTimeout(() => {
      card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 350);
  };

  // ── Réduire une carte par id ──
  window.collapseCard = function(id) {
    const card = document.getElementById('card-' + id);
    if (card) _collapse(card);
  };

  // ── Réduire interne ──
  function _collapse(card) {
    card.classList.remove('expanded');
  }

  // ── Auto-hide toast confirmation ──
  const toast = document.getElementById('confirmation-toast');
  if (toast) {
    setTimeout(() => {
      toast.style.opacity = '0';
      setTimeout(() => toast.remove(), 500);
    }, 4000);
  }

});