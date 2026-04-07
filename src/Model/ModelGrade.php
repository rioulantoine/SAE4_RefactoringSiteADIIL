<?php

require_once __DIR__ . '/database.php';

function getGrades(DB $db): array
{
    return $db->select(
        "SELECT * FROM GRADE WHERE deleted = false ORDER BY prix_grade"
    );
}

function getUserGrade(DB $db, int $userId): ?array
{
    $rows = $db->select(
        "SELECT GRADE.id_grade, GRADE.reduction_grade
         FROM ADHESION
         INNER JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade
         WHERE ADHESION.id_membre = ?
         LIMIT 1",
        "i",
        [$userId]
    );

    return $rows[0] ?? null;
}
