<?php

require_once __DIR__ . '/database.php';

function getNewsById(DB $db, int $newsId): ?array
{
    $rows = $db->select(
        'SELECT * FROM ACTUALITE WHERE id_actualite = ?',
        'i',
        [$newsId]
    );

    return $rows[0] ?? null;
}
