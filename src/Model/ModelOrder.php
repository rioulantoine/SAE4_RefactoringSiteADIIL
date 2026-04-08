<?php

require_once __DIR__ . '/database.php';

function getOrderProducts($db, $productIds)
{
    if (empty($productIds)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $query = "SELECT * FROM ARTICLE WHERE id_article IN ($placeholders)";
    $types = str_repeat('i', count($productIds));

    return $db->select($query, $types, $productIds);
}

function getUserAdhesionWithReduction($db, $userId)
{
    return $db->select(
        "SELECT * FROM ADHESION
        INNER JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade
        WHERE ADHESION.id_membre = ? AND reduction_grade > 0",
        'i',
        [$userId]
    );
}

function buyArticle($db, $userId, $productId, $quantity, $paymentMode)
{
    $db->query(
        'CALL achat_article(?, ?, ?, ?)',
        'iiis',
        [$userId, $productId, $quantity, $paymentMode]
    );
}
