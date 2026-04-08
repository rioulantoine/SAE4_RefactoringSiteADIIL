<?php

require_once __DIR__ . '/database.php';

function getEventCountById($db, $eventId)
{
    $result = $db->select(
        'SELECT COUNT(*) as count FROM INSCRIPTION WHERE id_evenement = ?',
        'i',
        [$eventId]
    );

    return $result[0]['count'];
}

function isUserSubscribedToEvent($db, $userId, $eventId)
{
    return !empty($db->select(
        'SELECT id_membre FROM INSCRIPTION WHERE id_membre = ? AND id_evenement = ?',
        'ii',
        [$userId, $eventId]
    ));
}
