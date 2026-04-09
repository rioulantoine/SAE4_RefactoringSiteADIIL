<?php
require_once __DIR__ . '/../../Service/files_save.php';
require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Model/ModelMedia.php';
require_once __DIR__ . '/../../Service/filter.php';

$db = new DB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'], $_POST['userid'], $_POST['eventid'])) {
    $fileName = saveImageEvent();
    
    if ($fileName !== null) {
        $date = new DateTime();
        $sqlDate = $date->format('Y-m-d H:i:s');
        
        $userId = filter::int($_POST['userid']);
        $eventId = filter::int($_POST['eventid']);
        
        // Utilise le Model pour insérer le média
        createMedia($db, $fileName, $sqlDate, $userId, $eventId);
    }

    header("Location: " . $base . "my_gallery?eventid=" . $_POST["eventid"]);
    exit();
} else {
    header("Location: " . $base . "");
    exit();
}
?>