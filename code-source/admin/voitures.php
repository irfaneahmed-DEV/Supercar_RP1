<?php
require_once 'admin_guard.php';
require_once '../connexion/dbconnexion.php';
require_once 'admin_layout.php';

const IMG_DIR = __DIR__ . '/../image/imagevoiture/';

/* ───────────── Helpers ───────────── */
/** @var Mysqli $conn */

function h($v): string
{
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function redirect(int $code): void
{
  header("Location: voitures.php?ok=" . $code);
  exit();
}

// Enregistre une image uploadée (types autorisés uniquement). Retourne le nom du fichier ou null.
function saveVoitureImage(array $file): ?string
{
  if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
    return null;
  }
  $info = @getimagesize($file['tmp_name']);
  $allowed = [
    IMAGETYPE_JPEG => 'jpg',
    IMAGETYPE_PNG  => 'png',
    IMAGETYPE_GIF  => 'gif',
    IMAGETYPE_WEBP => 'webp',
  ];
  if ($info === false || !isset($allowed[$info[2]])) {
    return null;
  }
  $name = bin2hex(random_bytes(8)) . '.' . $allowed[$info[2]];
  return move_uploaded_file($file['tmp_name'], IMG_DIR . $name) ? $name : null;
}

function deleteVoitureImage(?string $name): void
{
  if (!empty($name)) {
    @unlink(IMG_DIR . basename($name));
  }
}

/* ───────────── Messages ───────────── */

$msgs = [
  '1' => ['Voiture ajoutée avec succès !', 'success'],
  '2' => ['Voiture modifiée avec succès !', 'success'],
  '3' => ['Voiture supprimée !', 'success'],
  '4' => ["Erreur lors du traitement de l'image (formats : JPG, PNG, GIF, WEBP, 5 Mo max).", 'danger'],
  '5' => ['Marque ajoutée avec succès !', 'success'],
  '6' => ['Marque modifiée avec succès !', 'success'],
  '7' => ['Marque supprimée !', 'success'],
  '8' => ['Suppression impossible : cette marque est utilisée par des voitures.', 'danger'],
  '9' => ['Veuillez remplir tous les champs obligatoires.', 'danger'],
];
[$msg, $msgType] = $msgs[(string)($_GET['ok'] ?? '')] ?? ['', 'success'];

/* ───────────── Traitement des formulaires (POST uniquement) ───────────── */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  try {
    switch ($action) {
/** @var Mysqli $conn */
      // ── Suppression d'une voiture ──
      case 'supprimer_voiture':
        $id = (int)($_POST['idVoiture'] ?? 0);

        $stmt = $conn->prepare("SELECT imageVoiture FROM voiture WHERE idVoiture = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM voiture WHERE idVoiture = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        if ($row) {
          deleteVoitureImage($row['imageVoiture']);
        }
        redirect(3);
        break;

      // ── Ajout / modification d'une voiture ──
      case 'save_voiture':
        $idVoiture        = (int)($_POST['idVoiture'] ?? 0);
        $id_marque        = (int)($_POST['id_marque'] ?? 0);
        $modele           = trim($_POST['modele'] ?? '');
        $annee            = trim($_POST['annee'] ?? '');
        $puissance        = trim($_POST['puissance'] ?? '');
        $prix             = trim($_POST['prix'] ?? '');
        $typeCarburant    = trim($_POST['typeCarburant'] ?? '');
        $estDansCarrousel = isset($_POST['estDansCarrousel']) ? 1 : 0;

        if ($id_marque <= 0 || $modele === '') {
          redirect(9);
        }

        // Nouvelle image (facultative)
        $newImage = null;
        if (
          isset($_FILES['imageVoiture'])
          && $_FILES['imageVoiture']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
          $newImage = saveVoitureImage($_FILES['imageVoiture']);
          if ($newImage === null) {
            redirect(4);
          }
        }

        if ($idVoiture > 0) {
          // Modification
          $oldImage = null;
          if ($newImage !== null) {
            $stmt = $conn->prepare("SELECT imageVoiture FROM voiture WHERE idVoiture = ?");
            $stmt->bind_param("i", $idVoiture);
            $stmt->execute();
            $r = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $oldImage = $r['imageVoiture'] ?? null;

            $stmt = $conn->prepare("
              UPDATE voiture
              SET id_marque=?, modele=?, annee=?, puissance=?, prix=?,
                  typeCarburant=?, estDansCarrousel=?, imageVoiture=?
              WHERE idVoiture=?
            ");
            $stmt->bind_param(
              "isssssisi",
              $id_marque, $modele, $annee, $puissance, $prix,
              $typeCarburant, $estDansCarrousel, $newImage, $idVoiture
            );
          } else {
            // Pas de nouvelle image : on ne touche pas à la colonne imageVoiture
            $stmt = $conn->prepare("
              UPDATE voiture
              SET id_marque=?, modele=?, annee=?, puissance=?, prix=?,
                  typeCarburant=?, estDansCarrousel=?
              WHERE idVoiture=?
            ");
            $stmt->bind_param(
              "isssssii",
              $id_marque, $modele, $annee, $puissance, $prix,
              $typeCarburant, $estDansCarrousel, $idVoiture
            );
          }
          $stmt->execute();
          $stmt->close();

          deleteVoitureImage($oldImage);
          redirect(2);
        }

        // Ajout
        $imageName = $newImage ?? '';
        $stmt = $conn->prepare("
          INSERT INTO voiture
            (id_marque, modele, annee, puissance, prix, typeCarburant, estDansCarrousel, imageVoiture)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
          "isssssis",
          $id_marque, $modele, $annee, $puissance, $prix,
          $typeCarburant, $estDansCarrousel, $imageName
        );
        $stmt->execute();
        $stmt->close();
        redirect(1);
        break;

      // ── Ajout d'une marque ──
      case 'add_marque':
        $nom = trim($_POST['nom_marque'] ?? '');
        if ($nom === '') {
          redirect(9);
        }
        $stmt = $conn->prepare("INSERT INTO marque (nom) VALUES (?)");
        $stmt->bind_param("s", $nom);
        $stmt->execute();
        $stmt->close();
        redirect(5);
        break;

      // ── Modification d'une marque ──
      case 'edit_marque':
        $idMarque = (int)($_POST['id_marque'] ?? 0);
        $nom      = trim($_POST['nom_marque'] ?? '');
        if ($idMarque <= 0 || $nom === '') {
          redirect(9);
        }
        $stmt = $conn->prepare("UPDATE marque SET nom = ? WHERE id_marque = ?");
        $stmt->bind_param("si", $nom, $idMarque);
        $stmt->execute();
        $stmt->close();
        redirect(6);
        break;

      // ── Suppression d'une marque ──
      case 'delete_marque':
        $idMarque = (int)($_POST['id_marque'] ?? 0);

        $stmt = $conn->prepare("SELECT COUNT(*) AS n FROM voiture WHERE id_marque = ?");
        $stmt->bind_param("i", $idMarque);
        $stmt->execute();
        $n = (int)$stmt->get_result()->fetch_assoc()['n'];
        $stmt->close();

        if ($n > 0) {
          redirect(8);
        }

        $stmt = $conn->prepare("DELETE FROM marque WHERE id_marque = ?");
        $stmt->bind_param("i", $idMarque);
        $stmt->execute();
        $stmt->close();
        redirect(7);
        break;
    }
  } catch (mysqli_sql_exception $e) {
    error_log('voitures.php SQL error: ' . $e->getMessage());
    $msg     = "Une erreur est survenue lors de l'enregistrement.";
    $msgType = 'danger';
  }
}

/* ───────────── Données : Marques et Voitures ───────────── */

$marques  = $conn->query("SELECT nom, id_marque FROM marque ORDER BY nom ASC")->fetch_all(MYSQLI_ASSOC);
$voitures = $conn->query("
    SELECT v.*, m.nom AS marque_nom
    FROM voiture v
    JOIN marque m ON m.id_marque = v.id_marque
    ORDER BY v.idVoiture DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin – Gestion des Voitures</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="admin.css" />
</head>

<body>
  <div class="admin-layout d-flex flex-column flex-lg-row">
    <?= get_sidebar('voitures') ?>

    <main class="admin-main flex-grow-1 p-3 p-md-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 border-bottom border-secondary pb-3">
        <h1 class="h3 m-0">🚗 Gestion des Voitures</h1>
        <div>
          <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalVoiture" onclick="resetForm()">
            + Ajouter une voiture
          </button>
          <button type="button" class="btn grey" onclick="openOverlay('modalAddMarque')">
            ➕ Gérer les marques
          </button>
        </div>
      </div>

      <?php if (!empty($msg)): ?>
        <div class="alert alert-<?= h($msgType) ?> alert-dismissible fade show" role="alert">
          <?= h($msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="table-responsive card bg-dark border-secondary">
        <table class="table table-dark table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Image</th>
              <th>Marque & Modèle</th>
              <th>Année</th>
              <th>Puissance</th>
              <th>Prix</th>
              <th>Carburant</th>
              <th>Carrousel</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($voitures)): ?>
              <tr>
                <td colspan="9" class="text-center text-secondary py-4">Aucune voiture disponible</td>
              </tr>
            <?php else: ?>
              <?php foreach ($voitures as $v): ?>
                <tr>
                  <td>#<?= (int)$v['idVoiture'] ?></td>
                  <td>
                    <?php if (!empty($v['imageVoiture'])): ?>
                      <img src="../image/imagevoiture/<?= h($v['imageVoiture']) ?>" alt="Voiture" style="width: 60px; height: 40px; object-fit: cover;" class="rounded" onerror="this.src='../image/default.jpg'" />
                    <?php else: ?>
                      <span class="text-secondary">—</span>
                    <?php endif; ?>
                  </td>
                  <td class="fw-bold"><?= h($v['marque_nom'] . ' ' . $v['modele']) ?></td>
                  <td><?= h($v['annee'] ?? '—') ?></td>
                  <td><?= h($v['puissance'] ?? '—') ?></td>
                  <td><?= number_format((float)($v['prix'] ?? 0), 0, ',', ' ') ?> RS</td>
                  <td><?= h($v['typeCarburant'] ?? '—') ?></td>
                  <td>
                    <?php if ($v['estDansCarrousel'] == 1): ?>
                      <span class="badge bg-success">Oui</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Non</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <button type="button" class="btn btn-outline-warning btn-sm"
                      onclick="editerVoiture(<?= h(json_encode($v)) ?>)">Éditer</button>

                    <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce véhicule ?')">
                      <input type="hidden" name="action" value="supprimer_voiture">
                      <input type="hidden" name="idVoiture" value="<?= (int)$v['idVoiture'] ?>">
                      <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Modal Ajout / Edition d'une voiture -->
  <div class="modal fade" id="modalVoiture" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-light border-secondary">
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="save_voiture">
          <div class="modal-header border-secondary">
            <h5 class="modal-title" id="modalTitle">Ajouter une voiture</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="idVoiture" id="idVoiture" value="0">

            <div class="mb-3">
              <label class="form-label">Marque</label>
              <select name="id_marque" id="id_marque" class="form-select bg-dark text-light border-secondary" required>
                <?php foreach ($marques as $m): ?>
                  <option value="<?= (int)$m['id_marque'] ?>"><?= h($m['nom']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Modèle</label>
              <input type="text" name="modele" id="modele" class="form-control bg-dark text-light border-secondary" required>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label">Année</label>
                <input type="text" name="annee" id="annee" class="form-control bg-dark text-light border-secondary">
              </div>
              <div class="col-6">
                <label class="form-label">Puissance</label>
                <input type="text" name="puissance" id="puissance" class="form-control bg-dark text-light border-secondary">
              </div>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label">Prix (RS)</label>
                <input type="text" name="prix" id="prix" class="form-control bg-dark text-light border-secondary">
              </div>
              <div class="col-6">
                <label class="form-label">Carburant</label>
                <input type="text" name="typeCarburant" id="typeCarburant" class="form-control bg-dark text-light border-secondary">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Image du véhicule</label>
              <input type="file" name="imageVoiture" id="imageVoiture" accept="image/jpeg,image/png,image/gif,image/webp" class="form-control bg-dark text-light border-secondary">
              <div class="form-text" id="imageHelp">JPG, PNG, GIF ou WEBP – 5 Mo max.</div>
            </div>

            <div class="form-check mb-3">
              <input type="checkbox" name="estDansCarrousel" id="estDansCarrousel" class="form-check-input" value="1">
              <label class="form-check-label" for="estDansCarrousel">Mettre en avant sur le carrousel</label>
            </div>
          </div>
          <div class="modal-footer border-secondary">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-danger">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ── MODAL MARQUES (ajout + liste) ── -->
  <div class="modal-overlay" id="modalAddMarque">
    <div class="row marque-displayer d-flex align-items-start">
      <div class="col-lg-6 col-md-6 col-sm-12">
        <div class="modal-box">
          <button type="button" class="modal-close" onclick="closeOverlay('modalAddMarque')">✕</button>
          <h2>➕ Ajouter une marque</h2>
          <form method="POST">
            <input type="hidden" name="action" value="add_marque" />
            <div class="form-row">
              <label>Nom de la marque</label>
              <input type="text" name="nom_marque" placeholder="Ex : Ferrari" required />
            </div>
            <div style="display:flex;gap:10px;margin-top:8px">
              <button class="btn primary" type="submit">Ajouter</button>
              <button class="btn grey" type="button" onclick="closeOverlay('modalAddMarque')">Annuler</button>
            </div>
          </form>
        </div>
      </div>

      <div class="col-lg-6 col-md-6 col-sm-12">
        <div class="modal-box overflow-auto" style="height:350px">
          <h2>Liste des marques</h2>
          <table class="table table-dark">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Nom</th>
                <th scope="col">Modifier</th>
                <th scope="col">Supprimer</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($marques as $i => $marque): ?>
                <tr>
                  <th scope="row"><?= $i + 1 ?></th>
                  <td><?= h($marque['nom']) ?></td>
                  <td>
                    <button type="button" class="btn btn-primary btn-sm"
                      data-id="<?= (int)$marque['id_marque'] ?>"
                      data-nom="<?= h($marque['nom']) ?>"
                      onclick="ouvrirModifMarque(this)">Modifier</button>
                  </td>
                  <td>
                    <form method="POST" onsubmit="return confirm('Supprimer cette marque ?')">
                      <input type="hidden" name="action" value="delete_marque">
                      <input type="hidden" name="id_marque" value="<?= (int)$marque['id_marque'] ?>">
                      <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- ── MODAL MODIFICATION MARQUE ── -->
  <div class="modal-overlay" id="modalModifyMarque">
    <div class="row marque-displayer d-flex align-items-start">
      <div class="col-12">
        <div class="modal-box">
          <button type="button" class="modal-close" onclick="closeOverlay('modalModifyMarque')">✕</button>
          <h2>✏️ Modifier une marque</h2>
          <form method="POST">
            <input type="hidden" name="action" value="edit_marque" />
            <input type="hidden" name="id_marque" id="edit_id_marque" value="" />
            <div class="form-row">
              <label>Nom de la marque</label>
              <input type="text" name="nom_marque" id="edit_nom_marque" placeholder="Ex : Ferrari" required />
            </div>
            <div style="display:flex;gap:10px;margin-top:8px">
              <button class="btn primary" type="submit">Enregistrer</button>
              <button class="btn grey" type="button" onclick="closeOverlay('modalModifyMarque')">Annuler</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function openOverlay(id) {
      document.getElementById(id).classList.add('open');
    }

    function closeOverlay(id) {
      document.getElementById(id).classList.remove('open');
    }

    function ouvrirModifMarque(btn) {
      document.getElementById('edit_id_marque').value = btn.dataset.id;
      document.getElementById('edit_nom_marque').value = btn.dataset.nom;
      closeOverlay('modalAddMarque');
      openOverlay('modalModifyMarque');
    }

    function resetForm() {
      document.getElementById('modalTitle').innerText = "Ajouter une voiture";
      document.getElementById('idVoiture').value = "0";
      document.getElementById('modele').value = "";
      document.getElementById('annee').value = "";
      document.getElementById('puissance').value = "";
      document.getElementById('prix').value = "";
      document.getElementById('typeCarburant').value = "";
      document.getElementById('estDansCarrousel').checked = false;
      document.getElementById('imageVoiture').value = "";
      document.getElementById('imageHelp').innerText = "JPG, PNG, GIF ou WEBP – 5 Mo max.";
    }

    function editerVoiture(data) {
      document.getElementById('modalTitle').innerText = "Modifier la voiture";
      document.getElementById('idVoiture').value = data.idVoiture;
      document.getElementById('id_marque').value = data.id_marque;
      document.getElementById('modele').value = data.modele || "";
      document.getElementById('annee').value = data.annee || "";
      document.getElementById('puissance').value = data.puissance || "";
      document.getElementById('prix').value = data.prix || "";
      document.getElementById('typeCarburant').value = data.typeCarburant || "";
      document.getElementById('estDansCarrousel').checked = data.estDansCarrousel == 1;
      document.getElementById('imageVoiture').value = "";
      document.getElementById('imageHelp').innerText = data.imageVoiture ?
        "Laisser vide pour conserver l'image actuelle." :
        "JPG, PNG, GIF ou WEBP – 5 Mo max.";

      new bootstrap.Modal(document.getElementById('modalVoiture')).show();
    }
  </script>
</body>

</html>