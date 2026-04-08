<?php
require_once __DIR__ . '/../../Service/files_save.php';
require_once __DIR__ . '/../../Model/database.php';
$db = new DB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mediaid'], $_POST['eventid'])) {
        $fileName = $db->select(
            "SELECT url_media FROM `MEDIA` WHERE id_media = ? AND id_evenement = ?",
            "ii",
            [$_POST['mediaid'], $_POST['eventid']]
        )[0]['url_media'];

        if(deleteFile($fileName)){
            // Met à jour la base de données avec le nom du fichier
            $db->query(
                "DELETE FROM MEDIA WHERE id_media = ? AND id_evenement = ?",
                "ii",
                [$_POST['mediaid'], $_POST['eventid']]
            );
        }
        
        // Redirige en fonction de l'origine de la suppression
        $redirect = $_POST['redirect'] ?? 'my_gallery';

        if ($redirect === 'event_details') {
            header("Location: " . $base . "event_details?id=" . $_POST["eventid"]);
        } else {
            header("Location: my_gallery?eventid=" . $_POST["eventid"]);
        }
        exit();

    }else{
            header("Location: ". $base ."");
        exit();
    }

?>