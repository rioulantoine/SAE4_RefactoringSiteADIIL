<?php

namespace model;

use JsonSerializable;

require_once __DIR__ . '/BaseModel.php';

class Role extends BaseModel implements JsonSerializable
{
    public static function create(string $name, bool $p_log, bool $p_boutique, bool $p_reunion, bool $p_utilisateur,
                                  bool $p_grade, bool $p_role, bool $p_actualite, bool $p_evenement, bool $p_comptabilite,
                                  bool $p_achat, bool $p_moderation) : Role
    {
        $DB = new \DB();

        $id = $DB->query("INSERT INTO ROLE (nom_role, p_log_role, p_boutique_role, p_reunion_role, p_utilisateur_role, p_grade_role, p_roles_role, p_actualite_role, p_evenements_role, p_comptabilite_role, p_achats_role, p_moderation_role)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", "siiiiiiiiiii", [$name, $p_log, $p_boutique, $p_reunion, $p_utilisateur, $p_grade, $p_role, $p_actualite, $p_evenement, $p_comptabilite, $p_achat, $p_moderation]);

        return new Role($id);
    }

    public function update(string $name, bool $p_log, bool $p_boutique, bool $p_reunion, bool $p_utilisateur,
                           bool $p_grade, bool $p_role, bool $p_actualite, bool $p_evenement, bool $p_comptabilite,
                           bool $p_achat, bool $p_moderation) : Role
    {
        $this->DB->query("UPDATE ROLE SET nom_role = ?, p_log_role = ?, p_boutique_role = ?, p_reunion_role = ?, p_utilisateur_role = ?, p_grade_role = ?, p_roles_role = ?, p_actualite_role = ?, p_evenements_role = ?, p_comptabilite_role = ?, p_achats_role = ?, p_moderation_role = ? WHERE id_role = ?", "siiiiiiiiiiii", [$name, $p_log, $p_boutique, $p_reunion, $p_utilisateur, $p_grade, $p_role, $p_actualite, $p_evenement, $p_comptabilite, $p_achat, $p_moderation, $this->id]);

        return $this;
    }

    public function delete() : void
    {
        $this->DB->query("DELETE FROM ASSIGNATION WHERE id_role = ?", "i", [$this->id]);
        $this->DB->query("DELETE FROM ROLE WHERE id_role = ?", "i", [$this->id]);
    }

    public static function getInstance($id): ?Role
    {
        $DB = new \DB();
        $result = $DB->select("SELECT * FROM ROLE WHERE id_role = ?", "i", [$id]);

        if (count($result) == 0) {
            return null;
        }

        return new Role($id);
    }

    public static function bulkFetch() : array
    {
        $DB = new \DB();
        return $DB->select("SELECT * FROM ROLE");
    }

    public function jsonSerialize(): array
    {
        $res = $this->DB->select("SELECT * FROM ROLE WHERE id_role = ?", "i", [$this->id]);
        return !empty($res) ? $res[0] : [];
    }

    public function __toString() : string
    {
        return json_encode($this->jsonSerialize());
    }
}