<?php
session_start();
?>

<!doctype html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Supercar — Se connecter</title>
  <link rel="stylesheet" href="connect.css" />
  <link rel="stylesheet" href="mentions_legal.css" />
</head>

<body>
  <div class="tete">
    <div class="logo">
      <div class="logo_cont">
        <img src="../image/logo2.png" alt="Supercar Logo" width="150" height="100" />
      </div>
    </div>
    <div class="pages">
      <a href="index.php">Accueil</a>
      <a href="voiture.php">Voitures</a>
      <a href="demande_essai.php">Demande d'Essai</a>
      <a href="service.php">Services</a>
      <a href="contact.php">Contactez-nous</a>
    </div>
    <button class="menu-toggle" id="menuToggle" aria-label="Menu">☰</button>

    <div class="dropdown">
      <?php if (isset($_SESSION['idUtil'])): ?>
        <button id="dropdownBtn">👤 <?= htmlspecialchars($_SESSION['user_prenom'] ?? 'Mon Compte') ?></button>
        <div class="Drop_item">
          <a href="deconnexion.php" style="color:#ff4060;">Se déconnecter</a>
        </div>
      <?php else: ?>
        <button id="dropdownBtn">Compte</button>
        <div class="Drop_item">
          <a href="inscription.php">S'inscrire</a>
          <a href="connexion.php">Se connecter</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="mobile-menu" id="mobileMenu">
    <a href="index.php">Accueil</a>
    <a href="voiture.php">Voitures</a>
    <a href="demande_essai.php">Demande d'Essai</a>
    <a href="service.php">Services</a>
    <a href="contact.php">Contactez-nous</a>
    <?php if (isset($_SESSION['idUtil'])): ?>
      <a href="deconnexion.php" style="color:#ff6060;">Se déconnecter</a>
    <?php else: ?>
      <a href="inscription.php">S'inscrire</a>
      <a href="connexion.php">Se connecter</a>
    <?php endif; ?>
  </div>

  <div class="conteneur">
    <?php if (!empty($_SESSION['login_error'])): ?>
      <div class="error">Identifiants ou mot de passe incorrects</div>
      <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <div class="conec">
      <h1>Se connecter</h1>
      <form method="post" action="login.php">
        <input type="email" name="e-mail" placeholder="Adresse e-mail" required />
        <input type="password" name="mp" placeholder="Mot de passe" required />
        <input type="submit" value="Se connecter" />
        <p>Pas encore de compte ? <a href="inscription.php">Créez-en un</a></p>
      </form>
    </div>
  </div>

  <footer>
    <div class="footer-content">
      <h2>SUPERCAR</h2>
      <p>Votre concessionnaire de voitures de marque.</p>
      <p>Découvrez les plus belles voitures accessibles à tous.</p>
      <div class="socials">
        <a href="#"><img src="../image/facebook.png" alt="Facebook" /></a>
        <a href="#"><img src="../image/instagram.png" alt="Instagram" /></a>
        <a href="#"><img src="../image/twitter.png" alt="X" /></a>
      </div>
    </div>
    <div class="footer-contact">
      <h2>Contact</h2>
      <p>Ebene Commercial Centre<br />Quatre Bornes, Maurice</p>
      <p>+230 5536-0799</p>
      <p>info.supercar@gmail.com</p>
    </div>
    <div class="footer-hours">
      <h2>Horaires</h2>
      <p>Lundi – Vendredi : 9h – 19h<br />Samedi : 10h – 18h<br />Dimanche : Fermé</p>
    </div>
    <hr />
    <p class="copyright">© 2025 Supercar. Tous droits réservés.</p>
    <p class="copyright">
      <!-- Bouton pour ouvrir la popup (à placer où tu veux, ex: footer) -->
      <a style='text-decoration: none;color:inherit;' href="#" onclick="openMentionsLegales(event)">Mentions légales</a>
      <a style='text-decoration: none;color:inherit;' href="#" onclick="openCondition(event)">politique de confidentialité</a>
    </p>
  </footer>

  <?php
  require_once 'mentions_legales.php';
  echo getMentionsLegales();
  echo getPolitiqueDeConfidentialite();
  ?>
  <script src="animation.js"></script>
  <script>
    window.addEventListener('DOMContentLoaded', () => {
      const msg = document.querySelector('.error');
      if (msg) {
        setTimeout(() => {
          msg.classList.add('hide');
        }, 3000);
      }
    });
  </script>
</body>

</html>