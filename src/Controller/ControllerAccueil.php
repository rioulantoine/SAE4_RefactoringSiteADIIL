<?php

require_once __DIR__ . '/../Model/ModelAccueil.php';

$db = new DB();
$isLoggedIn = isset($_SESSION['userid']);

$podium = getHomePodium($db);

foreach ($podium as &$member) {
    $xpLength = strlen($member['xp_membre']);
    $member['xp_size_class'] = 'xp-size-default';

    if ($xpLength >= 8) {
        $member['xp_size_class'] = 'xp-size-xl';
    } elseif ($xpLength >= 6) {
        $member['xp_size_class'] = 'xp-size-lg';
    }
}
unset($member);

$today = date('Y-m-d');
$eventsToDisplay = getHomeEvents($db, $today);

foreach ($eventsToDisplay as &$event) {
    $eventid = $event['id_evenement'];

    $isPlaceDisponible = getHomePlaceStatus($db, $eventid)[0]['isPlaceDisponible'];

    if ($isPlaceDisponible) {
        $event['subscription_class'] = 'event-not-subscribed hover_effect';
        $event['subscription_label'] = "S'inscrire";
    } else {
        $event['subscription_class'] = 'event-full';
        $event['subscription_label'] = 'Complet';
    }

    if ($isLoggedIn) {
        $isSubscribed = !empty(isHomeUserSubscribed($db, $_SESSION['userid'], $eventid));

        if ($isSubscribed) {
            $event['subscription_class'] = 'event-subscribed';
            $event['subscription_label'] = 'Inscrit';
        }
    }
}
unset($event);

require_once __DIR__ . '/../View/accueil.php';
