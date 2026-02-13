<?php

require_once __DIR__ . '/../Model/database.php';
require_once __DIR__ . '/../../temp-site/files_save.php';

// Vérifie si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION["userid"]);
if (!$isLoggedIn) {
    header("Location: $base/login");
    exit;
}

$userid = $_SESSION["userid"];

// Vérifie que la requête est POST et contient les données nécessaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_SESSION["userid"];
    $eventid = $_POST["eventid"];

    $db = new DB();
    if(isset($_POST["price"], $_POST["eventid"])){
        $db->query(
            "INSERT INTO `INSCRIPTION` (`id_membre`, `id_evenement`, `date_inscription`, `paiement_inscription`, `prix_inscription`)
            VALUES (?, ?, NOW(), 'WEB', ?);",
            "iid",
            [$userid, $eventid, $_POST["price"]]
        );
        $xp = $db->select("SELECT xp_evenement FROM EVENEMENT WHERE id_evenement = ?", "i", [$eventid])[0]['xp_evenement'];
        $db->query(
            "UPDATE MEMBRE SET MEMBRE.xp_membre = MEMBRE.xp_membre + ? where MEMBRE.id_membre = ?;",
            "ii",
            [$xp, $userid]
        );
        header("Location: $base/events");
        exit;
    }
    elseif(isset($_POST["eventid"])){
            $event = $db->select(
                "SELECT nom_evenement, xp_evenement, prix_evenement, reductions_evenement FROM EVENEMENT WHERE id_evenement = ? ;",
                "i",
                [$eventid]
            );
            if(empty($event)){
                header("Location: $base/acceuil");
                exit;
            }
            $event = $event[0];
            $title = $event["nom_evenement"];
            $xp = $event["xp_evenement"];
            $price = $event["prix_evenement"];

            $isDiscounted = boolval($event["reductions_evenement"]);
            $user_reduction = 1;

            if($isDiscounted){
                $user_reduction = $db->select(
                    "SELECT reduction_grade FROM ADHESION 
                    JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade
                    WHERE id_membre = ? AND reduction_grade > 0 order by ADHESION.date_adhesion DESC LIMIT 1",
                    "i",
                    [$userid]
                );
                if(!empty($user_reduction)){
                    $user_reduction = 1 - ($user_reduction[0]["reduction_grade"]/100);
                }else{
                    $user_reduction = 1;
                }
            }
        }else{
            header("Location: $base/login");
            exit;
        }
    }else{
        header("Location: $base/login");
        exit;
    }

require_once __DIR__ . '/../View/event_subscription.php';
?>