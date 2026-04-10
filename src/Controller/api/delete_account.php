<?php
session_start();

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseUrl = preg_replace('#src/Controller/api/delete_account\.php$#', '', $scriptName);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    http_response_code(307);
    header('Location: ' . $baseUrl . 'delete_account');
    exit;
}

header('Location: ' . $baseUrl . 'delete_account');
exit;