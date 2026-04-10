<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/delete_account_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">
    <title>Supprimer le compte</title>
</head>
<body>
<?php if ($showConfirmation): ?>
    <div id="deleteAccountAlert" class="alert-container">
        <div class="alert-content">
            <p>
                ⚠️ Vous êtes sur le point de supprimer votre compte. Cette action est irréversible.
                Toutes vos données seront perdues. Veuillez cocher la case ci-dessous pour confirmer que vous comprenez les conséquences.
            </p>
            <input type="checkbox" id="confirmCheckbox"> <label for="confirmCheckbox">J'ai compris</label>
            <br><br>
            <ul>
                <li>
                    <form action="" method="POST">
                        <button id="confirmDelete" name="delete_account_valid" value="true" disabled>Valider</button>
                    </form>
                </li>
                <li>
                    <button id="cancelDelete" onclick="window.location.href='<?php echo $base; ?>account'">Revenir en arrière</button>
                </li>
            </ul>
        </div>
    </div>
<?php else: ?>
    <script>
        window.location.href = '<?php echo $base; ?>account';
    </script>
<?php endif; ?>

<script src="<?php echo $base; ?>public/scripts/confirm_account_supression.js"></script>
</body>
</html>
