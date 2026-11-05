<?php
session_start();
require_once '../connexion/dbconnexion.php';

/** @var Mysqli $conn */
// On vérifie si un message de succès est envoyé
$success = isset($_GET['success']);

// Si le client est connecté, on récupère ses infos depuis la BDD
$clientInfo = null;
if (isset($_SESSION['idUtil'])) {
  $stmt = $conn->prepare("SELECT nom, prenom, email, telephone FROM client WHERE idClient = ?");
  $stmt->bind_param("i", $_SESSION['idUtil']);
  $stmt->execute();
  $clientInfo = $stmt->get_result()->fetch_assoc();
}
?>
<!doctype html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Supercar — Contactez-nous</title>
  <link rel="stylesheet" href="contact.css" />
  <link rel="stylesheet" href="mentions_legal.css" />

</head>

<body>
  <div class="tete">
    <div class="logo">
      <div class="logo_cont">
        <img
          src="../image/logo2.png"
          alt="Supercar Logo"
          width="150"
          height="100" />
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
        <button id="dropdownBtn"> 👤 <?= htmlspecialchars($_SESSION['user_prenom'] ?? 'Mon Compte') ?></button>
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

  <main class="contact-page">


    <?php if ($success): ?>
      <div style="
        background:#4CAF50;
        color:white;
        padding:15px 20px;
        margin:20px auto;
        width:90%;
        max-width:600px;
        text-align:center;
        border-radius:8px;
        font-size:18px;
        box-shadow:0 4px 10px rgba(0,0,0,0.3);
      ">
        Votre message a bien été envoyé !
      </div>
    <?php endif; ?>


    <h1 class="contact-title">Contactez-nous pour toutes demandes</h1>
    <div class="info-blocks">
      <div class="info-box">
        <h3><span class="info-icon">ADRESSE</span></h3>
        <p>Ebene Commercial Centre<br />QF4Q+M6P, Quatre Bornes</p>
      </div>
      <div class="info-box">
        <h3><span class="info-icon">EMAIL</span></h3>
        <p>info.supercar@gmail.com</p>
      </div>
      <div class="info-box">
        <h3><span class="info-icon">TÉLÉPHONE</span></h3>
        <p>+230 5536-0799</p>
      </div>
      <div class="info-box">
        <h3><span class="info-icon">Personne à contacter </span></h3>
        <p>Ahmed Abdou</p>
      </div>
    </div>

    <div class="contact-main">
      <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1112.8930548973449!2d57.489216914247514!3d-20.245927682032182!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sfr!2smu!4v1779344833628!5m2!1sfr!2smu"
          width="100%"
          height="100%"
          style="border:0;"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>

      <div class="form-container">
        <p class="form-intro">
          Nous sommes là pour répondre à toutes vos questions concernant nos
          véhicules, nos services et vos besoins. N'hésitez pas à nous écrire !
        </p>

        <form action="traitement_contact.php" method="POST">

          <?php if (!isset($_SESSION['idUtil'])): ?>
            <!-- Utilisateur NON connecté : champs libres -->
            <div class="form-group">
              <label for="nom">NOM</label>
              <input type="text" id="nom" name="nom" placeholder="Votre nom" required>
            </div>

            <div class="form-group">
              <label for="email">EMAIL</label>
              <input type="email" id="email" name="email" placeholder="votre@email.com" required>
            </div>

            <div class="form-group">
              <label for="telephone">TÉLÉPHONE</label>
              <input type="tel" id="telephone" name="telephone" placeholder="+230 5X XX XX XX" required>
            </div>

          <?php else: ?>
            <!-- Utilisateur connecté : champs pré-remplis en lecture seule -->
            <p style="margin-bottom:15px; color:#aaa; font-size:14px;">

            </p>

            <div class="form-group">
              <label for="nom">NOM</label>
              <input type="text" id="nom" name="nom"
                value="<?= htmlspecialchars(($clientInfo['prenom'] ?? '') . ' ' . ($clientInfo['nom'] ?? '')) ?>"
                readonly
                style="opacity:0.6; cursor:not-allowed; background:#1a1a1a;">
            </div>

            <div class="form-group">
              <label for="email">EMAIL</label>
              <input type="email" id="email" name="email"
                value="<?= htmlspecialchars($clientInfo['email'] ?? '') ?>"
                readonly
                style="opacity:0.6; cursor:not-allowed; background:#1a1a1a;">
            </div>

            <div class="form-group">
              <label for="telephone">TÉLÉPHONE</label>
              <input type="tel" id="telephone" name="telephone"
                value="<?= htmlspecialchars($clientInfo['telephone'] ?? '') ?>"
                readonly
                style="opacity:0.6; cursor:not-allowed; background:#1a1a1a;">
            </div>

          <?php endif; ?>

          <!-- Message (toujours affiché) -->
          <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" placeholder="Votre message..." required></textarea>
          </div>

          <button type="submit" class="submit-btn">Envoyer le message</button>
        </form>

      </div>
    </div>
  </main>

  <footer>
    <div class="footer-content">
      <h2>SUPERCAR</h2>
      <p>Votre concessionnaire de voitures de marque.</p>
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
      <p>
        Lundi – Vendredi : 9h – 19h<br />Samedi : 10h – 18h<br />Dimanche :
        Fermé
      </p>
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
</body>

</html>