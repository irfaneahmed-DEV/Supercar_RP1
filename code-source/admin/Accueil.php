<?php
require_once 'admin_guard.php';
require_once '../connexion/dbconnexion.php';
require_once 'admin_layout.php';

// Récupération de toutes les voitures pour la sélection
$sql  = "SELECT idVoiture, imageVoiture, modele FROM voiture ORDER BY modele";
$tab  = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$limit = 3;

// Traitement du formulaire d'enregistrement du carrousel
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $success = false;
    $ids = [];

    for ($i = 1; $i <= $limit; $i++) {
        $key = "accueil" . $i;
        if (!empty($_POST[$key])) {

            $id = (int)$_POST[$key];
            $ids[] = $id;

            // Activation de l'élément sélectionné
            $sql = "UPDATE voiture SET estDansCarrousel = true WHERE idVoiture = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
        }
    }

    // Réinitialisation des voitures non sélectionnées
    $query = $conn->query("SELECT idVoiture FROM voiture")->fetch_all(MYSQLI_ASSOC);
    $allIds = array_column($query, 'idVoiture');

    foreach ($allIds as $id) {
        if (!in_array($id, $ids)) {
            $stmt = $conn->prepare("UPDATE voiture SET estDansCarrousel = false WHERE idVoiture = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }
    
    if ($success) {
        header("Location: accueil.php?ok=1");
        exit();
    }
}

// Récupération des voitures dans le carrousel
$sql = "SELECT idVoiture FROM voiture WHERE estDansCarrousel = true";
$stmt = $conn->prepare($sql);
$stmt->execute();

$result = $stmt->get_result();
$carousel = $result ? array_column($result->fetch_all(MYSQLI_ASSOC), 'idVoiture') : [];
$msg = isset($_GET['ok']) ? "Carrousel mis à jour avec succès !" : '';

// Indexation des voitures
$voituresById = [];
foreach ($tab as $elem) {
    $voituresById[$elem['idVoiture']] = $elem;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Accueil / Carrousel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
    <style>
        .slot-preview {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            background: #0f1014;
            display: block;
        }

        .slot-preview-empty {
            width: 100%;
            height: 140px;
            border-radius: 8px;
            background: #0f1014;
            border: 2px dashed #2e3040;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
            font-size: 13px;
            text-align: center;
            padding: 10px;
        }

        .car-card-img {
            width: 100%;
            height: 110px;
            object-fit: cover;
            display: block;
            background: #111;
        }
    </style>
</head>

<body class="bg-dark text-white">

    <div class="d-flex flex-column flex-lg-row min-vh-100">
        <!-- Sidebar et Nav Mobile générés directement via admin_layout.php -->
        <?= get_sidebar('accueil'); ?>

        <!-- Contenu Principal -->
       <main class="admin-main flex-grow-1 p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 border-bottom border-secondary pb-3">
        <h1 class="h3 m-0">🎪 Page d'accueil — Carrousel</h1>
        <div class="badge bg-secondary p-2 fs-6">
            👤 <?= htmlspecialchars($_SESSION['admin_nom'] ?? 'Administrateur') ?>
        </div>
    </div>

            <?php if ($msg): ?>
                <div class="alert alert-success mb-4"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="row g-3 mb-4">
                    <?php for ($i = 1; $i <= $limit; $i++):
                        $selected = $carousel[$i - 1] ?? 0;
                        $selCar   = $voituresById[$selected] ?? null;
                    ?>
                        <div class="col-12 col-md-4">
                            <div class="card bg-dark text-white p-3 border-secondary h-100">
                                <h3 class="h6 text-uppercase text-muted mb-3">
                                    <span class="badge bg-danger rounded-circle me-1"><?= $i ?></span> Slide <?= $i ?>
                                </h3>

                                <?php if ($selCar): ?>
                                    <img class="slot-preview mb-3"
                                        src="../image/imagevoiture/<?= htmlspecialchars($selCar['imageVoiture']) ?>"
                                        alt="<?= htmlspecialchars($selCar['modele']) ?>"
                                        onerror="this.style.opacity='.15'" />
                                <?php else: ?>
                                    <div class="slot-preview-empty mb-3">Aucune voiture sélectionnée</div>
                                <?php endif; ?>

                                <select name="accueil<?= $i ?>" class="form-select bg-dark text-white border-secondary" onchange="updatePreview(this, <?= $i ?>)">
                                    <option value="">— Choisir une voiture —</option>
                                    <?php foreach ($tab as $elem): ?>
                                        <option
                                            value="<?= $elem['idVoiture'] ?>"
                                            data-img="<?= htmlspecialchars($elem['imageVoiture']) ?>"
                                            <?= ($selected == $elem['idVoiture']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($elem['modele']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="d-flex gap-2 align-items-center mb-5 flex-wrap">
                    <button class="btn btn-danger" type="submit">Enregistrer le carrousel</button>
                    <?php if (!empty($carousel)): ?>
                        <a href="../supercar_client/index.php" target="_blank" class="btn btn-secondary">Voir la page d'accueil</a>
                    <?php endif; ?>
                </div>
            </form>

            <h2 class="h5 text-muted mb-3">Toutes les voitures disponibles</h2>
            <div class="row g-3">
                <?php foreach ($tab as $elem):
                    $pos = array_search($elem['idVoiture'], $carousel);
                ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card bg-dark text-white border-secondary h-100 position-relative <?= $pos !== false ? 'border-danger' : '' ?>">
                            <?php if ($pos !== false): ?>
                                <span class="position-absolute top-0 end-0 m-2 badge bg-danger">Slide <?= $pos + 1 ?></span>
                            <?php endif; ?>
                            <img class="car-card-img card-img-top"
                                src="../image/imagevoiture/<?= htmlspecialchars($elem['imageVoiture']) ?>"
                                alt="<?= htmlspecialchars($elem['modele']) ?>"
                                onerror="this.style.opacity='.15'" />
                            <div class="card-body p-2 text-center">
                                <h4 class="h6 mb-0 text-truncate"><?= htmlspecialchars($elem['modele']) ?></h4>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updatePreview(sel, slotIndex) {
            const opt = sel.options[sel.selectedIndex];
            const img = opt.getAttribute('data-img');
            const slot = sel.closest('.card');
            let prev = slot.querySelector('.slot-preview, .slot-preview-empty');

            if (img) {
                if (!prev || prev.classList.contains('slot-preview-empty')) {
                    const el = document.createElement('img');
                    el.className = 'slot-preview mb-3';
                    el.onerror = () => {
                        el.style.opacity = '.15';
                    };
                    if (prev) prev.replaceWith(el);
                    else sel.before(el);
                    prev = el;
                }
                prev.src = '../image/imagevoiture/' + img;
            } else {
                if (prev && prev.classList.contains('slot-preview')) {
                    const empty = document.createElement('div');
                    empty.className = 'slot-preview-empty mb-3';
                    empty.textContent = 'Aucune voiture sélectionnée';
                    prev.replaceWith(empty);
                }
            }
        }
    </script>
</body>

</html>