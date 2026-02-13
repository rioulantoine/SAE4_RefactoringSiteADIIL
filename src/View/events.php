<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <title>Evenements</title>
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/events_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/footer_style.css">
</head>
<body class="body_margin">
<h1>LES EVENEMENTS</h1>
<section>
    <a class="show-more" href="<?php echo $base; ?>events?show=<?php echo $show + 10; ?>">Voir plus loin dans le passé</a>
    <div class="events-display">
                <?php
                    foreach ($events_ready as $event):
                ?>
                    <div class="event-box <?php echo $event['other_classes'];?>" id="<?php echo $event['closest_event_id'] ?>">
                        <div class="timeline-event">
                            <h4> <?php echo $event['date_affichage'];?></h4>
                            <div class="vertical-line"></div>
                            <p> <?php echo $event['date_pin_label'];?></p>
                            <div class="timeline-marker <?php echo  $event['date_pin_class'] ?>">
                                <div class="time-line"></div>
                            </div>
                        </div>
                        <div class="event" event-id="<?php echo $event['id_evenement'];?>">
                            <div>
                                <h2><?php echo $event['nom_evenement'];?></h2>
                                <?php echo ucwords($event["lieu_evenement"]);?>
                            </div>
                            <h4
                                <?php

                                if($event['isPlaceDisponible']==1){
                                    $event_subscription_color_class = "event-not-subscribed hover_effect";
                                    $event_subscription_label = "S'inscrire";
                                }else{
                                    $event_subscription_color_class = "event-full";
                                    $event_subscription_label = "Complet";
                                }

                                if($isLoggedIn){
                                    $isSubscribed = !empty($db->select(
                                    "SELECT MEMBRE.id_membre FROM MEMBRE JOIN INSCRIPTION on MEMBRE.id_membre = INSCRIPTION.id_membre WHERE MEMBRE.id_membre = ? AND INSCRIPTION.id_evenement = ? ;",
                                    "ii",
                                    [$_SESSION['userid'], $event["id_evenement"]]
                                    ));
                                    
                                    if($isSubscribed){
                                        $event_subscription_color_class = "event-subscribed";
                                        $event_subscription_label = "Inscrit";
                                    }
                                }
                                
                                if($event['isPassed']){
                                    $event_subscription_color_class = "event-full";
                                    $event_subscription_label = "Passé";
                                }
                                echo "class=\"$event_subscription_color_class\"";
                                ?>>
                                <?php echo $event_subscription_label;?>
                            </h4>
                        </div>
                    </div>
                    <?php $closest_event_id = "";?>
                <?php endforeach; ?>
        </div>
</section>
    <script src="<?php echo $base; ?>public/scripts/event_details_redirect.js"></script>
    <script src="<?php echo $base; ?>public/scripts/scroll_to_closest_event.js"></script>
</body>
</html>
