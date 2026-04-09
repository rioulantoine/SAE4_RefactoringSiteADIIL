<?php

require_once __DIR__ . '/database.php';

function createMedia($db, $fileName, $sqlDate, $userId, $eventId)
{
    return $db->query(
        "INSERT INTO MEDIA VALUES (NULL, ?, ?, ?, ?)",
        "ssii",
        [$fileName, $sqlDate, $userId, $eventId]
    );
}


function getMediaById($db, $mediaId, $eventId)
{
    $result = $db->select(
        "SELECT url_media FROM MEDIA WHERE id_media = ? AND id_evenement = ?",
        "ii",
        [$mediaId, $eventId]
    );
    
    return empty($result) ? null : $result[0]['url_media'];
}

function deleteMedia($db, $mediaId, $eventId)
{
    $db->query(
        "DELETE FROM MEDIA WHERE id_media = ? AND id_evenement = ?",
        "ii",
        [$mediaId, $eventId]
    );
}

function getAllMediasByEvent($db, $eventId)
{
    return $db->select(
        "SELECT id_media, url_media, date_media, id_membre, id_evenement FROM MEDIA WHERE id_evenement = ?",
        "i",
        [$eventId]
    );
}
