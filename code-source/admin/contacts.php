<?php
require_once 'admin_guard.php';
require_once '../connexion/dbconnexion.php';
require_once 'admin_layout.php';

// ── Suppression d'un message ──
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM contact WHERE idContact = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: contacts.php?ok=2");
    exit();
}

$msgs = ['1' => 'Message mis à jour !', '2' => 'Message supprimé.'];
$msg  = $msgs[$_GET['ok'] ?? ''] ?? '';

// ── Liste des messages de contact joints avec la table client ──
$messages = $conn->query("
    SELECT ct.idContact, ct.message,
           c.idClient, c.nom, c.prenom, c.email, c.telephone
    FROM contact ct
    JOIN client c ON c.idClient = ct.idClient
    ORDER BY ct.idContact DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin – Messages de Contact</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body>
  <div class="admin-layout d-flex flex-column flex-lg-row">
    <?= get_sidebar('contacts') ?>

    <main class="admin-main flex-grow-1 p-3 p-md-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 border-bottom border-secondary pb-3">
        <h1 class="h3 m-0">✉️ Messages de Contact</h1>
        <div class="badge bg-secondary p-2 fs-6">👤 <?= htmlspecialchars($_SESSION['admin_nom']) ?></div>
      </div>

      <?php if ($msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="table-responsive card bg-dark border-secondary">
        <table class="table table-dark table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Client</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th>Message</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($messages)): ?>
              <tr>
                <td colspan="6" class="text-center text-secondary py-4">Aucun message reçu</td>
              </tr>
            <?php else: ?>
              <?php foreach ($messages as $m): ?>
                <tr>
                  <td>#<?= $m['idContact'] ?></td>
                  <td class="fw-bold"><?= htmlspecialchars(trim(($m['prenom'] ?? '') . ' ' . ($m['nom'] ?? ''))) ?></td>
                  <td><?= htmlspecialchars($m['email'] ?? '—') ?></td>
                  <td><?= htmlspecialchars($m['telephone'] ?? '—') ?></td>
                  <td style="max-width: 350px;" class="text-wrap">
                    <?= nl2br(htmlspecialchars($m['message'] ?? '')) ?>
                  </td>
                  <td>
                    <a href="contacts.php?action=supprimer&id=<?= $m['idContact'] ?>"
                       class="btn btn-outline-danger btn-sm"
                       onclick="return confirm('Supprimer ce message ?')">
                       Supprimer
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>