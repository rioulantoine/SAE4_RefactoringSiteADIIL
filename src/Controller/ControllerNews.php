<?php

require_once __DIR__ . '/../Model/ModelNews.php';

$db = new DB();
$show = 5;

if (isset($_GET['show']) && is_numeric($_GET['show'])) {
    $show = (int) $_GET['show'];
}

$news = getNews($db, $show);
$joursFr = [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
$moisFr = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];
$eventsToDisplay = [];
$closestFound = false;

foreach ($news as $event) {
    $eventDateValue = substr($event['date_actualite'], 0, 10);
    $eventDateInfo = getdate(strtotime($eventDateValue));

    $eventsToDisplay[] = [
        'id_actualite' => $event['id_actualite'],
        'titre_actualite' => $event['titre_actualite'],
        'date_actualite' => $event['date_actualite'],
        'date_label' => ucwords($joursFr[$eventDateInfo['wday']] . ' ' . $eventDateInfo['mday'] . ' ' . $moisFr[$eventDateInfo['mon']] . ' ' . $eventDateInfo['year']),
        'isClosest' => !$closestFound,
    ];

    if (!$closestFound) {
        $closestFound = true;
    }
}

require_once __DIR__ . '/../View/news.php';
