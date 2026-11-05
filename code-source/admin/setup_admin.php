<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Supercar — Setup Admin</title>
  
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  
  <style>
    body {
      background-color: #0f1014;
      color: #f2f2f2;
    }
    .card-custom {
      background-color: #171922;
      border: 1px solid #2e3040;
      border-radius: 14px;
    }
    .btn-supercar {
      background-color: #c81e3a;
      border: none;
      color: #fff;
    }
    .btn-supercar:hover {
      background-color: #a01428;
      color: #fff;
      scaleX: 0.8;
    }
    .form-control {
      background-color: #0f1014;
      border-color: #2e3040;
      color: #f2f2f2;
    }
    .form-control:focus {
      background-color: #0f1014;
      color: #f2f2f2;
      border-color: #c81e3a;
      box-shadow: 0 0 0 0.25rem rgba(200, 30, 58, 0.25);
    }
    .cred-box {
      background: #0f1014;
      border: 1px solid #2e3040;
      border-radius: 8px;
      padding: 14px;
      font-family: monospace;
      font-size: 13px;
    }
    .cred-box span { color: #c81e3a; }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-4">

<div class="container">
  <div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-8 col-lg-5">
      
      <div class="card card-custom p-4 p-md-5 shadow-lg">
        <h1 class="h3 text-danger mb-2">⚙️ Setup Admin</h1>
        <p class="text-secondary small mb-4">
          Ce script crée le compte administrateur dans la base de données.<br/>
        </p>

<?php
require_once '../connexion/dbconnexion.php';
require_once 'admin_guard.php';

// Créer la table admin si elle n'existe pas
$conn->query("
    CREATE TABLE IF NOT EXISTS `admin` (
        `id_admin`       INT NOT NULL AUTO_INCREMENT,
        `login`          VARCHAR(50)  NOT NULL UNIQUE,
        `password_hash`  VARCHAR(255) NOT NULL,
        `nom`            VARCHAR(100) NOT NULL DEFAULT 'Administrateur',
        PRIMARY KEY (`id_admin`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4
");

$done   = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login']    ?? '');
    $nom      = trim($_POST['nom']      ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (strlen($login) < 3)         $errors[] = "L'identifiant doit faire au moins 3 caractères.";
    if (strlen($password) < 8)      $errors[] = "Le mot de passe doit faire au moins 8 caractères.";
    if ($password !== $confirm)     $errors[] = "Les mots de passe ne correspondent pas.";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("SELECT id_admin FROM admin WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();

        if ($exists) {
            $stmt = $conn->prepare("UPDATE admin SET password_hash=?, nom=? WHERE login=?");
            $stmt->bind_param("sss", $hash, $nom, $login);
        } else {
            $stmt = $conn->prepare("INSERT INTO admin (login, password_hash, nom) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $login, $hash, $nom);
        }
        $stmt->execute();
        $done = true;
    }
}
?>

<?php if ($done): ?>
  <div class="alert alert-success small mb-3">
    ✅ Compte administrateur créé avec succès !
  </div>
  <div class="cred-box mb-3">
    Identifiant : <span><?= htmlspecialchars($_POST['login']) ?></span><br/>
    Mot de passe : <span><?= htmlspecialchars($_POST['password']) ?></span><br/>
    Accès admin : <span><a href="login_admin.php" class="text-danger">admin/login_admin.php</a></span>
  </div>

<?php elseif (!empty($errors)): ?>
  <?php foreach ($errors as $e): ?>
    <div class="alert alert-danger small mb-2">❌ <?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

<?php else: ?>
  <div class="alert alert-warning small mb-4">
    Remplissez le formulaire pour créer le compte admin.
  </div>
<?php endif; ?>

<?php if (!$done): ?>
<form method="POST">
  <div class="mb-3">
    <label class="form-label text-secondary small">Identifiant admin</label>
    <input type="text" name="login" class="form-control" value="admin" required/>
  </div>

  <div class="mb-3">
    <label class="form-label text-secondary small">Nom affiché</label>
    <input type="text" name="nom" class="form-control" value="Administrateur" required/>
  </div>

  <div class="mb-3">
    <label class="form-label text-secondary small">Mot de passe</label>
    <input type="password" name="password" class="form-control" placeholder="min. 8 caractères" required/>
  </div>

  <div class="mb-4">
    <label class="form-label text-secondary small">Confirmer le mot de passe</label>
    <input type="password" name="confirm" class="form-control" required/>
  </div>

  <div class="d-grid gap-2">
    <button type="submit" class="btn btn-supercar fw-bold py-2">Créer le compte admin</button>
    <button type="reset" class="btn btn-secondary py-2">Réinitialiser</button>
    <a href="index.php" class="btn btn-outline-light py-2">Retour à l'accueil</a>
  </div>
</form>
<?php endif; ?>

      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>