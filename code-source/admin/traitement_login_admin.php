<?php
// parametre back-office admin guard
session_start();
require_once '../connexion/dbconnexion.php';

$login    = trim($_POST['login']    ?? '');
$password = trim($_POST['password'] ?? '');

// Récupérer l'admin depuis la table admin
$stmt = $conn->prepare("SELECT id_admin, nom, password_hash FROM admin WHERE login = ? LIMIT 1");
$stmt->bind_param("s", $login);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if ($admin && password_verify($password, $admin['password_hash'])) {
    $_SESSION['admin_logged'] = true;
    $_SESSION['admin_id']     = $admin['id_admin'];
    $_SESSION['admin_nom']    = $admin['nom'];
    header("Location: index.php");
    exit();
} else {
    $_SESSION['admin_error'] = "Identifiant ou mot de passe incorrect.";
    header("Location: login_admin.php");
    exit();
}
?>
