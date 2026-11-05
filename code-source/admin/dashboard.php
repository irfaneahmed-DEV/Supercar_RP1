<?php
require_once 'admin_guard.php';
require_once '../connexion/dbconnexion.php';
require_once 'admin_layout.php';

// Compteurs
$nbClients  = $conn->query("SELECT COUNT(*) FROM client")->fetch_row()[0];
$nbVoitures = $conn->query("SELECT COUNT(*) FROM voiture")->fetch_row()[0];
$nbServices = $conn->query("SELECT COUNT(*) FROM service")->fetch_row()[0];
$nbDemandes = $conn->query("SELECT COUNT(*) FROM demande_d_essai")->fetch_row()[0];
$nbMessages = $conn->query("SELECT COUNT(*) FROM contact")->fetch_row()[0];
$nbAttente  = $conn->query("SELECT COUNT(*) FROM demande_d_essai WHERE status='en attente'")->fetch_row()[0];

// 10 dernières demandes
$demandes = $conn->query("
    SELECT d.idDemande, d.dateSouhaitee, d.status,
           c.nom, c.prenom, c.email,
           v.modele, m.nom AS marque
    FROM demande_d_essai d
    JOIN client c ON c.idClient = d.idClient
    JOIN voiture v ON v.idVoiture = d.idVoiture
    JOIN marque m ON m.id_marque = v.id_marque
    ORDER BY d.dateDemande DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin – Dashboard</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="admin.css">
</head>

<body>
  <div class="admin-layout d-flex flex-column flex-md-row">
    <?= get_sidebar('Tableau de bord') ?>

    <main class="admin-main flex-grow-1 p-3 p-md-4">
      <!-- En-tête -->
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 border-bottom pb-3">
        <h1 class="h3 m-0">📊 Tableau de bord</h1>
        <div class="badge bg-secondary p-2 fs-6">👤 <?= htmlspecialchars($_SESSION['admin_nom']) ?></div>
      </div>

      <!-- Cartes de Statistiques -->
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-6 g-3 mb-4">
        <div class="col">
          <div class="card h-100 bg-dark border-secondary text-center">
            <div class="card-body p-3">
              <h6 class="card-title text-secondary small">Clients inscrits</h6>
              <div class="display-6 fw-bold text-light"><?= $nbClients ?></div>
            </div>
          </div>
        </div>

        <div class="col">
          <div class="card h-100 bg-dark border-secondary text-center">
            <div class="card-body p-3">
              <h6 class="card-title text-secondary small">Voitures</h6>
              <div class="display-6 fw-bold text-light"><?= $nbVoitures ?></div>
            </div>
          </div>
        </div>

        <div class="col">
          <div class="card h-100 bg-dark border-secondary text-center">
            <div class="card-body p-3">
              <h6 class="card-title text-secondary small">Services</h6>
              <div class="display-6 fw-bold text-light"><?= $nbServices ?></div>
            </div>
          </div>
        </div>

        <div class="col">
          <div class="card h-100 bg-dark border-secondary text-center">
            <div class="card-body p-3">
              <h6 class="card-title text-secondary small">Demandes d'essai</h6>
              <div class="display-6 fw-bold text-light"><?= $nbDemandes ?></div>
            </div>
          </div>
        </div>

        <div class="col">
          <div class="card h-100 bg-dark border-secondary text-center">
            <div class="card-body p-3">
              <h6 class="card-title text-secondary small">Messages reçus</h6>
              <div class="display-6 fw-bold text-light"><?= $nbMessages ?></div>
            </div>
          </div>
        </div>

        <div class="col">
          <div class="card h-100 bg-dark border-warning text-center">
            <div class="card-body p-3">
              <h6 class="card-title text-warning small">En attente</h6>
              <div class="display-6 fw-bold text-warning"><?= $nbAttente ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Dernières demandes -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 m-0">Dernières demandes d'essai</h2>
        <a href="demandes.php" class="btn btn-outline-secondary btn-sm">Voir tout →</a>
      </div>

      <!-- Tableau Responsive -->
      <div class="table-responsive card bg-dark border-secondary">
        <table class="table table-dark table-hover table-striped align-middle mb-0">
          <thead>
            <tr>
              <th>Client</th>
              <th>Email</th>
              <th>Voiture</th>
              <th>Date souhaitée</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($demandes)): ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-4">Aucune demande enregistrée</td>
              </tr>
            <?php else: ?>
              <?php foreach ($demandes as $d): ?>
                <?php
                $s = strtolower($d['status']);
                $badgeBg = str_contains($s, 'accept') ? 'bg-success' : (str_contains($s, 'refus') ? 'bg-danger' : (str_contains($s, 'attente') ? 'bg-warning text-dark' : 'bg-secondary'));
                ?>
                <tr>
                  <td class="fw-medium"><?= htmlspecialchars($d['prenom'] . ' ' . $d['nom']) ?></td>
                  <td><a href="mailto:<?= htmlspecialchars($d['email']) ?>" class="text-secondary text-decoration-none"><?= htmlspecialchars($d['email']) ?></a></td>
                  <td><?= htmlspecialchars($d['marque'] . ' ' . $d['modele']) ?></td>
                  <td><?= date('d/m/Y', strtotime($d['dateSouhaitee'])) ?></td>
                  <td><span class="badge <?= $badgeBg ?>"><?= htmlspecialchars($d['status']) ?></span></td>
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