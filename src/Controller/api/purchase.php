<?php

require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Model/ModelPurchase.php';
require_once __DIR__ . '/../../Service/tools.php';

ini_set('display_errors', 1);
header('Content-Type: application/json');

tools::checkPermission('p_achat');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        get_purchase();
        break;
    case 'PATCH':
        validate_purchase();
        break;
    default:
        http_response_code(405);
        break;
}

function get_purchase() : void {
    $db = new DB();
    $data = getPurchaseHistory($db);

    echo json_encode($data);
}

function validate_purchase() : void {
    $payload = json_decode(file_get_contents('php://input'), true);
    $orderId = isset($payload['id_commande']) ? intval($payload['id_commande']) : 0;

    if ($orderId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Bad Request', 'message' => 'id_commande invalide']);
        return;
    }

    $db = new DB();
    validatePurchaseById($db, $orderId);

    echo json_encode(['success' => true]);
}