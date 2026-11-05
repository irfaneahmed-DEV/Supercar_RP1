<?php
session_start();
include('../connexion/dbconnexion.php');

/** @var Mysqli $conn */

// Recupure la premiere marque inseré pas ordre croissant
$marque = LastMarque();

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['marque'])) {
  $marque = $_POST['marque'];
}

// ✅ Récupération de toutes les marques
$selectionM = "SELECT * FROM marque ORDER BY nom";
$stmt = $conn->prepare($selectionM);
$stmt->execute();
$marqueList = $stmt->get_result();
$toutesMarques = $marqueList->fetch_all(MYSQLI_ASSOC); // stocké en tableau pour réutilisation

// ✅ Récupération des véhicules par marque sélectionnée
$selectionV = "SELECT v.*, m.nom AS nomMarque 
               FROM voiture v
               JOIN marque m ON m.id_marque = v.id_marque
               WHERE m.nom = ?
               ORDER BY v.modele";
$stmt = $conn->prepare($selectionV);
$stmt->bind_param("s", $marque);
$stmt->execute();
$voituresList = $stmt->get_result();
$toutesVoitures = $voituresList->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Supercar — Nos Voitures</title>
  <link rel="stylesheet" href="mentions_legal.css" />
  <link rel="stylesheet" href="car.css" />
</head>

<body>

  <!-- NAVBAR -->
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

    <!-- ✅ Navbar dynamique selon session -->
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

  <!-- CONTENU PRINCIPAL -->
  <div class="page-voitures">

    <!-- ✅ SIDEBAR : une seule form POST, les boutons soumettent avec la valeur de marque -->
    <aside class="sidebar">
      <?php foreach ($toutesMarques as $m): ?>
        <form method="POST" style="margin:0;">
          <button
            class="side-btn <?= ($m['nom'] === $marque) ? 'active' : '' ?>"
            name="marque"
            value="<?= htmlspecialchars($m['nom']) ?>">
            <?= htmlspecialchars($m['nom']) ?>
          </button>
        </form>
      <?php endforeach; ?>
    </aside>

    <!-- LISTE DES VOITURES -->
    <main class="content">
      <section class="tab active">
        <h1><?= htmlspecialchars($marque) ?></h1>
        <div class="grid">
          <?php if (empty($toutesVoitures)): ?>
            <p class="vide">Aucune voiture disponible pour cette marque.</p>
          <?php else: ?>
            <!-- affichage des voitures -->
            <?php foreach ($toutesVoitures as $v): ?>
              <div class="image">
                <div class="card">
                  <img
                    src="../image/imagevoiture/<?= htmlspecialchars($v['imageVoiture']) ?>"
                    alt="<?= htmlspecialchars($v['modele']) ?>"
                    onerror="this.src='../image/placeholder.jpg'" />
                  <div class="card-info">
                    <h3><?= htmlspecialchars($v['modele']) ?></h3>
                  </div>
                </div>
                <div class="pop-up">
                  <div class="container-vehicule">

                    <!-- GAUCHE -->
                    <div class="left">
                      <img src="../image/imagevoiture/<?= htmlspecialchars($v['imageVoiture']) ?>" alt="Voiture" class="car-image">

                      <div class="main-info">
                        <h1><?= htmlspecialchars($v['modele']) ?></h1>
                        <div class="price"><?= htmlspecialchars($v['prix']) ?> RS</div>
                        <a href="demande_essai.php?idVoiture=<?= $v['idVoiture'] ?>" class="btn">Demande d'essai</a>
                        <button class="btn" onclick="enleverPopUp()">Retour</button>
                      </div>
                    </div>

                    <!-- DROITE -->
                    <div class="right">
                      <h2>Informations</h2>

                      <div class="info-vehicule">
                        <span class="label">Modèle</span>
                        <span class="value"><?= htmlspecialchars($v['modele']) ?></span>
                      </div>

                      <div class="info-vehicule">
                        <span class="label">Année</span>
                        <span class="value"> <?= htmlspecialchars($v['annee']) ?></span>
                      </div>

                      <div class="info-vehicule">
                        <span class="label">Puissance</span>
                        <span class="value"><?= htmlspecialchars($v['puissance']) ?></span>
                      </div>

                      <div class="info-vehicule">
                        <span class="label">Carburant</span>
                        <span class="value"><?= htmlspecialchars($v['typeCarburant']) ?></span>
                      </div>

                    </div>

                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>

    </main>
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
      <p>123 Avenue des Champs-Élysées<br />75008 Paris, France</p>
      <p>+33 1 23 45 67 89</p>
      <p>contact@supercar.fr</p>
    </div>
    <div class="footer-hours">
      <h2>Horaires</h2>
      <p>Lundi – Vendredi : 9h – 19h<br />Samedi : 10h – 18h<br />Dimanche : Fermé</p>
    </div>
    <hr />
    <p class="copyright">© 2025 Supercar. Tous droits réservés.</p>
    <p class="copyright">
      <!-- Bouton pour ouvrir la popup (à placer où tu veux, ex: footer) -->
      <a style='text-decoration: none;color:inherit;' href="#" onclick="openMentionsLegales(event)">Mentions légales   </a>
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
<?php
include('../connexion/dbconnexion.php');

/* ✅ Fonctions nécessaires */
function estConnecte()
{
  return isset($_SESSION['idUtil']);
}
function estAdmin()
{
  return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/* ✅ Charger tous les services */
$services_db = [];
$sql = "SELECT idService, nom, libelle, img FROM service ORDER BY idService ASC";
$result = $conn->query($sql);

if ($result) {
  $services_db = $result->fetch_all(MYSQLI_ASSOC);
} else {
  echo "Erreur SQL : " . $conn->error;
}

/* ✅ Sélection de 4 services vedette */
$services_vedette = array_slice($services_db, 0, 4);

function LastMarque(): string
{
  include('../connexion/dbconnexion.php');
  $sql = "SELECT nom FROM marque ORDER BY nom ASC limit 1";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();

  return $row['nom'];
}
