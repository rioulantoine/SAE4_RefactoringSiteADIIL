<?php

require_once __DIR__ . '/../Model/ModelGradeSubscription.php';

$db = new DB();
$isLoggedIn = isset($_SESSION['userid']);

if (!$isLoggedIn) {
    header('Location: ' . $base . 'login');
    exit;
}

$userid = (int) $_SESSION['userid'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . $base . 'grade');
    exit;
}

$id_grade = (int) $_GET['id'];
$grade = getGradeById($db, $id_grade);

if (empty($grade)) {
    $_SESSION['message'] = "Le grade selectionne n'existe pas.";
    $_SESSION['message_type'] = 'error';
    header('Location: ' . $base . 'grade');
    exit;
}

$currentGrade = getCurrentAdhesion($db, $userid);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mode_paiement'])) {
    $mode_paiement = (string) $_POST['mode_paiement'];

    if (!empty($currentGrade)) {
        deleteCurrentAdhesion($db, $userid);
    }

    createAdhesion(
        $db,
        $userid,
        $id_grade,
        (float) $grade['prix_grade'],
        $mode_paiement
    );

    $_SESSION['message'] = 'Adhesion au grade reussie !';
    $_SESSION['message_type'] = 'success';
    header('Location: ' . $base . 'grade');
    exit;
}

require_once __DIR__ . '/../View/grade_subscription.php';
