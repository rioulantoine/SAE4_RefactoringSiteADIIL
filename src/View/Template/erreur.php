<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/erreur_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/footer_style.css">
</head>

<body class="body_margin">
    <main class="error-page">
        <div class="error-card">
            <p class="error-code">404</p>
            <h1>Page introuvable</h1>
            <p>La page demandee n'existe pas ou a ete deplacee.</p>
            <a class="error-cta" href="<?php echo $base; ?>accueil">Retour a l'accueil</a>
        </div>
    </main>
</body>
</html>