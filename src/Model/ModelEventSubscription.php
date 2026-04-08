<?php

require_once __DIR__ . '/database.php';

function getEventSubscriptionEvent($db, $eventid)
{
    return $db->select(
        "SELECT nom_evenement, xp_evenement, prix_evenement, reductions_evenement FROM EVENEMENT WHERE id_evenement = ? ;",
        "i",
        [$eventid]
    );
}

function getEventSubscriptionReduction($db, $userid)
{
    return $db->select(
        "SELECT reduction_grade FROM ADHESION 
        JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade
        WHERE id_membre = ? AND reduction_grade > 0 order by ADHESION.date_adhesion DESC LIMIT 1",
        "i",
        [$userid]
    );
}

function getEventSubscriptionXp($db, $eventid)
{
    return $db->select(
        "SELECT xp_evenement FROM EVENEMENT WHERE id_evenement = ?",
        "i",
        [$eventid]
    );
}

function createEventSubscription($db, $userid, $eventid, $price)
{
    $db->query(
        "INSERT INTO `INSCRIPTION` (`id_membre`, `id_evenement`, `date_inscription`, `paiement_inscription`, `prix_inscription`)
        VALUES (?, ?, NOW(), 'WEB', ?);",
        "iid",
        [$userid, $eventid, $price]
    );
}

function addEventXp($db, $xp, $userid)
{
    $db->query(
        "UPDATE MEMBRE SET MEMBRE.xp_membre = MEMBRE.xp_membre + ? where MEMBRE.id_membre = ?;",
        "ii",
        [$xp, $userid]
    );
}

function deleteEventSubscription($db, $userid, $eventid)
{
    $db->query(
        "DELETE FROM INSCRIPTION WHERE id_membre = ? AND id_evenement = ?;",
        "ii",
        [$userid, $eventid]
    );
}