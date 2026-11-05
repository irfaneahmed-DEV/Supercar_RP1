<?php
require_once 'admin_guard.php';
require_once '../connexion/dbconnexion.php';
require_once 'admin_layout.php';

// ── Récupération et validation de l'ID client ──
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header("Location: clients.php");
  exit();
}

// ── Actions POST ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Suppression du client
  if (isset($_POST['supprimer']) && isset($_POST['idClient'])) {

    $idSuppression = (int) $_POST['idClient'];

    if ($idSuppression > 0) {
      $stmt = $conn->prepare("DELETE FROM client WHERE idClient = ?");
      $stmt->bind_param("i", $idSuppression);
      $stmt->execute();
    }

    header("Location: clients.php");
    exit();
  }
  // Mise à jour du profil
  $nom       = trim($_POST['nom'] ?? '');
  $prenom    = trim($_POST['prenom'] ?? '');
  $email     = trim($_POST['email'] ?? '');
  $telephone = trim($_POST['telephone'] ?? '');
  $inscrit   = isset($_POST['inscrit']) ? 1 : 0;

  $stmt = $conn->prepare("UPDATE client SET nom=?, prenom=?, email=?, telephone=?, inscrit=? WHERE idClient=?");
  $stmt->bind_param("ssssii", $nom, $prenom, $email, $telephone, $inscrit, $id);
  $stmt->execute();

  header("Location: client_profil.php?id=$id&ok=1");
  exit();
}

// ── Chargement du client ──
$stmt = $conn->prepare("SELECT * FROM client WHERE idClient = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$client = $stmt->get_result()->fetch_assoc();

if (!$client) {
  header("Location: clients.php");
  exit();
}

$fullName = trim(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? ''));

$msgs = ['1' => 'Profil mis à jour !'];
$msg  = $msgs[$_GET['ok'] ?? ''] ?? '';

// ── Demandes d'essai du client (historique complet) ──
$stmt = $conn->prepare("
    SELECT d.idDemande, d.dateSouhaitee, d.dateDemande, d.status, v.modele, m.nom AS marque
    FROM demande_d_essai d
    JOIN voiture v ON v.idVoiture = d.idVoiture
    JOIN marque m  ON m.id_marque = v.id_marque
    WHERE d.idClient = ?
    ORDER BY d.dateDemande DESC
");
$stmt->bind_param("i", $id);
$stmt->execute();
$demandes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ── Messages du client ──
$stmt = $conn->prepare("SELECT idContact, message FROM contact WHERE idClient = ? ORDER BY idContact DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin – Profil Client</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="admin.css" />
</head>

<body>
  <div class="admin-layout d-flex flex-column flex-lg-row">
    <?= get_sidebar('clients') ?>

    <main class="admin-main flex-grow-1 p-3 p-md-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 border-bottom border-secondary pb-3">
        <div>
          <a href="clients.php" class="text-secondary text-decoration-none small d-block mb-1">← Retour aux clients</a>
          <h1 class="h3 m-0">👤 <?= $fullName !== '' ? htmlspecialchars($fullName) : 'Profil client' ?> <span class="text-secondary fs-6">#<?= $client['idClient'] ?></span></h1>
        </div>
        <div class="badge bg-secondary p-2 fs-6">👤 <?= htmlspecialchars($_SESSION['admin_nom']) ?></div>
      </div>

      <?php if ($msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="row g-4">
        <!-- Colonne informations / édition -->
        <div class="col-lg-4">
          <div class="card bg-dark border-secondary">
            <div class="card-body">
              <h2 class="h6 text-secondary text-uppercase mb-3">Informations</h2>
              <form method="POST">
                <div class="mb-3">
                  <label class="form-label">Prénom</label>
                  <input type="text" name="prenom" class="form-control bg-dark text-light border-secondary" value="<?= htmlspecialchars($client['prenom'] ?? '') ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">Nom</label>
                  <input type="text" name="nom" class="form-control bg-dark text-light border-secondary" value="<?= htmlspecialchars($client['nom'] ?? '') ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control bg-dark text-light border-secondary" value="<?= htmlspecialchars($client['email'] ?? '') ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">Téléphone</label>
                  <input type="text" name="telephone" class="form-control bg-dark text-light border-secondary" value="<?= htmlspecialchars($client['telephone'] ?? '') ?>">
                </div>
                <div class="form-check mb-3">
                  <input type="checkbox" name="inscrit" id="inscrit" class="form-check-input" value="1" <?= ($client['inscrit'] == 1) ? 'checked' : '' ?> disabled>
                  <label class="form-check-label" for="inscrit">Inscrit</label>
                </div>
                <div class="mb-3">
                  <label class="form-label text-secondary small">Date d'inscription</label>
                  <div><?= $client['dateInscription'] ? date('d/m/Y', strtotime($client['dateInscription'])) : '—' ?></div>
                </div>
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-danger">Enregistrer</button>
                  <button type="submit"
                    name="supprimer"
                    value="1"
                    class="btn btn-outline-danger"
                    onclick="return confirm('Supprimer ce client ?')">
                    Supprimer
                  </button>
                  <input type="hidden" name="idClient" value="<?= $client['idClient'] ?>">
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Colonne historique -->
        <div class="col-lg-8">
          <div class="card bg-dark border-secondary mb-4">
            <div class="card-body">
              <h2 class="h6 text-secondary text-uppercase mb-3">📋 Demandes d'essai (<?= count($demandes) ?>)</h2>
              <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Voiture</th>
                      <th>Date souhaitée</th>
                      <th>Demande faite le</th>
                      <th>Statut</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($demandes)): ?>
                      <tr>
                        <td colspan="5" class="text-center text-secondary py-4">Aucune demande</td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($demandes as $d): ?>
                        <?php
                        $s = strtolower($d['status']);
                        $badgeBg = str_contains($s, 'accept') ? 'bg-success' : (str_contains($s, 'refus') ? 'bg-danger' : (str_contains($s, 'attente') ? 'bg-warning text-dark' : 'bg-primary'));
                        ?>
                        <tr>
                          <td>#<?= $d['idDemande'] ?></td>
                          <td><?= htmlspecialchars($d['marque'] . ' ' . $d['modele']) ?></td>
                          <td><?= $d['dateSouhaitee'] ? date('d/m/Y', strtotime($d['dateSouhaitee'])) : '—' ?></td>
                          <td><?= $d['dateDemande'] ? date('d/m/Y', strtotime($d['dateDemande'])) : '—' ?></td>
                          <td><span class="badge <?= $badgeBg ?>"><?= htmlspecialchars($d['status']) ?></span></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="card bg-dark border-secondary">
            <div class="card-body">
              <h2 class="h6 text-secondary text-uppercase mb-3">✉️ Messages (<?= count($messages) ?>)</h2>
              <?php if (empty($messages)): ?>
                <p class="text-secondary mb-0">Aucun message reçu</p>
              <?php else: ?>
                <?php foreach ($messages as $m): ?>
                  <div class="border-bottom border-secondary py-2">
                    <p class="mb-0"><?= nl2br(htmlspecialchars($m['message'] ?? '')) ?></p>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
require_once 'admin_guard.php';
require_once '../connexion/dbconnexion.php';
require_once 'admin_layout.php';

// ── Récupération et validation de l'ID client ──
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header("Location: clients.php");
  exit();
}

// ── Mise à jour du profil ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom       = trim($_POST['nom'] ?? '');
  $prenom    = trim($_POST['prenom'] ?? '');
  $email     = trim($_POST['email'] ?? '');
  $telephone = trim($_POST['telephone'] ?? '');
  $inscrit   = isset($_POST['inscrit']) ? 1 : 0;

  $stmt = $conn->prepare("UPDATE client SET nom=?, prenom=?, email=?, telephone=?, inscrit=? WHERE idClient=?");
  $stmt->bind_param("ssssii", $nom, $prenom, $email, $telephone, $inscrit, $id);
  $stmt->execute();
  header("Location: client_profil.php?id=$id&ok=1");
  exit();
}

// ── Chargement du client ──
$stmt = $conn->prepare("SELECT * FROM client WHERE idClient = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$client = $stmt->get_result()->fetch_assoc();

if (!$client) {
  header("Location: clients.php");
  exit();
}

$fullName = trim(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? ''));

$msgs = ['1' => 'Profil mis à jour !'];
$msg  = $msgs[$_GET['ok'] ?? ''] ?? '';

// ── Demandes d'essai du client (historique complet) ──
$stmt = $conn->prepare("
    SELECT d.idDemande, d.dateSouhaitee, d.dateDemande, d.status, v.modele, m.nom AS marque
    FROM demande_d_essai d
    JOIN voiture v ON v.idVoiture = d.idVoiture
    JOIN marque m  ON m.id_marque = v.id_marque
    WHERE d.idClient = ?
    ORDER BY d.dateDemande DESC
");
$stmt->bind_param("i", $id);
$stmt->execute();
$demandes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ── Messages du client ──
$stmt = $conn->prepare("SELECT idContact, message FROM contact WHERE idClient = ? ORDER BY idContact DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>