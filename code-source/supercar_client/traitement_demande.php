<?php
// ✅ NOUVEAU FICHIER : traitement de la demande d'essai
session_start();
require_once "../connexion/dbconnexion.php";
/** @var Mysqli $conn */

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['idUtil'])) {
    header("Location: connexion.php");
    exit();
}

$idClient   = intval($_SESSION['idUtil']);
$idVoiture  = intval($_POST['idVoiture'] ?? 0);
$dateSouhaitee = $_POST['date'] ?? '';
$Heure = $_POST['Demande_heure']. ':00' ?? '' ;

echo $Heure;
 
// Validation basique
if ($idVoiture <= 0 || empty($dateSouhaitee)) {
    header("Location: demande_essai.php");
    exit();
}

// Vérifier que la voiture existe
$stmt = $conn->prepare("SELECT idVoiture FROM voiture WHERE idVoiture = ?");
$stmt->bind_param("i", $idVoiture);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    header("Location: demande_essai.php");
    exit();
}

// Insertion de la demande
$dateDemande = date('Y-m-d');
$status      = 'en attente';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $stmt = $conn->prepare("
        INSERT INTO demande_d_essai
            (dateSouhaitee, status, dateDemande, idVoiture, idClient, heureDemande)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
} catch (mysqli_sql_exception $e) {
    die("Erreur SQL : " . $e->getMessage());
}

$stmt->bind_param("sssiis", $dateSouhaitee, $status, $dateDemande, $idVoiture, $idClient,$Heure);
$stmt->execute();

// Redirection avec message de succès
header("Location: demande_essai.php?success=1");
exit();
?>
