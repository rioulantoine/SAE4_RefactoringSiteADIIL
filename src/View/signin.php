<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <?php $base = rtrim(getenv('BASE_URL') ?: 'http://localhost/SAE4/SAE4_RefactoringSiteADIIL/', '/'); ?>

    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/login_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/footer_style.css">

</head>
    <body>
        <?php 
            $db = new DB();

            function format_input($text){
                return htmlspecialchars(trim($text));
            }
        ?>

        <form method="POST" action="" class="login-form">
            <h1>S'inscrire</h1>

            <label for="mail">Prénom :</label>
            <input type="text" name="fname">

            <label for="mail">Nom :</label>
            <input type="text" name="lname">
        
            <label for="mail">Adresse Mail :*</label>
            <input type="email" name="mail" required>

            <label for="password">Mot de passe :*</label>
            <input type="password" name="password" required>

            <label for="password">Confirmez le Mot de passe :*</label>
            <input type="password" name="password_verif" required>

            <button type="submit">Confirmer</button>
        </form>

        <!-- Gestion de l'inscription -->
        <?php

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $mail = htmlspecialchars(trim($_POST['mail']));

            $selection_db = $db->select(
                "SELECT id_membre FROM MEMBRE WHERE email_membre = ?",
                "s",
                [$mail]
            );

            if(empty($selection_db)){

                $password = format_input($_POST['password']);
                $password_verif = format_input($_POST['password_verif']);

                if($password == $password_verif){
                    $fname = "N/A";
                    $lname = "N/A";
    
                    if(isset($_POST['fname'])){
                        $fname = format_input($_POST['fname']);
                    }
                    if(isset($_POST['lname'])){
                        $lname = format_input($_POST['lname']);
                    }

                    $db->query(
                        "CALL creationCompte ( ? , ? , ? , ? , ? );",
                        "sssss",
                        [$lname,$fname,$mail,password_hash($password, PASSWORD_DEFAULT),'defaultPP.png']
                    );
                }
                header("Location: " . $base . "/login");
                exit;
            }else{
                echo 'Utilisateur déjà présent';
            }
        }
        ?>
    </body>
</html>