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
  <link rel="stylesheet" href="admin.css" />
</head>

<body>
  <div class="admin-layout d-flex flex-column flex-lg-row">
    <?= get_sidebar('dashboard') ?>

    <main class="admin-main flex-grow-1 p-3 p-md-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 border-bottom border-secondary pb-3">
        <h1 class="h3 m-0">📊 Tableau de bord</h1>
        <div class="badge bg-secondary p-2 fs-6">👤 <?= htmlspecialchars($_SESSION['admin_nom']) ?></div>
      </div>

      <!-- Statistiques responsive en grilles Bootstrap -->
      <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
          <div class="card bg-dark border-secondary p-3 shadow-sm h-100">
            <h3 class="h6 text-secondary">Clients inscrits</h3>
            <div class="fs-2 fw-bold text-light"><?= $nbClients ?></div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
          <div class="card bg-dark border-secondary p-3 shadow-sm h-100">
            <h3 class="h6 text-secondary">Voitures</h3>
            <div class="fs-2 fw-bold text-light"><?= $nbVoitures ?></div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
          <div class="card bg-dark border-secondary p-3 shadow-sm h-100">
            <h3 class="h6 text-secondary">Services</h3>
            <div class="fs-2 fw-bold text-light"><?= $nbServices ?></div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
          <div class="card bg-dark border-secondary p-3 shadow-sm h-100">
            <h3 class="h6 text-secondary">Demandes d'essai</h3>
            <div class="fs-2 fw-bold text-light"><?= $nbDemandes ?></div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
          <div class="card bg-dark border-secondary p-3 shadow-sm h-100">
            <h3 class="h6 text-secondary">Messages reçus</h3>
            <div class="fs-2 fw-bold text-light"><?= $nbMessages ?></div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
          <div class="card bg-dark border-secondary p-3 shadow-sm h-100">
            <h3 class="h6 text-secondary">Demandes en attente</h3>
            <div class="fs-2 fw-bold text-warning"><?= $nbAttente ?></div>
          </div>
        </div>
      </div>

      <!-- Dernières demandes -->
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h2 class="h4 m-0">Dernières demandes d'essai</h2>
        <a href="demandes.php" class="btn btn-outline-secondary btn-sm">Voir tout →</a>
      </div>

      <div class="table-responsive card bg-dark border-secondary">
        <table class="table table-dark table-hover align-middle mb-0">
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
                <td colspan="5" class="text-center text-secondary py-4">Aucune demande</td>
              </tr>
            <?php else: ?>
              <?php foreach ($demandes as $d): ?>
                <?php
                $s = strtolower($d['status']);
                $badgeBg = str_contains($s, 'accept') ? 'bg-success' : (str_contains($s, 'refus') ? 'bg-danger' : (str_contains($s, 'attente') ? 'bg-warning text-dark' : 'bg-primary'));
                ?>
                <tr>
                  <td><?= htmlspecialchars($d['prenom'] . ' ' . $d['nom']) ?></td>
                  <td><?= htmlspecialchars($d['email']) ?></td>
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