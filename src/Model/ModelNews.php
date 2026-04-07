<?php

require_once __DIR__ . '/database.php';

function getNews(DB $db, int $show): array
{
    return $db->select(
        'SELECT id_actualite, titre_actualite, date_actualite FROM ACTUALITE WHERE date_actualite <= NOW() ORDER BY date_actualite ASC LIMIT ?;',
        'i',
        [$show]
    );
}
