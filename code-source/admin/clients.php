<?php
require_once 'admin_guard.php';
require_once '../connexion/dbconnexion.php';
require_once 'admin_layout.php';

// Les erreurs SQL lèvent des exceptions (nécessaire pour la transaction ci-dessous)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function h(String $v): string
{
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

/* ───────────── Suppression d'un client (POST uniquement) ───────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_client'])) {
  $id = (int)$_POST['delete_client'];

  try {
    $conn->begin_transaction();
    // Noms de tables fixes (jamais issus de l'utilisateur)
    foreach (['contact', 'demande_d_essai', 'login', 'client'] as $table) {
      $stmt = $conn->prepare("DELETE FROM $table WHERE idClient = ?");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $stmt->close();
    }
    $conn->commit();
    $_SESSION['admin_msg']      = "Client supprimé avec succès.";
    $_SESSION['admin_msg_type'] = 'success';
  } catch (mysqli_sql_exception $e) {
    $conn->rollback();
    error_log('clients.php delete error: ' . $e->getMessage());
    $_SESSION['admin_msg']      = "Erreur lors de la suppression du client.";
    $_SESSION['admin_msg_type'] = 'danger';
  }

  header("Location: clients.php");
  exit();
}

$msg     = $_SESSION['admin_msg'] ?? '';
$msgType = $_SESSION['admin_msg_type'] ?? 'success';
unset($_SESSION['admin_msg'], $_SESSION['admin_msg_type']);

/* ───────────── Recherche ───────────── */
$search = '';
if (isset($_GET['recherche']) && is_string($_GET['recherche'])) {
  $search = trim($_GET['recherche']);
}

$where  = '';
$types  = '';
$params = [];
if ($search !== '') {
  // On échappe % et _ pour qu'ils soient traités comme du texte
  $like   = '%' . addcslashes($search, '\\%_') . '%';
  $where  = "WHERE c.email LIKE ?";
  $types  = 's';
  $params = [$like];
}

/* ───────────── Pagination ───────────── */
$countStmt = $conn->prepare("SELECT COUNT(*) AS nbClients FROM client c $where");
if ($params) {
  $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$nombreDeClients = (int)$countStmt->get_result()->fetch_assoc()['nbClients'];
$countStmt->close();

$limit         = 8;
$nombreDePages = max(1, (int)ceil($nombreDeClients / $limit));

$page = 1;
if (isset($_GET['page']) && is_string($_GET['page']) && ctype_digit($_GET['page'])) {
  $page = (int)$_GET['page'];
}
$page   = min(max(1, $page), $nombreDePages); // jamais au-delà de la dernière page
$offset = ($page - 1) * $limit;

/* ───────────── Données ───────────── */
$stmt = $conn->prepare("
  SELECT c.idClient, c.nom, c.prenom, c.email, c.telephone, c.dateInscription, c.inscrit,
         COUNT(DISTINCT d.idDemande)  AS nb_demandes,
         COUNT(DISTINCT co.idContact) AS nb_messages
  FROM client c
  LEFT JOIN demande_d_essai d ON d.idClient = c.idClient
  LEFT JOIN contact co        ON co.idClient = c.idClient
  $where
  GROUP BY c.idClient
  ORDER BY c.dateInscription DESC
  LIMIT ? OFFSET ?
");
$bindTypes  = $types . 'ii';
$bindParams = array_merge($params, [$limit, $offset]);
$stmt->bind_param($bindTypes, ...$bindParams);
$stmt->execute();
$clients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// URL d'une page donnée (conserve la recherche)
$pageUrl = fn(int $p): string => '?' . http_build_query(array_merge($_GET, ['page' => $p]));

// Fenêtre de pagination : page-2 … page+2
$debut = max(1, $page - 2);
$fin   = min($nombreDePages, $page + 2);
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin – Clients</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome (pour l'icône de recherche) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="admin.css" />
</head>

<body>
  <div class="admin-layout d-flex flex-column flex-lg-row">
    <?= get_sidebar('clients') ?>

    <main class="admin-main flex-grow-1 p-3 p-md-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 border-bottom border-secondary pb-3">
        <h1 class="h3 m-0">👥 Gestion des Clients</h1>
        <div class="badge bg-secondary p-2 fs-6">👤 <?= h($_SESSION['admin_nom'] ?? '') ?></div>
      </div>

      <?php if ($msg): ?>
        <div class="alert alert-<?= h($msgType) ?> alert-dismissible fade show" role="alert">
          <?= h($msg) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="search">
        <form method="GET">
          <div class="search-wrapper">
            <div class="search-box dark">
              <i class="fas fa-search search-icon"></i>
              <input type="text" class="form-control search-input" name="recherche"
                value="<?= h($search) ?>" placeholder="Rechercher par email...">
              <button type="submit" class="btn search-button">Rechercher</button>
            </div>
          </div>
        </form>
      </div>

      <div class="table-responsive card bg-dark border-secondary">
        <table class="table table-dark table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Nom & Prénom</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th>Inscrit</th>
              <th>Demandes</th>
              <th>Messages</th>
              <th>Profil</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($clients)): ?>
              <tr>
                <td colspan="9" class="text-center text-secondary py-4">
                  <?= $search !== '' ? 'Aucun client ne correspond à votre recherche' : 'Aucun client inscrit' ?>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($clients as $c): ?>
                <tr>
                  <td>#<?= (int)$c['idClient'] ?></td>
                  <td class="fw-bold"><?= h(trim(($c['prenom'] ?? '') . ' ' . ($c['nom'] ?? ''))) ?></td>
                  <td><?= h($c['email'] ?? '—') ?></td>
                  <td><?= h($c['telephone'] ?? '—') ?></td>
                  <td>
                    <?php if ($c['inscrit'] == 1): ?>
                      <span class="badge bg-success">Oui</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Non</span>
                    <?php endif; ?>
                  </td>
                  <td><?= (int)$c['nb_demandes'] ?></td>
                  <td><?= (int)$c['nb_messages'] ?></td>
                  <td>
                    <a href="client_profil.php?id=<?= (int)$c['idClient'] ?>" class="btn btn-outline-light btn-sm">Profil</a>
                  </td>
                  <td>
                    <form method="POST" onsubmit="return confirm('Supprimer ce client ainsi que ses demandes et messages ?')">
                      <input type="hidden" name="delete_client" value="<?= (int)$c['idClient'] ?>">
                      <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="pagination-wrapper overflow-auto">
        <nav aria-label="Navigation des pages">
          <ul class="pagination justify-content-center flex-wrap mb-0">

            <?php $queryParams = $_GET; ?>

            <!-- Lien Page Précédente -->
            <?php
            $prevPage = max(1, $page - 1);
            $queryParams['page'] = $prevPage;
            ?>
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?<?= http_build_query($queryParams) ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>

            <!-- Pages autour de la page courante -->
            <?php for ($i = 0; $i <= 2; $i++): ?>
              <?php
              $numeroPage = $page + $i;
              if ($numeroPage > $nombreDePages) break; // évite de dépasser
              $queryParams['page'] = $numeroPage;
              ?>
              <li class="page-item <?= $numeroPage === $page ? 'disabled' : '' ?>">
                <a href="?<?= http_build_query($queryParams) ?>" class="page-link">
                  <?= $numeroPage ?>
                </a>
              </li>
            <?php endfor; ?>

            <!-- Lien "..." vers page+3, seulement si elle existe -->
            <?php if ($page + 3 <= $nombreDePages): ?>
              <?php $queryParams['page'] = $page + 3; ?>
              <li class="page-item">
                <a href="?<?= http_build_query($queryParams) ?>" class="page-link">...</a>
              </li>
            <?php endif; ?>

            <!-- Lien Page Suivante -->
            <?php
            $nextPage = min($nombreDePages, $page + 1);
            $queryParams['page'] = $nextPage;
            ?>
            <li class="page-item <?= $page >= $nombreDePages ? 'disabled' : '' ?>">
              <a class="page-link" href="?<?= http_build_query($queryParams) ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>

          </ul>
        </nav>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>