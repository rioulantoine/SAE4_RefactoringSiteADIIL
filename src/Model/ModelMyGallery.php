<?php

require_once __DIR__ . '/database.php';

function getMyGalleryEvent($db, $eventid)
{
    return $db->select(
        "SELECT `nom_evenement` FROM EVENEMENT WHERE id_evenement = ?",
        "i",
        [$eventid]
    )[0];
}

function getMyGalleryMedias($db, $userid, $eventid, $limit)
{
    return $db->select(
        "SELECT id_media, url_media FROM `MEDIA` WHERE id_membre = ? and id_evenement = ? ORDER by date_media ASC LIMIT ?;",
        "iii",
        [$userid, $eventid, $limit]
    );
}
