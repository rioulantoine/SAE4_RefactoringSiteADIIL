<?php

require_once __DIR__ . '/database.php';

function deleteAccount($db, $userId)
{
    $db->query(
        "CALL suppressionCompte ( ? );",
        "i",
        [$userId]
    );
}