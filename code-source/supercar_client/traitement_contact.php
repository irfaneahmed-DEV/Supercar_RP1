<?php
session_start();
// Importation de la connexion MySQLi
require_once "../connexion/dbconnexion.php";

// Vérifier que tous les champs existent
if (
    isset($_POST['nom']) &&
    isset($_POST['email']) &&
    isset($_POST['telephone']) &&
    isset($_POST['message'])
) {
    // Sécurisation des données
    $nom       = htmlspecialchars(trim($_POST['nom']));
    $email     = htmlspecialchars(trim($_POST['email']));
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $message   = htmlspecialchars(trim($_POST['message']));

    // Si le client est connecté, on utilise directement son ID
    if (isset($_SESSION['idUtil'])) {
        $idClient = intval($_SESSION['idUtil']);
    } else {
        // Vérifier si le client existe déjà via email
        $stmt = $conn->prepare("SELECT idClient FROM client WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $client = $result->fetch_assoc();

        if ($client) {
            $idClient = $client['idClient'];
        } else {
            // Le client n'existe pas → insertion
            $stmt = $conn->prepare("
                INSERT INTO client (prenom, email, telephone, nom, inscrit, dateInscription)
                VALUES (?, ?, ?, ?, 0, NOW())
            ");
            $prenom = "";
            $stmt->bind_param("ssss", $prenom, $email, $telephone, $nom);
            $stmt->execute();
            $idClient = $stmt->insert_id;
        }
    }

    // Insérer le message dans Contact
    $stmt = $conn->prepare("INSERT INTO contact (message, idClient) VALUES (?, ?)");
    $stmt->bind_param("si", $message, $idClient);
    $success = $stmt->execute();

    if ($success) {
        header("Location: contact.php?success=1");
        exit();
    }
} else if (isset($_SESSION['idUtil']) && isset($_POST['message'])) {

    $idClient = $_SESSION['idUtil'];
    $message  = trim($_POST['message']); //  pas de htmlspecialchars ici

    $stmt = $conn->prepare("INSERT INTO contact (message, idClient) VALUES (?, ?)");
    $stmt->bind_param("si", $message, $idClient); //  casse cohérente

    if ($stmt->execute()) {
        header("Location: contact.php?success=1");
        exit();
    }
} else {
    echo "Erreur : formulaire incomplet.";
}