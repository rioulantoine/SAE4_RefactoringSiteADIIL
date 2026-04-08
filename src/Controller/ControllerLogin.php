<?php

require_once __DIR__ . '/../Model/ModelLogin.php';

$db = new DB();
$modelLogin = new ModelLogin($db);

$loginError = '';
$oldMail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldMail = htmlspecialchars(trim($_POST['mail'] ?? ''));
    $password = htmlspecialchars(trim($_POST['password'] ?? ''));

    $user = $modelLogin->getUserByEmail($oldMail);

    if (!empty($user)) {
        $dbMail = $user['email_membre'];
        $dbPassword = $user['password_membre'];

        $mailOk = ($dbMail === $oldMail);

        if ($dbPassword == null && $password === '') {
            $passwordOk = true;
        } else {
            $passwordOk = password_verify($password, $dbPassword);
        }

        if ($mailOk && $passwordOk) {
            $memberId = $user['id_membre'];
            $_SESSION['userid'] = $memberId;

            if ($modelLogin->hasAssignedRole($memberId)) {
                $_SESSION['isAdmin'] = true;
            }

            header('Location: ' . $base . 'accueil');
            exit;
        }
    }

    $loginError = 'Erreur dans les informations de connexion.';
}

require_once __DIR__ . '/../View/login.php';
