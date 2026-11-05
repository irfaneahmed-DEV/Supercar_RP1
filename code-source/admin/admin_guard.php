<?php
//page back-office admin guard
// Inclure sur chaque page admin pour protéger l'accès
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login_admin.php");
    exit();
}
?>