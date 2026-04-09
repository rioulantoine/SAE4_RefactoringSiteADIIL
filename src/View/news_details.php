<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/footer_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/news_details_style.css">
</head>

<body class="news-details-page">

<main class="news-details-content">
    <section class="news-details">
        <?php if($event['image_actualite'] == null):?>
            <img src="<?php echo $base; ?>public/admin/ressources/default_images/event.jpg" alt="Image de l'actualite">
        <?php else:?>
            <img src="<?php echo $base; ?>public/api/files/<?php echo $event['image_actualite']; ?>" alt="Image de l'actualite">
        <?php endif?>
        <h1><?php echo strtoupper($event['titre_actualite']); ?></h1>

        <div>
            <h2>
                <?php echo date('d/m/Y', strtotime($event['date_actualite'])); ?>
            </h2>
        </div>
        <ul></ul>
        <p>
            <?php echo nl2br(htmlspecialchars($event['contenu_actualite'])); ?>
        </p>
    </section>
</main>

</body>
</html>