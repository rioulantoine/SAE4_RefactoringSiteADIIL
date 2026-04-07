<?php

require_once __DIR__ . '/database.php';

function modelEventDetailsGetEventById(DB $db, int $eventId): ?array
{
    $event = $db->select(
        "SELECT nom_evenement, xp_evenement, places_evenement, prix_evenement, reductions_evenement, lieu_evenement, date_evenement
         FROM EVENEMENT WHERE id_evenement = ?",
        "i",
        [$eventId]
    );

    return $event[0] ?? null;
}

function modelEventDetailsIsUserSubscribed(DB $db, int $eventId, int $userId): bool
{
    $subscription = $db->select(
        "SELECT id_membre FROM INSCRIPTION WHERE id_evenement = ? AND id_membre = ?",
        "ii",
        [$eventId, $userId]
    );

    return !empty($subscription);
}

function modelEventDetailsGetUserMedias(DB $db, int $eventId, int $userId, int $limit = 4): array
{
    return $db->select(
        "SELECT url_media FROM MEDIA WHERE id_membre = ? AND id_evenement = ? ORDER BY date_media ASC LIMIT ?",
        "iii",
        [$userId, $eventId, $limit]
    );
}

function modelEventDetailsGetGeneralMedias(DB $db, int $eventId, int $limit): array
{
    return $db->select(
        "SELECT url_media FROM MEDIA WHERE id_evenement = ? ORDER BY date_media ASC LIMIT ?",
        "ii",
        [$eventId, $limit]
    );
}
