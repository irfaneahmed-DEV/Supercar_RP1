<?php
session_start();
// Si déjà connecté → dashboard
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Supercar — Admin Login</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    :root { --accent: #c81e3a; }
    body { background-color: #0f1014; }
    .card-login {
      background-color: #171922;
      border: 1px solid #2e3040;
      border-top: 4px solid var(--accent);
      border-radius: 14px;
    }
    .btn-accent {
      background-color: var(--accent);
      border: none;
      color: #fff;
    }
    .btn-accent:hover {
      background-color: #a01428;
      color: #fff;
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-4">

<div class="container">
  <div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
      
      <div class="card card-login p-4 p-md-5 shadow-lg">
        <div class="text-center mb-4">
          <h1 class="h3 fw-bold text-danger mb-1">🔐 SUPERCAR</h1>
          <div class="small text-secondary">ADMINISTRATION</div>
        </div>

        <?php if (!empty($_SESSION['admin_error'])): ?>
          <div class="alert alert-danger text-center small mb-3 py-2">
            <?= htmlspecialchars($_SESSION['admin_error']) ?>
          </div>
          <?php unset($_SESSION['admin_error']); ?>
        <?php endif; ?>

        <form method="POST" action="traitement_login_admin.php">
          <div class="mb-3">
            <label class="form-label text-secondary small">Identifiant admin</label>
            <input type="text" name="login" class="form-control" placeholder="admin" required autofocus />
          </div>

          <div class="mb-4">
            <label class="form-label text-secondary small">Mot de passe</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required />
          </div>

          <button type="submit" class="btn btn-accent w-100 fw-bold py-2 mb-3">Se connecter</button>
        </form>

        <a href="../supercar_client/index.php" class="text-center d-block text-secondary text-decoration-none small hover-light">
          ← Retour au site client
        </a>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>