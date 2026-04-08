<?php

require_once __DIR__ . '/../Model/ModelEventDetails.php';

$isLoggedIn = isset($_SESSION['userid']);
$db = new DB();

$show = 8;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . $base . 'accueil');
    exit;
}

$eventid = $_GET['id'];
$event = modelEventDetailsGetEventById($db, $eventid);

if (empty($event)) {
    header('Location: ' . $base . 'accueil');
    exit;
}

if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show'] > 0) {
    $show = $_GET['show'];
}

$current_date = new DateTime(date('Y-m-d'));
$event_date = new DateTime(substr($event['date_evenement'], 0, 10));

$isSubscribed = false;
$userMedias = [];

if ($isLoggedIn) {
    $userId = $_SESSION['userid'];
    $isSubscribed = modelEventDetailsIsUserSubscribed($db, $eventid, $userId);
    $userMedias = modelEventDetailsGetUserMedias($db, $eventid, $userId, 4);
}

$generalMedias = modelEventDetailsGetGeneralMedias($db, $eventid, $show);

require_once __DIR__ . '/../View/event_details.php';