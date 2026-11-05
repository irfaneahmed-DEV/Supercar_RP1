<?php
//  NOUVEAU FICHIER : déconnexion du client
session_start();
session_unset();    // Supprime toutes les variables de session
session_destroy();  // Détruit la session

// Redirection vers l'accueil
header("Location: index.php");
exit();
?>
