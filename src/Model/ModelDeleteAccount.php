<?php

require_once __DIR__ . '/database.php';

function deleteAccountByUserId(DB $db, int $userId): void
{
    $db->query(
        "CALL suppressionCompte ( ? );",
        "i",
        [$userId]
    );
}
