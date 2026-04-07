<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualités</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/news_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/footer_style.css">
</head>
<body class="body_margin">
<h1>ACTUALITES</h1>
<section>
    <a class="show-more" href="<?php echo $base; ?>news?show=<?php echo $show + 10; ?>">Voir plus loin dans le passé</a>
    <div class="events-display">
        <?php foreach ($eventsToDisplay as $event): ?>
            <div class="event-box" id="<?php echo $event['isClosest'] ? 'closest-event' : ''; ?>">
                <div class="timeline-event">
                    <h4><?php echo $event['date_label']; ?></h4>
                    <div class="vertical-line"></div>
                </div>
                <div class="event" event-id="<?php echo $event['id_actualite']; ?>">
                    <div>
                        <h2 style="margin-bottom: 0px;"><?php echo htmlspecialchars($event['titre_actualite']); ?></h2>
                    </div>
                    <h4 class="event-not-subscribed">Consulter</h4>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
    const BASE_URL = '<?php echo $base; ?>';
</script>
<script src="<?php echo $base; ?>public/scripts/news_details_redirect.js"></script>
<script src="<?php echo $base; ?>public/scripts/scroll_to_closest_event.js"></script>
</body>
</html>
