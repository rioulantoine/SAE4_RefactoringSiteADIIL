<?php

require_once __DIR__ . '/database.php';

class ModelSignin
{
    private DB $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    public function emailExists(string $mail): bool
    {
        $selection = $this->db->select(
            "SELECT id_membre FROM MEMBRE WHERE email_membre = ?",
            "s",
            [$mail]
        );

        return !empty($selection);
    }

    public function createAccount(string $lname, string $fname, string $mail, string $hashedPassword): void
    {
        $this->db->query(
            "CALL creationCompte ( ? , ? , ? , ? , ? );",
            "sssss",
            [$lname, $fname, $mail, $hashedPassword, 'defaultPP.png']
        );
    }
}
