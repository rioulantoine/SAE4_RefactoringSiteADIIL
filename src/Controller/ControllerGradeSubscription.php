<?php

require_once __DIR__ . '/../Model/ModelGradeSubscription.php';

$db = new DB();
$isLoggedIn = isset($_SESSION['userid']);

if (!$isLoggedIn) {
    header('Location: ' . $base . 'login');
    exit;
}

$userid = $_SESSION['userid'];
$savedPaymentInfo = isset($_SESSION['saved_payment_info_grade']) ? $_SESSION['saved_payment_info_grade'] : [];
$savedCardNumber = isset($savedPaymentInfo['numero_carte']) ? $savedPaymentInfo['numero_carte'] : '';
$savedExpiration = isset($savedPaymentInfo['expiration']) ? $savedPaymentInfo['expiration'] : '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . $base . 'grade');
    exit;
}

$id_grade = $_GET['id'];
$grade = getGradeById($db, $id_grade);

if (empty($grade)) {
    $_SESSION['message'] = "Le grade selectionne n'existe pas.";
    $_SESSION['message_type'] = 'error';
    header('Location: ' . $base . 'grade');
    exit;
}

$currentGrade = getCurrentAdhesion($db, $userid);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mode_paiement'])) {
    $mode_paiement = $_POST['mode_paiement'];

    if ($mode_paiement === 'carte_credit' && isset($_POST['remember_payment']) && $_POST['remember_payment'] === '1' && isset($_POST['numero_carte'], $_POST['expiration'])) {
        $_SESSION['saved_payment_info_grade'] = [
            'numero_carte' => $_POST['numero_carte'],
            'expiration' => $_POST['expiration'],
        ];
    }

    if (!empty($currentGrade)) {
        deleteCurrentAdhesion($db, $userid);
    }

    createAdhesion(
        $db,
        $userid,
        $id_grade,
        $grade['prix_grade'],
        $mode_paiement
    );

    $_SESSION['message'] = 'Adhesion au grade reussie !';
    $_SESSION['message_type'] = 'success';
    header('Location: ' . $base . 'grade');
    exit;
}

require_once __DIR__ . '/../View/grade_subscription.php';
