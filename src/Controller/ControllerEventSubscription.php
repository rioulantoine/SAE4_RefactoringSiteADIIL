<?php

require_once __DIR__ . '/../Model/ModelEventSubscription.php';
require_once __DIR__ . '/../Service/files_save.php';
// Vérifie si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION["userid"]);
if (!$isLoggedIn) {
    header("Location: $base/login");
    exit;
}

$userid = $_SESSION["userid"];
$savedPaymentInfo = isset($_SESSION['saved_payment_info_event']) ? $_SESSION['saved_payment_info_event'] : [];
$savedCardNumber = isset($savedPaymentInfo['numero_carte']) ? $savedPaymentInfo['numero_carte'] : '';
$savedExpiration = isset($savedPaymentInfo['expiration']) ? $savedPaymentInfo['expiration'] : '';

// Vérifie que la requête est POST et contient les données nécessaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_SESSION["userid"];
    $eventid = isset($_POST["eventid"]) ? $_POST["eventid"] : null;

    $db = new DB();

    // Désinscription à un évènement
    if (isset($_POST['unsubscribe']) && $_POST['unsubscribe'] === '1' && $eventid !== null) {
        deleteEventSubscription($db, $userid, $eventid);
        header("Location: $base/events");
        exit;
    }

    if(isset($_POST["price"], $_POST["eventid"])){
        $mode_paiement = $_POST['mode_paiement'] ?? 'carte_credit';

        if ($mode_paiement === 'carte_credit' && isset($_POST['remember_payment']) && $_POST['remember_payment'] === '1' && isset($_POST['numero_carte'], $_POST['expiration'])) {
            $_SESSION['saved_payment_info_event'] = [
                'numero_carte' => $_POST['numero_carte'],
                'expiration' => $_POST['expiration'],
            ];
        }

        createEventSubscription($db, $userid, $eventid, $_POST["price"], $mode_paiement);
        $xp = getEventSubscriptionXp($db, $eventid)[0]['xp_evenement'];
        addEventXp($db, $xp, $userid);
        header("Location: $base/events");
        exit;
    }
    elseif(isset($_POST["eventid"])){
            $event = getEventSubscriptionEvent($db, $eventid);
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
                $user_reduction = getEventSubscriptionReduction($db, $userid);
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