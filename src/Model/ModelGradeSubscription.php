<?php

require_once __DIR__ . '/database.php';

function getGradeById(DB $db, int $gradeId): ?array
{
    $grade = $db->select(
        "SELECT * FROM GRADE WHERE id_grade = ?",
        "i",
        [$gradeId]
    );

    return $grade[0] ?? null;
}

function getCurrentAdhesion(DB $db, int $userId): array
{
    return $db->select(
        "SELECT * FROM ADHESION WHERE id_membre = ?",
        "i",
        [$userId]
    );
}

function deleteCurrentAdhesion(DB $db, int $userId): void
{
    $db->query(
        "DELETE FROM ADHESION WHERE id_membre = ?",
        "i",
        [$userId]
    );
}

function createAdhesion(DB $db, int $userId, int $gradeId, float $price, string $paymentMode): void
{
    $db->query(
        "INSERT INTO ADHESION (id_membre, id_grade, prix_adhesion, paiement_adhesion, date_adhesion) VALUES (?, ?, ?, ?, NOW())",
        "iiss",
        [$userId, $gradeId, $price, $paymentMode]
    );
}
