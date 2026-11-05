<?php
session_start();
require_once "../connexion/dbconnexion.php";
/** @var Mysqli $conn */

// Récupération sécurisée des champs
$nom        = trim($_POST['nom']        ?? '');
$prenom     = trim($_POST['prenom']     ?? '');
$telephone  = trim($_POST['telephone']  ?? '');
$email      = trim($_POST['email']      ?? '');
$mp         = $_POST['mp']              ?? '';

$inscrit          = 1;
$dateInscription  = date('Y-m-d');

$data = [
    "nom" => $nom,
    "prenom" => $prenom,
    "telephone" => $telephone,
    "email" => $email,
    "dateInscription" => $dateInscription
];

// Hash du mot de passe
$hashmp = password_hash($mp, PASSWORD_DEFAULT);

// 1) Vérifier si l'email existe déjà
$stmt = $conn->prepare("SELECT * FROM client WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row    = $result->fetch_assoc();

if ($row) {
    // si un compte existe deja avec l'email, on redirige
    if ($row['inscrit'] == true) {
        header("Location: inscription.php?error=email_used");
        exit();
    } else {
        // sinon on met à jour l'etat de "inscrit" et on insert les données dans la table login
        updateCompte($row['idClient'], $data);
        insetionLogin($hashmp, $row['idClient']);
        // Redirection 
        redirection($row['idClient'], $row['prenom']);
    }
} else {

    // 2) Insertion du client
    $stmt = $conn->prepare("
    INSERT INTO client (nom, prenom, telephone, email, inscrit, dateInscription) 
    VALUES (?, ?, ?, ?, ?, ?)
");
    $stmt->bind_param("ssssis", $nom, $prenom, $telephone, $email, $inscrit, $dateInscription);
    $stmt->execute();

    // Récupération de l'id généré
    $idClient = $conn->insert_id;

    // 3) Insertion dans Login
    insetionLogin($hashmp, $idClient);

    redirection($idClient, $prenom);
}

function insetionLogin(String $hashmp, int $idClient): bool
{
    global $conn;
    $stmt = $conn->prepare("INSERT INTO login (mp, idClient) VALUES (?, ?)");
    $stmt->bind_param("si", $hashmp, $idClient);
    return $stmt->execute();
}
function updateCompte(int $id, array $data): bool
{
    global $conn;
    // On met à jour les données dans client
    $sql = "UPDATE client SET nom = ?, prenom = ?, telephone = ?, email = ?, dateInscription = ?,inscrit = true WHERE idClient = ?";
    $nom = $data['nom'];
    $prenom = $data['prenom'];
    $telephone = $data['telephone'];
    $email = $data['email'];
    $dateInscription = $data['dateInscription'];
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nom, $prenom, $telephone, $email, $dateInscription, $id);
    return $stmt->execute();
}
function redirection(int $idClient, String $prenom)
{
    $_SESSION['idUtil']      = $idClient;
    $_SESSION['user_prenom'] = $prenom;

    // 5) Redirection vers l'accueil
    header("Location: index.php");
    exit();
}