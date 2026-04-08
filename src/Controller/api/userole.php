<?php
use model\Member;

require_once __DIR__ . '/../../Service/filter.php';
require_once __DIR__ . '/../../Model/api/Role.php';
require_once __DIR__ . '/../../Model/api/Member.php';
require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Service/tools.php';

ob_start();
ini_set('display_errors', 0);
header('Content-Type: application/json');

try {
    tools::checkPermission('p_role');
    tools::checkPermission('p_utilisateur');

    $methode = $_SERVER['REQUEST_METHOD'];

    switch ($methode) {
        case 'GET':
            get_userRoles();
            break;
        case 'PUT':
            if (tools::methodAccepted('application/json')) {
                setUserRoles();
            }
            break;
        default:
            http_response_code(405);
            break;
    }
} catch (\Throwable $e) {
    @ob_clean();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function get_userRoles() : void
{
    if (isset($_GET['id'])) {
        $user = Member::getInstance(filter::int($_GET['id']));
        if (!$user) {
            http_response_code(404);
            exit;
        }
        http_response_code(200);
        @ob_clean();
        echo json_encode($user->getRoles());
    }
    exit;
}

function setUserRoles() : void
{
    $user = Member::getInstance(filter::int($_GET['id']));
    $data = json_decode(file_get_contents('php://input'), true);

    if ($user && isset($data['roles'])) {
        $user->setRoles($data['roles']);
        http_response_code(200);
        @ob_clean();
        echo json_encode($user->getRoles());
    }
    exit;
}