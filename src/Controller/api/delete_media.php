<?php
require_once __DIR__ . '/../../Service/files_save.php';
require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Model/ModelMedia.php';
require_once __DIR__ . '/../../Service/filter.php';

$db = new DB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mediaid'], $_POST['eventid'])) {
    $mediaId = filter::int($_POST['mediaid']);
    $eventId = filter::int($_POST['eventid']);
    
    // Récupère le chemin du fichier avant suppression
    $fileName = getMediaById($db, $mediaId, $eventId);
    
    if ($fileName !== null && deleteFile($fileName)) {
        // Supprime l'enregistrement de la base de données
        deleteMedia($db, $mediaId, $eventId);
    }
    
    // Redirige en fonction de l'origine de la suppression
    $redirect = $_POST['redirect'] ?? 'my_gallery';
    
    if ($redirect === 'event_details') {
        header("Location: " . $base . "event_details?id=" . $eventId);
    } else {
        header("Location: " . $base . "my_gallery?eventid=" . $eventId);
    }
    exit();
} else {
    header("Location: " . $base . "");
    exit();
}
?>