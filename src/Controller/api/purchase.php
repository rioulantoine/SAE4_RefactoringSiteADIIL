<?php

// Chemins corrigés avec __DIR__
require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Service/tools.php';

// TODO: Remove this line in production
ini_set('display_errors', 1);

header('Content-Type: application/json');

tools::checkPermission('p_achat');

$DB = new DB();

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':                      # READ
        get_purchase();
        break;
    default:
        # 405 Method Not Allowed
        http_response_code(405);
        break;
}

function get_purchase() : void {
    $db = new DB();
    $data = $db->select("SELECT *, nom_utilisateur AS nom_membre FROM HISTORIQUE");
    echo json_encode(array_reverse($data));
}