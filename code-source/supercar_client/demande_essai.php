<?php
session_start();
include('../connexion/dbconnexion.php');
/** @var Mysqli $conn */
// Récupérer la voiture pré-sélectionnée si on vient de voiture.php
$idVoiturePre = intval($_GET['idVoiture'] ?? 0);

// Récupérer toutes les voitures pour le select
$stmt = $conn->prepare("SELECT v.idVoiture, v.modele, m.nom AS marque 
                         FROM voiture v 
                         JOIN marque m ON m.id_marque = v.id_marque 
                         ORDER BY m.nom, v.modele");
$stmt->execute();
$listeVoitures = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Récupérer les infos de la voiture sélectionnée (modèle, prix, carburant)
$voitureInfo = null;
if ($idVoiturePre !== 0) {
  $stmtInfo = $conn->prepare("SELECT v.modele, m.nom AS marque, v.prix, v.typeCarburant as carburant, v.imageVoiture 
                               FROM voiture v 
                               JOIN marque m ON m.id_marque = v.id_marque 
                               WHERE v.idVoiture = ?");
  $stmtInfo->bind_param("i", $idVoiturePre);
  $stmtInfo->execute();
  $voitureInfo = $stmtInfo->get_result()->fetch_assoc();
}
?>
<!doctype html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Supercar — Demande d'Essai</title>
  <link rel="stylesheet" href="demande.css" />
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
          <a href="deconnexion.php" style="color:#ff6060;">Se déconnecter</a>
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

  <div class="main-content">
    <div class="conteneur">
      <div class="dem-es">
        <h1>Votre demande d'essai</h1>

        <?php if (!isset($_SESSION['idUtil'])): ?>
          <div class="alert-info">
            <p>Vous devez être connecté pour faire une demande d'essai.</p>
            <a href="connexion.php" class="btn-essai">Se connecter</a>
            <a href="s'inscrire.php" class="btn-essai">Créer un compte</a>
          </div>
          <?php
          $_SESSION['demande'] = true;
          header("Location: connexion.php") ?>
        <?php elseif (!empty($_GET['success'])): ?>
          <div class="success">
            ✅ Votre demande a bien été enregistrée ! Nous vous contacterons pour confirmer.
          </div>

        <?php else: ?>

          <div class="deux-cadres">

            <!-- ===== CADRE 1 : FORMULAIRE ===== -->
            <div class="cadre cadre-formulaire">
              <div class="cadre-header">
                <span class="cadre-icon">📋</span>
                <h2>Vos informations</h2>
              </div>

              <form method="GET" id="formVoiture" class="form-select-voiture">
                <div class="field-group">
                  <label for="voiture">Modèle de voiture</label>
                  <select name="idVoiture" id="voiture" required
                    onchange="document.getElementById('formVoiture').submit()">
                    <option value="">— Sélectionnez un modèle —</option>
                    <?php foreach ($listeVoitures as $v): ?>
                      <option
                        value="<?= $v['idVoiture'] ?>"
                        <?= ($v['idVoiture'] == $idVoiturePre) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </form>

              <form method="post" action="traitement_demande.php" class="form-reservation">
                <input type="hidden" name="idVoiture" value="<?= $idVoiturePre ?>">

                <?php if ($_SESSION['idUtil']):

                  $clientInfo = getInfoUtil($_SESSION['idUtil']);
                ?>
                  <div class="field-group">
                    <label for="prenom">Prénom</label>
                    <input style='color:gray' type="text" name="prenom" id="prenom" placeholder="Votre prénom" readonly value="<?= $clientInfo['prenom'] ?>" />
                  </div>

                  <div class="field-group">
                    <label for="email">Adresse e-mail</label>
                    <input style='color:gray' type="email" name="email" id="email" placeholder="votre@email.com" readonly value="<?= $clientInfo['email'] ?>" />
                  </div>

                  <div class="field-group">
                    <label for="telephone">Téléphone</label>
                    <input style='color:gray' type="tel" name="telephone" id="telephone" placeholder="+33 6 00 00 00 00" readonly value="<?= $clientInfo['telephone'] ?>" />
                  </div>
                <?php endif ?>
                <div class="field-group">
                  <label for="date">Date souhaitée</label>
                  <input type="date" name="date" id="date"
                    min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required />
                </div>

                <div class="field-group">
                  <label for="Demande_heure">Heure souhaitée pour la demande (9h à 18h)</label>
                  <input
                    type="time"
                    id="Demande_heure"
                    name="Demande_heure"
                    min="09:00"
                    max="19:00"
                    value="09:00"
                    step="1800"
                    required >
                </div>

                <input type="submit" value="Confirmer la réservation" />
              </form>
            </div>

            <!-- ===== CADRE 2 : INFOS VOITURE ===== -->
            <div class="cadre cadre-voiture">
              <div class="cadre-header">
                <span class="cadre-icon">🚗</span>
                <h2>Véhicule sélectionné</h2>
              </div>

              <?php if ($voitureInfo): ?>
                <div class="voiture-photo">
                  <img src="../image/imagevoiture/<?= htmlspecialchars($voitureInfo['imageVoiture']) ?>" alt="<?= htmlspecialchars($voitureInfo['marque'] . ' ' . $voitureInfo['modele']) ?>">
                </div>
                <div class="voiture-details">
                  <h3 class="voiture-nom"><?= htmlspecialchars($voitureInfo['marque'] . ' ' . $voitureInfo['modele']) ?></h3>
                  <div class="voiture-specs">
                    <div class="spec-item">
                      <span class="spec-label">Prix</span>
                      <span class="spec-value prix"><?= number_format($voitureInfo['prix'], 0, ',', ' ') ?> €</span>
                    </div>
                    <div class="spec-divider"></div>
                    <div class="spec-item">
                      <span class="spec-label">Carburant</span>
                      <span class="spec-value"><?= htmlspecialchars($voitureInfo['carburant']) ?></span>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                <div class="voiture-placeholder">
                  <div class="placeholder-icon">🏎️</div>
                  <p>Sélectionnez un modèle pour voir ses informations</p>
                </div>
              <?php endif; ?>
            </div>

          </div><!-- fin .deux-cadres -->

        <?php endif; ?>
      </div>
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
    const timeInput = document.getElementById('Demande_heure');
    timeInput.addEventListener('click', e => {
      time = timeInput.value;
      timeInput.value = roundTime(time);
    });

    // Arrondit l'heure à la demi-heure la plus proche (00 ou 30)
    function roundTime(time) {
      // Si l'input est vide, on retourne une chaîne vide
      if (!time) return "";

      const [heureStr, minutesStr] = time.split(":");

      let heure = parseInt(heureStr, 10);
      let minutes = parseInt(minutesStr, 10);

      if (minutes > 30) {
        minutes = 0;
        heure += 1;
      } else if (minutes < 15 && minutes !== 0) {
        minutes = 0;
      }else if(minutes > 15){
        minutes = 30;
      }
       else if (minutes === 30) {
        // Si c'est pile 30, on ne change rien, mais on doit quand même retourner l'heure
        return time;
      }

      // Gestion du débordement des 24 heures (ex: 23:45 devient 00:00)
      if (heure >= 24) {
        heure = 0;
      }

      // Formatage pour toujours avoir deux chiffres (ex: "09:00" au lieu de "9:0")
      const heureFormatee = String(heure).padStart(2, '0');
      const minutesFormatees = String(minutes).padStart(2, '0');

      return `${heureFormatee}:${minutesFormatees}`;
    }
  </script>

</body>

</html>

<?php

function getInfoUtil(int $id): array
{
  global $conn;
  $sql = "SELECT * FROM client WHERE idClient = ? ";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}
