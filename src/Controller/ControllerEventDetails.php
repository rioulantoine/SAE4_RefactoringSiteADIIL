<?php


$isLoggedIn = isset($_SESSION["userid"]);
$db = new DB();

$show = 8;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $eventid = $_GET['id'];
    // image et description  enlevé pour le moment, à remettre si on les ajoute à la base de données
    $event = $db->select(
        "SELECT EVENEMENT.nom_evenement, EVENEMENT.xp_evenement, EVENEMENT.places_evenement, EVENEMENT.prix_evenement, EVENEMENT.reductions_evenement, EVENEMENT.lieu_evenement, EVENEMENT.date_evenement FROM EVENEMENT WHERE id_evenement = ?",
        "i",
        [$eventid]
    );
    if(empty($event) || is_null($event)){
        header("Location: /accueil");
        exit;
    }
    $event = $event[0];

    if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show']) {
        $show = (int) $_GET['show'];
    }

}else{
    header("Location: /accueil");
    exit;
}





require_once __DIR__ . '/../View/event_details.php';