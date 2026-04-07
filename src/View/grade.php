<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/grade_style.css">

    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">

    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/footer_style.css">

</head>



<body class="body_margin">

<!--------------->
<!------PHP------>
<!--------------->



<!--------------->
<!------HTML----->
<!--------------->

<H1>Les grades</H1>

<!-- Affichage du message de succès ou d'erreur -->
<div>
    <?php
        if (isset($_SESSION['message'])) {
            $messageStyle = isset($_SESSION['message_type']) && $_SESSION['message_type'] === "error" ? "error-message" : "success-message";
            echo '<div id="' . $messageStyle . '">' . htmlspecialchars($_SESSION['message']) . '</div>';
            unset($_SESSION['message']); // Supprimer le message après affichage
            unset($_SESSION['message_type']); // Supprimer le type après affichage
        }
    ?>
</div>

<?php if (!empty($products)) : ?>
    <div id="product-list">
        <?php foreach ($products as $product) : ?>
                <div id="one-product">
                    <div>
                        <?php if($product['image_grade'] == null):?>
                            <img src="<?php echo $base; ?>/public/admin/ressources/default_images/grade.webp" alt="Image du grade" />
                        <?php else:?>
                            <img src="<?php echo $base; ?>/public/api/files/<?php echo $product['image_grade']; ?>" alt="Image du grade" />
                        <?php endif?>

                        <h3 title="<?= htmlspecialchars($product['nom_grade']) ?>">
                            <?= htmlspecialchars($product['nom_grade']) ?>
                        </h3>
                        <?php if (!empty($product['description_grade'])) { ?>
                            <p><?= htmlspecialchars($product['description_grade'])?></p>
                        <?php } ?>
                        <p>-- Prix : <?= number_format(htmlspecialchars($product['prix_grade']), 2, ',', ' ') ?> € --</p>
                    </div>
                    <div>
                        <p id="adhesion-status">

                            <?php if ($currentUserGradeId !== null && (int)$product['id_grade'] === $currentUserGradeId): ?>
                                <button id="detention">Vous détenez ce grade</button>
                            <?php elseif ($currentUserReduction !== null && (float)$product['reduction_grade'] < $currentUserReduction): ?>
                                <button id="detention" disabled>Grade inférieur indisponible</button>
                            <?php else: ?>
                                <a id="buy-button" href="<?php echo $base; ?>grade_subscription?id=<?= htmlspecialchars($product['id_grade']) ?>">
                                    Acheter
                                </a>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <p>Aucun grade trouvé.</p>
<?php endif; ?>




</body>
</html>