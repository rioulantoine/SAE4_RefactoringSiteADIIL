<?php

require_once __DIR__ . '/database.php';

class ModelCart
{
    private DB $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    public function getProductsByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $query = "SELECT * FROM ARTICLE WHERE id_article IN ($placeholders)";
        $types = str_repeat('i', count($ids));

        return $this->db->select($query, $types, $ids);
    }

    public function getMemberReductionPercent(int $userId): ?float
    {
        $memberReduction = $this->db->select(
            "SELECT reduction_grade
             FROM ADHESION
             INNER JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade
             WHERE ADHESION.id_membre = ? AND reduction_grade > 0
             LIMIT 1",
            "i",
            [$userId]
        );

        if (empty($memberReduction)) {
            return null;
        }

        return (float) ($memberReduction[0]['reduction_grade'] ?? 0);
    }
}
