<?php

namespace model;

use JsonSerializable;

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Member.php';

class Meeting extends BaseModel implements JsonSerializable
{
    public function delete() : void
    {
        $file = $this->getFile();
        if ($file) $file->deleteFile();
        $this->DB->query("DELETE FROM REUNION WHERE id_reunion = ?", "i", [$this->id]);
    }

    public function getFile() : File | null
    {
        $res = $this->DB->select("SELECT fichier_reunion FROM REUNION WHERE id_reunion = ?", "i", [$this->id]);
        if (empty($res)) return null;

        return File::getFile($res[0]['fichier_reunion']);
    }

    public function getUser() : Member
    {
        $res = $this->DB->select("SELECT id_membre FROM REUNION WHERE id_reunion = ?", "i", [$this->id]);
        return new Member($res[0]['id_membre']);
    }

    public static function create(string $date, File $file, Member $member) : Meeting
    {
        $DB = new \DB();
        $id = $DB->query("INSERT INTO REUNION (date_reunion, fichier_reunion, id_membre)
                    VALUES (?, ?, ?)", "ssi", [$date, $file->getFileName(), $member->getId()]);

        return new Meeting($id);
    }

    public static function getInstance($id): ?Meeting
    {
        $DB = new \DB();
        $result = $DB->select("SELECT * FROM REUNION WHERE id_reunion = ?", "i", [$id]);

        if (count($result) == 0) {
            return null;
        }

        return new Meeting($id);
    }

    public function jsonSerialize(): array
    {
        $res = $this->DB->select("SELECT id_reunion, date_reunion, fichier_reunion FROM REUNION WHERE id_reunion = ?", "i", [$this->id]);
        if (empty($res)) return [];

        $data = $res[0];
        $user = $this->getUser();
        $data['user'] = $user->toJson();

        return $data;
    }

    public static function bulkFetch() : array
    {
        $DB = new \DB();
        return $DB->select("SELECT * FROM REUNION");
    }

    public function __toString() : string
    {
        return json_encode($this->jsonSerialize());
    }
}