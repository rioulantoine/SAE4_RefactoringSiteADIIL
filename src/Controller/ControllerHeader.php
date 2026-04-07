<?php
@session_start();
$isUserLoggedIn = isset($_SESSION['userid']);
$isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'];
$currentPage = $page ?? ($_GET['page'] ?? 'accueil');

function isActive($currentPage, array $pages)
{
    return in_array($currentPage, $pages, true) ? 'header-link-active' : '';
}

require_once __DIR__ . '/../View/Template/header.php';
