<?php

require_once __DIR__ . '/../Model/database.php';
require_once __DIR__ . '/../Model/ModelDeleteAccount.php';

$db = new DB();
$showConfirmation = false;

if (!isset($_SESSION['userid'])) {
    header('Location: ' . $base . 'login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_account']) && $_POST['delete_account'] === 'true') {
        $showConfirmation = true;
    }

    if (isset($_POST['delete_account_valid']) && $_POST['delete_account_valid'] === 'true') {
        deleteAccountByUserId($db, $_SESSION['userid']);
        session_unset();
        session_destroy();
        header('Location: ' . $base . 'index.php');
        exit;
    }
}

require_once __DIR__ . '/../View/delete_account.php';
