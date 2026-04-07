<?php

require_once __DIR__ . '/database.php';

class ModelLogin
{
    private DB $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    public function getUserByEmail(string $mail): ?array
    {
        $selection = $this->db->select(
            "SELECT id_membre, email_membre, password_membre FROM MEMBRE WHERE email_membre = ?",
            "s",
            [$mail]
        );

        return $selection[0] ?? null;
    }

    public function hasAssignedRole(int $memberId): bool
    {
        $result = $this->db->select(
            "SELECT COUNT(*) as nb_roles FROM ASSIGNATION WHERE id_membre = ?",
            "i",
            [$memberId]
        );

        return !empty($result) && (int)$result[0]['nb_roles'] > 0;
    }
}
