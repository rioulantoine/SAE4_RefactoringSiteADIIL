<?php

$db = new DB();
$isLoggedIn = isset($_SESSION['userid']);

$podium = $db->select(
    "SELECT prenom_membre, xp_membre, pp_membre FROM MEMBRE ORDER BY xp_membre DESC LIMIT 3;"
);

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
$eventsToDisplay = $db->select(
    "SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement FROM EVENEMENT WHERE date_evenement >= ? ORDER BY date_evenement ASC LIMIT 2;",
    "s",
    [$today]
);

foreach ($eventsToDisplay as &$event) {
    $eventid = $event['id_evenement'];

    $isPlaceDisponible = $db->select(
        "SELECT (EVENEMENT.places_evenement - (SELECT COUNT(*) FROM INSCRIPTION WHERE INSCRIPTION.id_evenement = EVENEMENT.id_evenement)) > 0 AS isPlaceDisponible FROM EVENEMENT WHERE EVENEMENT.id_evenement = ? ;",
        "i",
        [$eventid]
    )[0]['isPlaceDisponible'];

    if ($isPlaceDisponible) {
        $event['subscription_class'] = 'event-not-subscribed hover_effect';
        $event['subscription_label'] = "S'inscrire";
    } else {
        $event['subscription_class'] = 'event-full';
        $event['subscription_label'] = 'Complet';
    }

    if ($isLoggedIn) {
        $isSubscribed = !empty($db->select(
            "SELECT MEMBRE.id_membre FROM MEMBRE JOIN INSCRIPTION on MEMBRE.id_membre = INSCRIPTION.id_membre WHERE MEMBRE.id_membre = ? AND INSCRIPTION.id_evenement = ? ;",
            "ii",
            [$_SESSION['userid'], $eventid]
        ));

        if ($isSubscribed) {
            $event['subscription_class'] = 'event-subscribed';
            $event['subscription_label'] = 'Inscrit';
        }
    }
}
unset($event);

require_once __DIR__ . '/../View/accueil.php';
