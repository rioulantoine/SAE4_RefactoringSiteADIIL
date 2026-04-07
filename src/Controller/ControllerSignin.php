<?php

require_once __DIR__ . '/../Model/ModelSignin.php';

$db = new DB();
$modelSignin = new ModelSignin($db);

$errorMessage = '';
$oldFname = '';
$oldLname = '';
$oldMail = '';

function sanitizeInput(string $text): string
{
    return htmlspecialchars(trim($text));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldFname = sanitizeInput($_POST['fname'] ?? '');
    $oldLname = sanitizeInput($_POST['lname'] ?? '');
    $oldMail = sanitizeInput($_POST['mail'] ?? '');

    $password = sanitizeInput($_POST['password'] ?? '');
    $passwordVerif = sanitizeInput($_POST['password_verif'] ?? '');

    if ($modelSignin->emailExists($oldMail)) {
        $errorMessage = 'Utilisateur deja present';
    } elseif ($password !== $passwordVerif) {
        $errorMessage = 'Les mots de passe ne correspondent pas';
    } else {
        $fname = $oldFname !== '' ? $oldFname : 'N/A';
        $lname = $oldLname !== '' ? $oldLname : 'N/A';

        $modelSignin->createAccount(
            $lname,
            $fname,
            $oldMail,
            password_hash($password, PASSWORD_DEFAULT)
        );

        $_SESSION['message'] = 'Compte cree avec succes';
        $_SESSION['message_type'] = 'success';

        header('Location: ' . $base . 'login');
        exit;
    }
}

require_once __DIR__ . '/../View/signin.php';
