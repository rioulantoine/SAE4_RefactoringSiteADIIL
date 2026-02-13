<?php
// logout.php - Handles user disconnection

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];
session_destroy();

header("Location: " . $base . "accueil");
exit();
