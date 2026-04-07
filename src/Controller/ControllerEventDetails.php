<?php


$isLoggedIn = isset($_SESSION["userid"]);
$db = new DB();

$show = 8;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $eventid = $_GET['id'];
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
    //affichage de la date de l'event
    $current_date = new DateTime(date("Y-m-d"));
    $event_date = new DateTime(substr($event['date_evenement'], 0, 10));
    
    //utilisateur deja inscrit
    @$a = $db->select("SELECT * FROM INSCRIPTION WHERE id_evenement = ? AND id_membre = ?;","ii",[$_GET['id'], $_SESSION['userid']]);
    $isSubscribed = !empty($a);

}else{
    header("Location: /accueil");
    exit;
}





require_once __DIR__ . '/../View/event_details.php';