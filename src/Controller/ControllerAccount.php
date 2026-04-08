<?php

// Importer les fichiers
require_once __DIR__ . '/../Service/files_save.php';
require_once __DIR__ . '/../Model/ModelAccount.php';

// Connexion à la base de donnees
$db = new DB();

// 
$isLoggedIn = isset($_SESSION["userid"]);
if (!$isLoggedIn) {
    header("Location: " . $base . "login");
    exit;
}

$infoUser = getAccountInfo($db, $_SESSION['userid']);

if (empty($infoUser)) {
    $_SESSION['message'] = "Erreur : utilisateur introuvable dans la base de données.";
    $_SESSION['message_type'] = "error";
    header("Location: " . $base . "login");
    exit;
}
?>
<!-- Formulaire permettant de modifier la photo de profil de l'utilisateur-->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Appelle saveImage() pour traiter l'image
    $fileName = saveImageEvent();

    if ($fileName !== null) {

        // Suppression de l'ancienne image si elle existe
        if (!empty($infoUser[0]['pp_membre'])) {
            deleteFile($infoUser[0]['pp_membre']); 
        }

        // Met à jour la base de données avec le nom du fichier
        updateAccountPicture($db, $fileName, $_SESSION['userid']);

            $_SESSION['message'] = "Mise à jour de la photo de profil réussie !";
            $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Erreur : veuillez vérifier le fichier envoyé.";
        $_SESSION['message_type'] = "error";
    }
    // Recharge la page pour afficher la nouvelle image
    header("Location: " . $base . "/account");
    exit;
}
?>

<!-- Formulaire contenant les données personnelles de l'utilisateur -->
<?php
    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['lastName'], $_POST['mail'])) {
        // Charger les informations actuelles de l'utilisateur depuis la base de données
        $currentUserData = getCurrentUserData($db, $_SESSION['userid']);

        // Vérifier si les données actuelles existent
        if (!empty($currentUserData)) {
            $currentName = $currentUserData[0]['prenom_membre'];
            $currentLastName = $currentUserData[0]['nom_membre'];
            $currentMail = $currentUserData[0]['email_membre'];
            $currentTp = $currentUserData[0]['tp_membre'];

            // Récupérer les nouvelles valeurs ou conserver les anciennes si aucune modification
            $name = empty($_POST['name']) ? $currentName : htmlspecialchars($_POST['name']);
            $lastName = empty($_POST['lastName']) ? $currentLastName : htmlspecialchars($_POST['lastName']);
            $mail = empty($_POST['mail']) ? $currentMail : htmlspecialchars($_POST['mail']);
            $tp = isset($_POST['tp']) && !empty($_POST['tp']) ? htmlspecialchars($_POST['tp']) : $currentTp;

            // Vérifier si l'adresse e-mail existe déjà (et appartient à un autre utilisateur)
            $existingEmail = getExistingEmail($db, $mail, $_SESSION['userid']);

            if (!empty($existingEmail)) {
                // Cas où l'adresse e-mail est déjà utilisée
                $_SESSION['message'] = "Les modifications n'ont pas pu être effectuées car l'adresse e-mail est déjà utilisée par un autre compte.";
                $_SESSION['message_type'] = "error"; // Pour gérer les styles
            } else {
                // Mettre à jour les informations de l'utilisateur
                updateAccountInfo($db, $name, $lastName, $mail, $tp, $_SESSION['userid']);

                // Message de succès suite aux modifications
                $_SESSION['message'] = "Vos informations ont été mises à jour avec succès !";
                $_SESSION['message_type'] = "success"; // Pour gérer les styles
            }
        } else {
            // Cas où l'utilisateur actuel n'existe pas dans la base
            $_SESSION['message'] = "Erreur : utilisateur introuvable dans la base de données.";
            $_SESSION['message_type'] = "error";
        }

        // Recharger la page
        header("Location: " . $base . "/account");
        exit();
    }
    ?>


<!-- Formulaire permettant à l'utilisateur de modifier son mot de passe-->
<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mdp'], $_POST['newMdp'], $_POST['newMdpVerif'])) {
        $currentPassword = htmlspecialchars(trim($_POST['mdp']));
        $newPassword = htmlspecialchars(trim($_POST['newMdp']));
        $newPasswordVerif = htmlspecialchars(trim($_POST['newMdpVerif']));

        // Récupérer l'utilisateur et le mot de passe actuel depuis la base de données
        $user = getAccountPassword($db, $_SESSION['userid']);

        if (empty($user)) {
            $_SESSION['message'] = "Mot de passe actuel incorrect.";
            $_SESSION['message_type'] = "error";
        } else {
            if($user[0]['password_membre'] == NULL && $currentPassword == ""){
                $password_ok = true;
            }else{
                $password_ok = password_verify($currentPassword, $user[0]['password_membre']);
            }

            if (!$password_ok) {
                $_SESSION['message'] = "Mot de passe actuel incorrect.";
                $_SESSION['message_type'] = "error";
            } elseif ($newPassword != $newPasswordVerif) {
                $_SESSION['message'] = "Les nouveaux mots de passe ne correspondent pas.";
                $_SESSION['message_type'] = "error";
            } else {
                // Mettre à jour le mot de passe dans la base de données
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                updateAccountPassword($db, $hashedPassword, $_SESSION['userid']);

                $_SESSION['message'] = "Mot de passe mis à jour avec succès !";
                $_SESSION['message_type'] = "success";
            }
        }

        // Redirection pour éviter le double envoi du formulaire
        header("Location: " . $base . "/account");
        exit();
 }

$viewAll = isset($_GET['viewAll']) && $_GET['viewAll'] === '1';
$historiqueAchats = getAccountHistory($db, $_SESSION['userid'], $viewAll);

require_once __DIR__ . '/../View/account.php';