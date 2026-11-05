// ============================================================
//  animation.js — Supercar
//  Gère: Dropdown, Carousel, Menu Mobile
// ============================================================

// ---- DROPDOWN ----
const btn = document.getElementById('dropdownBtn');
const menu = document.querySelector('.Drop_item');
if (btn && menu) {
  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    menu.classList.toggle('show');
  });
  document.addEventListener('click', (e) => {
    if (!btn.contains(e.target) && !menu.contains(e.target)) {
      menu.classList.remove('show');
    }
  });
}

// ---- MENU HAMBURGER MOBILE ----
const menuToggle = document.getElementById('menuToggle');
const mobileMenu = document.getElementById('mobileMenu');
if (menuToggle && mobileMenu) {
  menuToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    mobileMenu.classList.toggle('open');
    menuToggle.textContent = mobileMenu.classList.contains('open') ? '✕' : '☰';
  });
  document.addEventListener('click', (e) => {
    if (!menuToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
      mobileMenu.classList.remove('open');
      menuToggle.textContent = '☰';
    }
  });
}

// ---- CAROUSEL ----
const slides = document.querySelectorAll('.slide');
let current = 0;
if (slides.length > 0) {
  function nextSlide() {
    slides[current].classList.remove('active');
    current = (current + 1) % slides.length;
    slides[current].classList.add('active');
  }
  setInterval(nextSlide, 5000);
}

// ---- Affichage d'une pop-up ----
const infos = document.querySelectorAll('.image');
infos.forEach(info => {
  const pop_up = info.querySelector('.pop-up');
  const card = info.querySelector('.card');
  card.addEventListener('click', afficherCard);
  function afficherCard() {
    pop_up.classList.toggle('active');
  }
});

window.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') {
    enleverPopUp();
  }
});

function enleverPopUp() {
  const popUp = document.querySelectorAll('.pop-up');
  popUp.forEach(pop_up => {
    pop_up.classList.remove('active');
  });
}


// Fonctions de gestions des popups de mentions legals
function openMentionsLegales(e) {
  if (e) e.preventDefault();
  document.getElementById('mentionsLegalesOverlay').style.display = 'flex';
}

function closeMentionsLegales() {
  document.getElementById('mentionsLegalesOverlay').style.display = 'none';
}
// Fermer en cliquant en dehors du contenu
document.getElementById('mentionsLegalesOverlay').addEventListener('click', function (e) {
  if (e.target === this) closeMentionsLegales();
});

// Fonctions de gestions des popups de mentions Conditions d'utilisation
function openCondition(e) {
  if (e) e.preventDefault();
  document.getElementById('ConfidentialiteOverlay').style.display = 'flex';
}

function closeCondition() {
  document.getElementById('ConfidentialiteOverlay').style.display = 'none';
}
// Fermer en cliquant en dehors du contenu
document.getElementById('ConfidentialiteOverlay').addEventListener('click', function (e) {
  if (e.target === this) closeCondition();
});