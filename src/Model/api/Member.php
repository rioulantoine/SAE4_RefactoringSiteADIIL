<?php
namespace model;

use Filter;
use model\Role;
use JsonSerializable;

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Role.php';

class Member extends BaseModel implements JsonSerializable
{
    public function delete() : void
    {
        $this->getProfilePic()?->deleteFile();

        $this->DB->query("DELETE FROM ASSIGNATION WHERE id_membre = ?", "i", [$this->id]);
        $this->DB->query("CALL suppressionCompte(?)", "i", [$this->id]);
    }

    public function update(string $nom, string $prenom, string $email, string $tp, int $xp) : Member
    {
        $this->DB->query("UPDATE MEMBRE SET nom_membre = ?, prenom_membre = ?, email_membre = ?, tp_membre = ?, xp_membre = ? WHERE id_membre = ?", "ssssii", [$nom, $prenom, $email, $tp, $xp, $this->id]);

        return $this;
    }

    public function updateProfilePic(File $pp) : Member
    {
        $this->DB->query("UPDATE MEMBRE SET pp_membre = ? WHERE id_membre = ?", "si", [$pp->getFileName(), $this->id]);

        return $this;
    }

    public static function create(string $nom, string $prenom, string $email, File | null $pp, string $tp) : Member
    {
        $DB = new \DB();
        
        $ppPath = $pp !== null ? $pp->getFileName() : "default.png";

        $id = $DB->query("INSERT INTO MEMBRE (nom_membre, prenom_membre, email_membre, pp_membre, tp_membre) VALUES (?,?,?,?,?)", "sssss", [$nom, $prenom, $email, $ppPath, $tp]);

        return new Member($id);
    }

    public function toJson(): array
    {
        $result = $this->DB->select("SELECT id_membre, nom_membre, prenom_membre, email_membre, xp_membre, discord_token_membre, pp_membre, tp_membre, (SELECT COUNT(*) FROM ASSIGNATION WHERE MEMBRE.id_membre = ASSIGNATION.id_membre) as nb_roles
                                      FROM MEMBRE WHERE id_membre = ?", "i", [$this->id]);

        return $result[0];
    }

    public static function getInstance($id) : ?Member
    {
        $DB = new \DB();
        $result = $DB->select("SELECT * FROM MEMBRE WHERE id_membre = ?", "i", [$id]);

        if (count($result) == 0) {
            return null;
        }

        return new Member($id);
    }

    public function getProfilePic(): File | null
    {
        $pp = $this->toJson()["pp_membre"];
        
        if (str_starts_with($pp, 'http')) {
            return null;
        }
        
        return File::getFile($pp);
    }

    public function setRoles(array $roles): bool
    {
        $rolesObjects = [];
        foreach ($roles as $role) {
            $roleObj = Role::getInstance(\filter::int($role));
            if (is_null($roleObj)) {
                return false;
            }
            $rolesObjects[] = $roleObj;
        }

        $this->DB->query("DELETE FROM ASSIGNATION WHERE id_membre = ?", "i", [$this->id]);

        foreach ($rolesObjects as $roleObj) {
            $roleObj->addMember($this);
        }

        return true;
    }

    public static function bulkFetch(): array
    {
        $DB = new \DB();
        return $DB->select("SELECT * FROM MEMBRE");
    }

    public function getRoles(): array
    {
        $result = $this->DB->select("SELECT id_role
                               FROM ASSIGNATION
                               WHERE ASSIGNATION.id_membre = ?", "i", [$this->id]);

        $roles = [];
        foreach ($result as $role) {
            $roles[] = Role::getInstance($role["id_role"]);
        }

        return $roles;
    }

    public function toJsonWithRoles() : array
    {
        $data =  $this->toJson();
        $data["roles"] = [];

        foreach ($this->getRoles() as $role) {
            $data["roles"][] = $role->toJson();
        }

        return $data;
    }

    public function __toString()
    {
        return json_encode($this->toJsonWithRoles());
    }

    public function jsonSerialize(): array
    {
        return $this->toJsonWithRoles();
    }
}