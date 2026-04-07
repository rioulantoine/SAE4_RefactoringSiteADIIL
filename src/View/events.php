<?php
$show = isset($show) ? $show : 5;
$search = isset($search) ? $search : '';
$available = isset($available) ? $available : false;
$events_ready = isset($events_ready) ? $events_ready : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title>Evenements - ADIIL</title>

    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/events_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/footer_style.css">
</head>

<body class="body_margin">

<h1>LES EVENEMENTS</h1>

<div class="filter-bar">
    <form action="" method="GET">
        <input type="hidden" name="page" value="events">
        <input type="text" name="search" placeholder="Rechercher un evenement..." value="<?php echo htmlspecialchars($search); ?>">

        <label class="checkbox-container">
            Places disponibles uniquement
            <input type="checkbox" name="available" <?php echo $available ? 'checked' : ''; ?>>
            <span class="checkmark"></span>
        </label>

        <button type="submit" class="filter-btn">Filtrer</button>
        <a href="<?php echo $base; ?>events" class="reset-btn">Reinitialiser</a>
    </form>
</div>

<section>
    <?php if (empty($search)): ?>
        <a class="show-more" href="<?php echo $base; ?>events?show=<?php echo $show + 10; ?>">Voir plus loin dans le passe</a>
    <?php endif; ?>

    <div class="events-display">
        <?php foreach ($events_ready as $event): ?>
            <div class="event-box <?php echo $event['other_classes']; ?>" id="<?php echo $event['closest_event_id']; ?>">
                <div class="timeline-event">
                    <h4><?php echo $event['date_affichage']; ?></h4>
                    <div class="vertical-line"></div>
                    <p><?php echo $event['date_pin_label']; ?></p>
                    <div class="timeline-marker <?php echo $event['date_pin_class']; ?>"></div>
                </div>

                <div class="event" event-id="<?php echo $event['id_evenement']; ?>">
                    <div>
                        <h2><?php echo htmlspecialchars($event['nom_evenement']); ?></h2>
                        <?php echo ucwords(htmlspecialchars($event['lieu_evenement'])); ?>
                    </div>
                    <h4 class="<?php echo $event['subscription_class']; ?>"><?php echo $event['subscription_label']; ?></h4>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script src="<?php echo $base; ?>public/scripts/event_details_redirect.js"></script>
<script src="<?php echo $base; ?>public/scripts/scroll_to_closest_event.js"></script>
</body>
</html>
