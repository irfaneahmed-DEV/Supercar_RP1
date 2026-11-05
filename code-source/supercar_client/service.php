<?php
session_start();
require_once '../connexion/dbconnexion.php';

// Charger les services
$services_db = [];
$sql = "SELECT idService, nom, libelle, img FROM service ORDER BY idService ASC";
$result = $conn->query($sql);

if ($result) {
  $services_db = $result->fetch_all(MYSQLI_ASSOC);
} else {
  echo "Erreur SQL : " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Supercar — Services 2026</title>
  <link rel="stylesheet" href="service.css">
  <link rel="stylesheet" href="mentions_legal.css" />
  <link rel="stylesheet" href="services_cards.css">
</head>

<body>
  <div class="tete">
    <div class="logo">
      <div class="logo_cont"><img src="../image/logo2.png" alt="Supercar Logo" width="150" height="100"></div>
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
  <main class="main-content">
    <div class="optique">
      <h2>L'Excellence à votre service</h2>
      <canvas id="particule"></canvas>
    </div>
    <div class="section-title">
      <h1>Nos Services</h1>
      <p>Performance, Entretien &amp; Personnalisation</p>
      <div class="section-title__line"></div>
    </div>
    <div class="services-grid" id="servicesGrid">
      <?php if (!empty($services_db)): ?>
        <?php foreach ($services_db as $index => $s):
          $id        = (int) $s['idService'];
          $img       = htmlspecialchars($s['img']);
          $nom       = htmlspecialchars($s['nom']);
          $lib       = htmlspecialchars($s['libelle']);
          $short_lib = mb_strimwidth($lib, 0, 100, '…');
        ?>
          <div class="service-card" id="card-<?php echo $id; ?>">
            <div class="service-card__bg" style="background-image: url('../image/<?php echo $img; ?>');"></div>
            <div class="service-card__overlay"></div>
            <div class="service-card__num"><?php echo sprintf('%02d', $index + 1); ?></div>
            <div class="service-card__body">
              <div class="service-card__title"><?php echo $nom; ?></div>
              <div class="service-card__sep"></div>
              <div class="service-card__desc"><?php echo $short_lib; ?></div>
              <div class="service-card__actions">
                <button type="button" class="service-card__expand-btn" onclick="expandCard(<?php echo $id; ?>)">En savoir plus</button>
              </div>
            </div>
            <div class="service-card__expanded-inner">
              <div class="service-card__expanded-content">
                <div class="service-card__title"><?php echo $nom; ?></div>
                <div class="service-card__sep"></div>
                <p class="service-card__expanded-desc"><?php echo nl2br($lib); ?></p>
                <div class="service-card__expanded-actions">
                  <button type="button" class="service-card__close-btn" onclick="collapseCard(<?php echo $id; ?>)">✕ Réduire</button>
                </div>
              </div>
              <div class="service-card__expanded-img" style="background-image: url('../image/<?php echo $img; ?>');"></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="services-empty">
          <span>⚠️</span>
          <p>Aucun service disponible pour le moment.<br>Vérifiez votre base de données.</p>
        </div>
      <?php endif; ?>
    </div>
    <section class="localisation">
      <h2>Nous trouver</h2>
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3948.798537974024!2d57.49782567454564!3d-20.27273978125256!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x217c592000000001%3A0x0!2sEbene%20Commercial%20Centre!5e0!3m2!1sfr!2smu!4v1734788000000!5m2!1sfr!2smu"
        width="100%" height="400" style="border:0;" allowfullscreen loading="lazy"></iframe>
    </section>
  </main>
  <footer>
    <div class="footer-content">
      <h2>SUPERCAR</h2>
      <p>Votre concessionnaire de voitures de marque.</p>
      <div class="socials">
        <a href="#"><img src="../image/facebook.png" alt="Facebook"></a>
        <a href="#"><img src="../image/instagram.png" alt="Instagram"></a>
        <a href="#"><img src="../image/twitter.png" alt="X"></a>
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
      <p>Lundi – Vendredi : 9h – 19h<br>Samedi : 10h – 18h<br>Dimanche : Fermé</p>
    </div>
    <hr>
    <p class="copyright">© 2026 Supercar. Tous droits réservés.</p>
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
  <script src="particules.js"></script>
  <script src="service.js"></script>
</body>

</html>