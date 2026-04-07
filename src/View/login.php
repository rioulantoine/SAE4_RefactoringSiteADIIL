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
        <?php if (!empty($loginError)): ?>
            <h3 class="login-error"><?php echo htmlspecialchars($loginError); ?></h3>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form method="POST" action="" class="login-form">
            <h1>Connexion</h1>
            <label for="mail">Adresse Mail :</label>
            <input type="email" name="mail" value="<?php echo htmlspecialchars($oldMail ?? ''); ?>" required>

            <label for="password">Mot de passe :</label>
            <input type="password" name="password">

            <button type="submit">Se connecter</button>
        </form>

        <form method="GET" action="<?php echo $base; ?>signin" id="create-account">
            <h2>Pas encore de compte ?</h2>
            <button type="submit">Créez en un</button>
        </form>
    </body>
</html>