<?php




$db = new DB();
$isLoggedIn = isset($_SESSION["userid"]);
$show = 5;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['show']) && is_numeric($_GET['show'])) {
    $show = (int) $_GET['show'];
}



$date = getdate();
$sql_date = $date["year"]."-".$date["mon"]."-".$date["mday"];
$joursFr = [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
$moisFr = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];
$current_date = new DateTime(date("Y-m-d"));

$events_to_display = $db->select(
    "SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement FROM EVENEMENT WHERE date_evenement >= ? AND deleted = false ORDER BY date_evenement ASC;",
    "s",
    [$sql_date]
);
$passed_events = $db->select(
    "SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement FROM EVENEMENT WHERE date_evenement < ? AND deleted = false ORDER BY date_evenement ASC LIMIT ?;",
    "si",
    [$sql_date, $show]
);

$events_to_display = array_merge($events_to_display, $passed_events);


$closest_event_id = "";

$events_ready = [];
foreach ($events_to_display as $event) {
    $eventid = $event["id_evenement"];
    $event_date = substr($event['date_evenement'], 0, 10);
    $event_date_info = getdate(strtotime($event_date));
    $event_date_obj = new DateTime($event_date);
    $other_classes = "";
    $isPassed = false;
    $date_pin_class = "";
    $date_pin_label = "";
    $closest_event_id = "";

    if ($event_date_obj < $current_date) {
        $date_pin_class = "passed";
        $date_pin_label = "Passé";
        $other_classes = 'passed';
        $isPassed = true;
        
    } elseif ($event_date_obj == $current_date) {
        $date_pin_class = "today";
        $date_pin_label = "Aujourd'hui";
        $closest_event_id = "closest-event";
    } else {
        $date_pin_class = "upcoming";
        $date_pin_label = "A venir";
        $closest_event_id = "closest-event";
        
    }

    $events_ready[] = [
        'id_evenement' => $eventid,
        'nom_evenement' => $event['nom_evenement'],
        'lieu_evenement' => $event['lieu_evenement'],
        'date_affichage' => ucwords($joursFr[$event_date_info['wday']].' '.$event_date_info["mday"].' '.$moisFr[$event_date_info['mon']].' '.$event_date_info["year"]),
        'date_pin_class' => $date_pin_class,
        'date_pin_label' => $date_pin_label,
        'other_classes' => $other_classes,
        'closest_event_id' => $closest_event_id,
        'isPassed' => $isPassed,
        'isPlaceDisponible' => $db->select(
            "SELECT (EVENEMENT.places_evenement - (SELECT COUNT(*) FROM INSCRIPTION WHERE INSCRIPTION.id_evenement = EVENEMENT.id_evenement)) > 0 AS isPlaceDisponible FROM EVENEMENT WHERE EVENEMENT.id_evenement = ? ;",
            "i",
            [$eventid])[0]['isPlaceDisponible']
    ];
}



require_once __DIR__ . '/../View/events.php';