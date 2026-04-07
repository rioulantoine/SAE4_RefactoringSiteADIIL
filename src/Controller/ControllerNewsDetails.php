<?php

require_once __DIR__ . '/../Model/ModelNewsDetails.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    header('Location: ' . $base . 'accueil');
    exit;
}

$newsId = (int) $_GET['id'];
$db = new DB();
$event = getNewsById($db, $newsId);

if (empty($event)) {
    header('Location: ' . $base . 'accueil');
    exit;
}

require_once __DIR__ . '/../View/news_details.php';
