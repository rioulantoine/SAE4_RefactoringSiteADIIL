<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>


    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/login_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/footer_style.css">

</head>
    <body>
        <?php if (!empty($errorMessage)): ?>
            <h3 class="error-message"><?php echo htmlspecialchars($errorMessage); ?></h3>
        <?php endif; ?>

        <form method="POST" action="" class="login-form">
            <h1>S'inscrire</h1>

            <label for="mail">Prénom :</label>
            <input type="text" name="fname" value="<?php echo htmlspecialchars($oldFname ?? ''); ?>">

            <label for="mail">Nom :</label>
            <input type="text" name="lname" value="<?php echo htmlspecialchars($oldLname ?? ''); ?>">
        
            <label for="mail">Adresse Mail :*</label>
            <input type="email" name="mail" value="<?php echo htmlspecialchars($oldMail ?? ''); ?>" required>

            <label for="password">Mot de passe :*</label>
            <input type="password" name="password" required>

            <label for="password">Confirmez le Mot de passe :*</label>
            <input type="password" name="password_verif" required>

            <button type="submit">Confirmer</button>
        </form>
    </body>
</html>