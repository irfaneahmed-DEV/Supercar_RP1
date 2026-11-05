<?php
require_once '../connexion/dbconnexion.php';
session_start();

$imageChoisie = [];

/** @var Mysqli $conn */

$sql = "SELECT imageVoiture, idVoiture FROM voiture WHERE estDansCarrousel = true";
$stmt = $conn->prepare($sql);
$stmt->execute();
$solus = $stmt->get_result();
$imageChoisie = $solus->fetch_all(MYSQLI_ASSOC); // ← fetch_all !

/* Fonctions nécessaires */
function estConnecte()
{
    return isset($_SESSION['idUtil']);
}
function estAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/* Charge tous les services */
$services_db = [];
$sql = "SELECT idService, nom, libelle, img FROM service ORDER BY idService ASC";
$result = $conn->query($sql);

if ($result) {
    $services_db = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Erreur SQL : " . $conn->error;
}

/* Sélection de 4 services vedette */
$services_vedette = array_slice($services_db, 0, 4);

/* Charge toutes les voitures */
$voitures_db = [];
$sql = "SELECT idVoiture, modele, imageVoiture FROM voiture ORDER BY idVoiture ASC";
$result = $conn->query($sql);

if ($result) {
    $voitures_db = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Erreur SQL : " . $conn->error;
}

/* Sélection de 4 voitures vedette */
$voitures_vedette = array_slice($voitures_db, 0, 4);
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Supercar — Accueil</title>
    <link rel="stylesheet" href="head.css" />
    <link rel="stylesheet" href="mentions_legal.css" />

    <style>
        .V_image_element {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .voiture_vedette_element .service-nom {
            font-size: 12px;
            color: #ccc;
            font-family: "Georgia", serif;
            text-align: center;
            padding: 6px 8px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #ccc;
            font-family: "Georgia", serif;
        }

        .user-badge span {
            color: var(--crimson);
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
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
            <?php if (estAdmin()): ?>
                <div class="admin"><a href="gestion.php">Admin</a></div>
            <?php endif; ?>
        </div>
        <button class="menu-toggle" id="menuToggle" aria-label="Menu">☰</button>
        <div class="dropdown">
            <button id="dropdownBtn">
                <?php echo estConnecte() ? '👤 ' . htmlspecialchars($_SESSION['user_prenom']) : 'Compte'; ?>
            </button>
            <div class="Drop_item">
                <?php if (estConnecte()): ?>
                    <a href="deconnexion.php" style="color:#ff4060;">Se déconnecter</a>
                <?php else: ?>
                    <a href="inscription.php">S'inscrire</a>
                    <a href="connexion.php">Se connecter</a>
                <?php endif; ?>
            </div>
        </div>
    </div> <!-- fermeture correcte de .tete -->

    <div class="mobile-menu" id="mobileMenu">
        <a href="index.php">Accueil</a>
        <a href="voiture.php">Voitures</a>
        <a href="demande_essai.php">Demande d'Essai</a>
        <a href="service.php">Services</a>
        <a href="contact.php">Contactez-nous</a>
        <?php if (estConnecte()): ?>
            <a href="deconnexion.php" style="color:#ff6060;">Se déconnecter</a>
        <?php else: ?>
            <a href="inscription.php">S'inscrire</a>
            <a href="connexion.php">Se connecter</a>
        <?php endif; ?>
    </div>

    <!-- CAROUSEL -->
    <div class="carousel">
        <div class="carou_text">
            <h1>L'excellence automobile</h1>
            <h2>Découvrez notre grande collection de voitures de par le monde</h2>
            <button class="carou_btn" onclick="location.href='voiture.php'">Explorez Maintenant</button>
            <button class="carou_btn" onclick="location.href='demande_essai.php'">Réservez maintenant</button>
        </div>
        <?php if ($imageChoisie): ?>
            <?php foreach ($imageChoisie as $i => $row): ?>
                <img src="../image/imagevoiture/<?php echo htmlspecialchars($row['imageVoiture']); ?>"
                    alt=""
                    class="slide <?php echo $i === 0 ? 'active' : ''; ?>">
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- CREDOS -->
    <div class="fade-item">
        <div class="credo">
            <div class="credo_element">
                <img class="img" src="../image/etoile.png" alt="Étoile" />
                <h2>Collection Exclusive</h2>
                <p>Une sélection rigoureuse parmi les plus belles voitures du monde.</p>
            </div>
            <div class="credo_element">
                <img class="img" src="../image/bouclier.png" alt="Bouclier" />
                <h2>Garantie Premium</h2>
                <p>Tous les véhicules sont certifiés avec un service après-vente assuré.</p>
            </div>
            <div class="credo_element">
                <img class="img" src="../image/badge.png" alt="Badge" />
                <h2>Service d'Excellence</h2>
                <p>Accompagnement personnalisé de l'achat jusqu'à l'entretien.</p>
            </div>
        </div>
    </div>

    <!-- VOITURES EN VEDETTE -->
    <div class="annonce">
        <div class="fade-item">
            <div class="vedette">
                <h1>Voitures en vedette</h1>
                <h3>Découvrez les sélections du moment</h3>
                <div class="voiture_container">
                    <button class="gauche"><img class="fleche" src="../image/fleche-gauche.png" alt="Précédent" /></button>
                    <div class="voiture_vedette">
                        <?php if (!empty($voitures_vedette)): ?>
                            <?php foreach ($voitures_vedette as $car): ?>
                                <div class="voiture_vedette_element">
                                    <div class="V_image_element" style="background-image: url('../image/imagevoiture/<?php echo htmlspecialchars($car['imageVoiture']); ?>');">
                                    </div>
                                    <span class="service-nom"><?php echo htmlspecialchars($car['modele']); ?></span>
                                    <a href="voiture.php">Voir les voitures</a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="voiture_vedette_element">
                                <div class="V_image_element"></div><a href="voiture.php">Voir les voitures</a>
                            </div>
                            <div class="voiture_vedette_element">
                                <div class="V_image_element"></div><a href="voiture.php">Voir les voitures</a>
                            </div>
                            <div class="voiture_vedette_element">
                                <div class="V_image_element"></div><a href="voiture.php">Voir les voitures</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button class="droite"><img class="fleche" src="../image/fleche-droite.png" alt="Suivant" /></button>
                </div>
            </div>
            <div style="text-align:center; margin:20px 0">
                <a href="voiture.php" class="suivant">Voir toutes les voitures</a>
            </div>
        </div>
    </div>

    <!-- SERVICES EN VEDETTE -->
    <div class="annonce">
        <div class="fade-item">
            <div class="vedette">
                <h1>Découvrir les services</h1>
                <h3>Découvrez les meilleures offres de services du moment</h3>
                <div class="voiture_container">
                    <button class="gauche"><img class="fleche" src="../image/fleche-gauche.png" alt="Précédent" /></button>
                    <div class="voiture_vedette">
                        <?php if (!empty($services_vedette)): ?>
                            <?php foreach ($services_vedette as $svc): ?>
                                <div class="voiture_vedette_element">
                                    <div class="V_image_element" style="background-image: url('../image/<?php echo htmlspecialchars($svc['img']); ?>');">
                                    </div>
                                    <span class="service-nom"><?php echo htmlspecialchars($svc['nom']); ?></span>
                                    <a href="service.php">Voir le service</a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="voiture_vedette_element">
                                <div class="V_image_element"></div><a href="service.php">Voir le service</a>
                            </div>
                            <div class="voiture_vedette_element">
                                <div class="V_image_element"></div><a href="service.php">Voir le service</a>
                            </div>
                            <div class="voiture_vedette_element">
                                <div class="V_image_element"></div><a href="service.php">Voir le service</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button class="droite"><img class="fleche" src="../image/fleche-droite.png" alt="Suivant" /></button>
                </div>
            </div>
            <div style="text-align:center; margin:20px 0">
                <a href="service.php" class="suivant">Tous nos services</a>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <section class="cta-section">
        <h1>Prêt à vivre l'expérience Supercar ?</h1>
        <h3>Réservez votre essai gratuit dès aujourd'hui</h3>
        <a href="demande_essai.php" class="cta-button">Réserver un essai</a>
    </section>

    <!-- FOOTER -->
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
            <p>Lundi – Vendredi : 9h – 19h<br />Samedi : 10h – 18h<br />Dimanche : Fermé</p>
        </div>
        <hr />
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

    <script>
        const items = document.querySelectorAll(".fade-item");

        function checkVisibility() {
            items.forEach(item => item.classList.toggle("visible",
                item.getBoundingClientRect().top < window.innerHeight - 100));
        }

        window.addEventListener("scroll", checkVisibility);
        window.addEventListener("load", checkVisibility);

        document.querySelectorAll(".droite").forEach((btn, i) => {
            const v = document.querySelectorAll(".voiture_vedette")[i];
            if (!v) return;
            btn.addEventListener("click", () => {
                const item = v.querySelector(".voiture_vedette_element");
                const gap = parseFloat(getComputedStyle(v).gap) || 0;
                const step = item ? Math.round(item.getBoundingClientRect().width + gap) : v.clientWidth;
                v.scrollLeft >= v.scrollWidth - v.clientWidth - 1 ?
                    v.scrollTo({
                        left: 0,
                        behavior: "smooth"
                    }) :
                    v.scrollBy({
                        left: step,
                        behavior: "smooth"
                    });
            });
        });

        document.querySelectorAll(".gauche").forEach((btn, i) => {
            const v = document.querySelectorAll(".voiture_vedette")[i];
            if (!v) return;
            btn.addEventListener("click", () => {
                const item = v.querySelector(".voiture_vedette_element");
                const gap = parseFloat(getComputedStyle(v).gap) || 0;
                const step = item ? Math.round(item.getBoundingClientRect().width + gap) : v.clientWidth;
                v.scrollLeft <= 1 ?
                    v.scrollTo({
                        left: v.scrollWidth - v.clientWidth,
                        behavior: "smooth"
                    }) :
                    v.scrollBy({
                        left: -step,
                        behavior: "smooth"
                    });
            });
        });
    </script>
    <script src="animation.js"></script>
</body>
<?php

?>

</html>