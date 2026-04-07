<?php
// 1. Inclusions nécessaires
require_once __DIR__ . '/../Model/api/Event.php'; 
use model\Event;

$db = new DB();
$isLoggedIn = isset($_SESSION["userid"]);

$show = isset($_GET['show']) && is_numeric($_GET['show']) ? (int)$_GET['show'] : 5;
$search = $_GET['search'] ?? '';
$available = isset($_GET['available']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title>Événements - ADIIL</title>

    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/events_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/footer_style.css">
</head>

<body class="body_margin">

<h1>LES ÉVÉNEMENTS</h1>

<div class="filter-bar">
    <form action="" method="GET">
        <input type="hidden" name="page" value="events">
        <input type="text" name="search" placeholder="Rechercher un événement..." value="<?php echo htmlspecialchars($search); ?>">
        
        <label class="checkbox-container">
            Places disponibles uniquement
            <input type="checkbox" name="available" <?php echo $available ? 'checked' : ''; ?>>
            <span class="checkmark"></span>
        </label>

        <button type="submit" class="filter-btn">Filtrer</button>
        <a href="<?php echo $base; ?>events" class="reset-btn">Réinitialiser</a>
    </form>
</div>

<h1>LES EVENEMENTS</h1>
<section>
    <?php if(empty($search)): ?>
        <a class="show-more" href="<?php echo $base; ?>events?show=<?php echo $show + 10; ?>">Voir plus loin dans le passé</a>
    <?php endif; ?>

    <div class="events-display">
        <?php
            $current_date_obj = new DateTime(date("Y-m-d"));
            $joursFr = [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
            $moisFr = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];

            // 5. Appel au Model filtré
            $events_to_display = Event::fetchFiltered($search, $available, 100);

            $closest_event_id = "";

            foreach ($events_to_display as $event):
                $eventid = $event["id_evenement"];
                $event_date_raw = substr($event['date_evenement'], 0, 10);
                $event_date_info = getdate(strtotime($event_date_raw));
                $event_date_obj = new DateTime($event_date_raw);
                
                $other_classes = "";
                $isPassed = false;

                if ($event_date_obj < $current_date_obj) {
                    $date_pin_class = "passed";
                    $date_pin_label = "Passé";
                    $other_classes = 'passed';
                    $isPassed = true;
                } elseif ($event_date_obj == $current_date_obj) {
                    $date_pin_class = "today";
                    $date_pin_label = "Aujourd'hui";
                    $closest_event_id = "closest-event";
                } else {
                    $date_pin_class = "upcoming";
                    $date_pin_label = "À venir";
                    if (empty($closest_event_id)) $closest_event_id = "closest-event";
                }
        ?>
            <div class="event-box <?php echo $other_classes; ?>" id="<?php echo $closest_event_id; ?>">
                <div class="timeline-event">
                    <h4><?php echo ucwords($joursFr[$event_date_info['wday']]." ".$event_date_info["mday"]." ".$moisFr[$event_date_info['mon']]); ?></h4>
                    <div class="vertical-line"></div>
                    <p><?php echo $date_pin_label; ?></p>
                    <div class="timeline-marker <?php echo $date_pin_class; ?>"></div>
                </div>

                <div class="event" event-id="<?php echo $eventid; ?>">
                    <div>
                        <h2><?php echo htmlspecialchars($event['nom_evenement']); ?></h2>
                        <?php echo ucwords(htmlspecialchars($event["lieu_evenement"])); ?>
                    </div>
                    <h4 <?php
                        // Calcul des places restantes
                        $remaining = $event['places_evenement'] - ($db->select("SELECT COUNT(*) as count FROM INSCRIPTION WHERE id_evenement = ?", "i", [$eventid])[0]['count']);
                        
                        if($isPassed) {
                            $btn_class = "event-full"; $btn_label = "Passé";
                        } elseif ($remaining <= 0) {
                            $btn_class = "event-full"; $btn_label = "Complet";
                        } else {
                            $btn_class = "event-not-subscribed hover_effect"; $btn_label = "S'inscrire";
                        }
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

                        if($isLoggedIn && !$isPassed){
                            $sub = $db->select("SELECT id_membre FROM INSCRIPTION WHERE id_membre = ? AND id_evenement = ?", "ii", [$_SESSION['userid'], $eventid]);
                            if(!empty($sub)) { $btn_class = "event-subscribed"; $btn_label = "Inscrit"; }
                        }
                        echo "class=\"$btn_class\"";
                    ?>>
                        <?php echo $btn_label; ?>
                    </h4>
                </div>
            </div>
            <?php $closest_event_id = ""; ?>
        <?php endforeach; ?>
    </div>
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

    <script src="<?php echo $base; ?>public/scripts/event_details_redirect.js"></script>
    <script src="<?php echo $base; ?>public/scripts/scroll_to_closest_event.js"></script>
</body>
</html>