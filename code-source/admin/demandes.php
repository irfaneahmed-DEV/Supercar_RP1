<?php
require_once 'admin_guard.php';
require_once '../connexion/dbconnexion.php';
require_once 'admin_layout.php';

// ── Mise à jour du statut ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ids    = $_POST['demande_essai'] ?? [];
  $status = $_POST['status'] ?? null;
  $ok = '1';
  foreach ($ids as $id) {
    if ($status != null) {
      if ($status == 'terminée') {
        if (VerifierStatus($id)) {
          updateStatus($status, $id);
        } else {
          $ok = '3';
        }
      } else {
        updateStatus($status, $id);
      }
    }
  }
  header("Location: demandes.php?ok=$ok");
  exit();
}

function updateStatus(string $status, int $id)
{
  include('../connexion/dbconnexion.php');
  $stmt = $conn->prepare("UPDATE demande_d_essai SET status=? WHERE idDemande=?");
  $stmt->bind_param("si", $status, $id);
  if (!$stmt->execute()) {
    error_log("SQL error: " . $conn->error);
  }
}

function VerifierStatus(int $id)
{
  include('../connexion/dbconnexion.php');
  $sql = 'SELECT status FROM demande_d_essai WHERE idDemande = ?';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  if ($stmt->execute()) {
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && $row['status'] === 'acceptée') {
      return true;
    }
  }
  return false;
}

$msgs = ['1' => 'Statut mis à jour !', '2' => 'Demande supprimée.', '3' => "Les demandes terminées doivent d'abord être acceptées"];
$msg  = $msgs[$_GET['ok'] ?? ''] ?? '';

/** @var Mysqli $conn */

$demandes = $conn->query("
    SELECT d.idDemande, d.dateSouhaitee, d.dateDemande, d.status,
           c.idClient, c.nom, c.prenom, c.email, d.heureDemande,
           v.modele, m.nom AS marque
    FROM demande_d_essai d
    JOIN client c ON c.idClient = d.idClient
    JOIN voiture v ON v.idVoiture = d.idVoiture 
    JOIN marque m ON m.id_marque = v.id_marque 
    WHERE d.status != 'terminée'
    ORDER BY d.dateDemande DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin – Demandes</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="admin.css" />
</head>

<body>
  <div class="admin-layout d-flex flex-column flex-lg-row">
    <?= get_sidebar("demandes") ?>

    <main class="admin-main flex-grow-1 p-3 p-md-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 border-bottom border-secondary pb-3">
        <h1 class="h3 m-0">📋 Demandes d'essai</h1>
        <div class="badge bg-secondary p-2 fs-6">👤 <?= htmlspecialchars($_SESSION['admin_nom']) ?></div>
      </div>

      <?php if ($msg): ?>
        <div class="alert alert-<?= ($_GET['ok'] ?? '') === '3' ? 'warning' : 'success' ?> alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
          <div class="d-flex flex-wrap gap-2">
            <?php
            $actions = [
              'Accepter' => 'acceptée',
              'Refuser'  => 'refusée',
              'Terminer' => 'terminée',
              'Attente'  => 'en attente'
            ];
            foreach ($actions as $action => $status):
            ?>
              <button name="status" value="<?= $status ?>" class="btn btn-outline-light btn-sm" type="submit">
                <?= ucfirst($action) ?>
              </button>
            <?php endforeach; ?>
          </div>
          <button type="button" onclick="cocher()" class="btn btn-secondary btn-sm">Tout cocher / décocher</button>
        </div>

        <div class="table-responsive card bg-dark border-secondary">
          <table class="table table-dark table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Client</th>
                <th>Email</th>
                <th>Voiture</th>
                <th>Date souhaitée</th>
                <th>Heure souhaitée</th>
                <th>Demande faite le</th>
                <th>Sélection</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($demandes)): ?>
                <tr>
                  <td colspan="8" class="text-center text-secondary py-4">Aucune demande</td>
                </tr>
              <?php else: ?>
                <?php foreach ($demandes as $d): ?>
                  <tr>
                    <td>#<?= $d['idDemande'] ?></td>
                    <td><?= htmlspecialchars(trim(($d['prenom'] ?? '') . ' ' . ($d['nom'] ?? ''))) ?></td>
                    <td><?= htmlspecialchars($d['email'] ?? '—') ?></td>
                    <td><?= htmlspecialchars(($d['marque'] ?? '') . ' ' . ($d['modele'] ?? '')) ?></td>
                    <td><?= $d['dateSouhaitee'] ? date('d/m/Y', strtotime($d['dateSouhaitee'])) : '—' ?></td>
                    <td><?= htmlspecialchars($d['heureDemande'] ?? '—')  ?></td>
                    <td><?= $d['dateDemande'] ? date('d/m/Y', strtotime($d['dateDemande'])) : '—' ?></td>
                    <td>
                      <input type="checkbox" class="form-check-input case" name="demande_essai[]" value="<?= $d['idDemande'] ?>">
                    </td>
                    <?php
                    $statusText = strtolower(htmlspecialchars($d['status']));
                    $badgeClass = 'bg-secondary';
                    switch ($statusText) {
                      case 'acceptée':
                        $badgeClass = 'bg-success';
                        break;
                      case 'refusée':
                        $badgeClass = 'bg-danger';
                        break;
                      case 'en attente':
                        $badgeClass = 'bg-warning text-dark';
                        break;
                      case 'terminée':
                        $badgeClass = 'bg-primary';
                        break;
                    }
                    ?>
                    <td>
                      <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($d['status']) ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </form>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function cocher() {
      const casesACochees = Array.from(document.querySelectorAll('.case'));
      if (casesACochees.length === 0) return;
      
      const allChecked = casesACochees.every((checkbox) => checkbox.checked);
      casesACochees.forEach((checkbox) => {
        checkbox.checked = !allChecked;
      });
    }
  </script>
</body>

</html>