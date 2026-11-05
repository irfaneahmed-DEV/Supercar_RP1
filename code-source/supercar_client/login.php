<?php
session_start();
require_once "../connexion/dbconnexion.php";

// Récupération sécurisée
$email = $_POST['e-mail'] ?? '';
$mp = $_POST['mp'] ?? '';

// 1) Vérifier que l'utilisateur existe
$stmt = $conn->prepare("
    SELECT client.idClient, client.prenom, login.mp 
    FROM client 
    INNER JOIN login ON client.idClient = login.idClient
    WHERE client.email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// 2) Si utilisateur introuvable → erreur
if (!$user) {
    $_SESSION['login_error'] = true;
    header("Location: connexion.php");
    exit();
}

// 3) Vérifier le mot de passe
if (!password_verify($mp, $user['mp'])) {
    $_SESSION['login_error'] = true;
    header("Location: connexion.php");
    exit();
}

// 4) Connexion réussie → stocker l'id ET le prénom
$_SESSION['idUtil']      = $user['idClient'];
$_SESSION['user_prenom'] = $user['prenom'];

if (isset($_SESSION['demande'])) {
    header("Location: demande_essai.php");
    exit();
} else {
    // Aller à l'accueil
    header("Location: index.php");
    exit();
}