<?php

require_once __DIR__ . '/database.php';

function getHomePodium($db)
{
    return $db->select(
        "SELECT prenom_membre, xp_membre, pp_membre FROM MEMBRE ORDER BY xp_membre DESC LIMIT 3;"
    );
}

function getHomeEvents($db, $today)
{
    return $db->select(
        "SELECT id_evenement, nom_evenement, lieu_evenement, date_evenement FROM EVENEMENT WHERE date_evenement >= ? ORDER BY date_evenement ASC LIMIT 2;",
        "s",
        [$today]
    );
}

function getHomePlaceStatus($db, $eventId)
{
    return $db->select(
        "SELECT (EVENEMENT.places_evenement - (SELECT COUNT(*) FROM INSCRIPTION WHERE INSCRIPTION.id_evenement = EVENEMENT.id_evenement)) > 0 AS isPlaceDisponible FROM EVENEMENT WHERE EVENEMENT.id_evenement = ? ;",
        "i",
        [$eventId]
    );
}

function isHomeUserSubscribed($db, $userId, $eventId)
{
    return $db->select(
        "SELECT MEMBRE.id_membre FROM MEMBRE JOIN INSCRIPTION on MEMBRE.id_membre = INSCRIPTION.id_membre WHERE MEMBRE.id_membre = ? AND INSCRIPTION.id_evenement = ? ;",
        "ii",
        [$userId, $eventId]
    );
}