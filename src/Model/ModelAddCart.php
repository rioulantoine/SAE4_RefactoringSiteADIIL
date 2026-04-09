<?php

require_once __DIR__ . '/database.php';

function doesProductExistForCart($db, $productId)
{
    $result = $db->select(
        "SELECT id_article FROM ARTICLE WHERE id_article = ?",
        "i",
        [$productId]
    );

    return !empty($result);
}