<?php

require_once __DIR__ . '/../Model/api/Event.php';

use model\Event;

$db = new DB();
$isLoggedIn = isset($_SESSION['userid']);

$show = isset($_GET['show']) && is_numeric($_GET['show']) ? (int) $_GET['show'] : 5;
$search = $_GET['search'] ?? '';
$available = isset($_GET['available']);

$currentDate = new DateTime(date('Y-m-d'));
$joursFr = [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
$moisFr = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];

$events = Event::fetchFiltered($search, $available, 1000);

usort($events, function ($a, $b) {
    return strtotime($b['date_evenement']) <=> strtotime($a['date_evenement']);
});

if (empty($search)) {
    $upcomingEvents = [];
    $passedEvents = [];

    foreach ($events as $event) {
        $eventDate = new DateTime(substr($event['date_evenement'], 0, 10));
        if ($eventDate < $currentDate) {
            $passedEvents[] = $event;
        } else {
            $upcomingEvents[] = $event;
        }
    }

    $events = array_merge($upcomingEvents, array_slice($passedEvents, 0, $show));
}

$closestUpcomingEventId = null;
$closestUpcomingTimestamp = null;
foreach ($events as $event) {
    $eventDate = new DateTime(substr($event['date_evenement'], 0, 10));
    if ($eventDate >= $currentDate) {
        $timestamp = strtotime($event['date_evenement']);
        if ($closestUpcomingTimestamp === null || $timestamp < $closestUpcomingTimestamp) {
            $closestUpcomingTimestamp = $timestamp;
            $closestUpcomingEventId = (int) $event['id_evenement'];
        }
    }
}

$events_ready = [];
foreach ($events as $event) {
    $eventId = (int) $event['id_evenement'];
    $eventDateRaw = substr($event['date_evenement'], 0, 10);
    $eventDateInfo = getdate(strtotime($eventDateRaw));
    $eventDateObj = new DateTime($eventDateRaw);
    $isPassed = $eventDateObj < $currentDate;

    if ($isPassed) {
        $datePinClass = 'passed';
        $datePinLabel = 'Passé';
        $otherClasses = 'passed';
    } elseif ($eventDateObj == $currentDate) {
        $datePinClass = 'today';
        $datePinLabel = "Aujourd'hui";
        $otherClasses = '';
    } else {
        $datePinClass = 'upcoming';
        $datePinLabel = 'À venir';
        $otherClasses = '';
    }

    $remaining = (int)$event['places_evenement'] - (int)($db->select(
        'SELECT COUNT(*) as count FROM INSCRIPTION WHERE id_evenement = ?',
        'i',
        [$eventId]
    )[0]['count']);

    if ($isPassed) {
        $subscriptionClass = 'event-full';
        $subscriptionLabel = 'Passé';
    } elseif ($remaining <= 0) {
        $subscriptionClass = 'event-full';
        $subscriptionLabel = 'Complet';
    } else {
        $subscriptionClass = 'event-not-subscribed hover_effect';
        $subscriptionLabel = "S'inscrire";
    }

    if ($isLoggedIn && !$isPassed) {
        $isSubscribed = !empty($db->select(
            'SELECT id_membre FROM INSCRIPTION WHERE id_membre = ? AND id_evenement = ?',
            'ii',
            [$_SESSION['userid'], $eventId]
        ));

        if ($isSubscribed) {
            $subscriptionClass = 'event-subscribed';
            $subscriptionLabel = 'Inscrit';
        }
    }

    $events_ready[] = [
        'id_evenement' => $eventId,
        'nom_evenement' => $event['nom_evenement'],
        'lieu_evenement' => $event['lieu_evenement'],
        'date_affichage' => ucwords($joursFr[$eventDateInfo['wday']] . ' ' . $eventDateInfo['mday'] . ' ' . $moisFr[$eventDateInfo['mon']] . ' ' . $eventDateInfo['year']),
        'date_pin_class' => $datePinClass,
        'date_pin_label' => $datePinLabel,
        'other_classes' => $otherClasses,
        'closest_event_id' => ($closestUpcomingEventId !== null && $eventId === $closestUpcomingEventId) ? 'closest-event' : '',
        'subscription_class' => $subscriptionClass,
        'subscription_label' => $subscriptionLabel,
    ];
}

require_once __DIR__ . '/../View/events.php';