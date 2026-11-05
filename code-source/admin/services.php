<?php
require_once 'admin_guard.php';
require_once '../connexion/dbconnexion.php';
require_once 'admin_layout.php';

// ── SUPPRESSION ──
if (isset($_POST['delete_id'])) {
  $id = intval($_POST['delete_id']);
  $conn->query("DELETE FROM beneficier WHERE idService = $id");
  $conn->query("DELETE FROM service WHERE idService = $id");
  header("Location: services.php?ok=supprime");
  exit();
}

// ── AJOUT ──
if (isset($_POST['action']) && $_POST['action'] === 'add') {
  $nom    = trim($_POST['nom']    ?? '');
  $libelle = trim($_POST['libelle'] ?? '');

  if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $img    = $_FILES['img'];
    $extension = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
    $accepted = ['png', 'jpeg', 'webp', 'jpg', 'avif'];

    if (in_array($extension, $accepted)) {
      $nomFichier = uniqid() . "." . $extension;
      $cheminDestination = "../image/" . $nomFichier;

      if (move_uploaded_file($img['tmp_name'], $cheminDestination)) {
        $stmt = $conn->prepare("INSERT INTO service (nom,libelle,img) VALUES (?,?,?)");
        $stmt->bind_param("sss", $nom, $libelle, $nomFichier);
        if ($stmt->execute()) {
          header("Location: services.php?ok=ajoute");
          exit();
        } else {
          $msg = "Erreur SQL : " . $conn->error;
          $msgType = "danger";
        }
      } else {
        $msg = "Erreur lors du déplacement du fichier.";
        $msgType = "danger";
      }
    } else {
      $msg = "Extension non autorisée.";
      $msgType = "danger";
    }
  } else {
    $msg = "Veuillez sélectionner une image valide.";
    $msgType = "danger";
  }
}

// ── MODIFICATION ──
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
  $id      = intval($_POST['idService']);
  $nom     = trim($_POST['nom'] ?? '');
  $libelle = trim($_POST['libelle'] ?? '');

  if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $img = $_FILES['img'];
    $extension = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
    $accepted  = ['png', 'jpeg', 'webp', 'jpg', 'avif'];

    if (in_array($extension, $accepted)) {
      $nomFichier = uniqid() . "." . $extension;
      $cheminDestination = "../image/" . $nomFichier;

      if (move_uploaded_file($img['tmp_name'], $cheminDestination)) {
        $stmt = $conn->prepare("UPDATE service SET nom=?, libelle=?, img=? WHERE idService=?");
        $stmt->bind_param("sssi", $nom, $libelle, $nomFichier, $id);
        if ($stmt->execute()) {
          header("Location: services.php?ok=modifie");
          exit();
        } else {
          $msg = "Erreur SQL : " . $conn->error;
          $msgType = 'danger';
        }
      } else {
        $msg = "Erreur lors du déplacement du fichier.";
        $msgType = "danger";
      }
    } else {
      $msg = "Extension non autorisée.";
      $msgType = "danger";
    }
  } else {
    $stmt = $conn->prepare("UPDATE service SET nom=?, libelle=? WHERE idService=?");
    $stmt->bind_param("ssi", $nom, $libelle, $id);
    $stmt->execute();
    header("Location: services.php?ok=modifie");
    exit();
  }
}
// Initialisation des variables pour éviter l'avertissement PHP
$msg = $msg ?? '';
$msgType = $msgType ?? 'success';

$msgs = ['ajoute' => 'Service ajouté !', 'modifie' => 'Service modifié !', 'supprime' => 'Service supprimé !'];
if (isset($_GET['ok']) && isset($msgs[$_GET['ok']])) {
    $msg = $msgs[$_GET['ok']];
}

$services = $conn->query("SELECT * FROM service ORDER BY idService")->fetch_all(MYSQLI_ASSOC);

$editSvc = null;
if (isset($_GET['edit'])) {
  $eid = intval($_GET['edit']);
  foreach ($services as $s) {
    if ($s['idService'] == $eid) {
      $editSvc = $s;
      break;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin – Services</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="admin.css">
  <style>
    .svc-img {
      width: 80px;
      height: 50px;
      object-fit: cover;
      border-radius: 6px;
    }
  </style>
</head>

<body>
  <div class="admin-layout d-flex flex-column flex-md-row">
    <?= get_sidebar('services') ?>

    <main class="admin-main flex-grow-1 p-3 p-md-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 border-bottom pb-3">
        <h1 class="h3 m-0">🔧 Gestion des services</h1>
        <div class="badge bg-secondary p-2 fs-6">👤 <?= htmlspecialchars($_SESSION['admin_nom']) ?></div>
      </div>

      <?php if (!empty($msg)): ?>
        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <span class="text-secondary"><?= count($services) ?> service(s)</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">➕ Ajouter un service</button>
      </div>

      <div class="table-responsive card bg-dark border-secondary">
        <table class="table table-dark table-hover table-striped align-middle mb-0">
          <thead>
            <tr>
              <th style="width: 100px;">Image</th>
              <th>Nom</th>
              <th>Description</th>
              <th style="width: 150px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($services as $s): ?>
              <tr>
                <td>
                  <img class="svc-img bg-black border border-secondary"
                    src="<?= htmlspecialchars(str_starts_with($s['img'], 'http') ? $s['img'] : '../image/' . $s['img']) ?>"
                    alt="<?= htmlspecialchars($s['nom']) ?>"
                    onerror="this.style.opacity='.15'" />
                </td>
                <td class="fw-bold"><?= htmlspecialchars($s['nom']) ?></td>
                <td><?= htmlspecialchars($s['libelle']) ?></td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <a href="?edit=<?= $s['idService'] ?>" class="btn btn-outline-light">✏️</a>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce service ?')">
                      <input type="hidden" name="delete_id" value="<?= $s['idService'] ?>" />
                      <button class="btn btn-outline-danger">🗑️</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- MODAL AJOUT -->
  <div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-light border-secondary">
        <div class="modal-header border-secondary">
          <h5 class="modal-title">➕ Ajouter un service</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <input type="hidden" name="action" value="add" />
            <div class="mb-3">
              <label class="form-label">Nom du service</label>
              <input type="text" name="nom" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <input type="text" name="libelle" class="form-control" />
            </div>
            <div class="mb-3">
              <label class="form-label">Image</label>
              <input type="file" name="img" class="form-control" required />
            </div>
          </div>
          <div class="modal-footer border-secondary">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Ajouter</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL EDIT -->
  <?php if ($editSvc): ?>
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-light border-secondary">
          <div class="modal-header border-secondary">
            <h5 class="modal-title">✏️ Modifier le service</h5>
            <a href="services.php" class="btn-close"></a>
          </div>
          <form method="POST" enctype="multipart/form-data">
            <div class="modal-body">
              <input type="hidden" name="action" value="edit" />
              <input type="hidden" name="idService" value="<?= $editSvc['idService'] ?>" />
              <div class="mb-3">
                <label class="form-label">Nom du service</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($editSvc['nom']) ?>" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <input type="text" name="libelle" class="form-control" value="<?= htmlspecialchars($editSvc['libelle']) ?>" />
              </div>
              <div class="mb-3">
                <label class="form-label">Changer l'image (optionnel)</label>
                <input type="file" name="img" class="form-control" />
              </div>
            </div>
            <div class="modal-footer border-secondary">
              <a href="services.php" class="btn btn-secondary">Annuler</a>
              <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <?php if ($editSvc): ?>
    <script>
      new bootstrap.Modal(document.getElementById('modalEdit')).show();
    </script>
  <?php endif; ?>
</body>
</html>