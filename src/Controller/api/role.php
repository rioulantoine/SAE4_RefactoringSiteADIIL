<?php
use model\Role;

require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Service/tools.php';
require_once __DIR__ . '/../../Service/filter.php';
require_once __DIR__ . '/../../Model/api/Role.php';

ob_start();
ini_set('display_errors', 0); 
header('Content-Type: application/json');

set_error_handler(function($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

try {
    tools::checkPermission('p_role');
    $methode = $_SERVER['REQUEST_METHOD'];

    switch ($methode) {
        case 'GET':
            get_role();
            break;
        case 'POST':
            create_role();
            break;
        case 'PUT':
            if (tools::methodAccepted('application/json')) {
                update_role();
            }
            break;
        case 'DELETE':
            delete_role();
            break;
        default:
            http_response_code(405);
            break;
    }
} catch (\Throwable $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Erreur PHP : ' . $e->getMessage()]);
    exit;
}

function get_role() : void
{
    if (isset($_GET['id'])) {
        $role = Role::getInstance(filter::int($_GET['id']));
        if (!$role) {
            http_response_code(404);
            echo json_encode(["message" => "Not found"]);
            exit;
        }
        $data = $role->jsonSerialize();
    } else {
        $data = Role::bulkFetch();
    }
    http_response_code(200);
    ob_clean();
    echo json_encode($data);
    exit;
}

function create_role(): void
{
    $role = Role::create("Nouveau role", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    http_response_code(201);
    ob_clean();
    echo json_encode($role->jsonSerialize());
    exit;
}

function update_role() : void
{
    $data = json_decode(file_get_contents('php://input'), true);
    $role = Role::getInstance(filter::int($_GET['id']));

    if ($role && isset($data['name'], $data['permissions'])) {
        $role->update(
            filter::string($data['name']),
            filter::bool($data['permissions']['p_log'] ?? false),
            filter::bool($data['permissions']['p_boutique'] ?? false),
            filter::bool($data['permissions']['p_reunion'] ?? false),
            filter::bool($data['permissions']['p_utilisateur'] ?? false),
            filter::bool($data['permissions']['p_grade'] ?? false),
            filter::bool($data['permissions']['p_role'] ?? false),
            filter::bool($data['permissions']['p_actualite'] ?? false),
            filter::bool($data['permissions']['p_evenement'] ?? false),
            filter::bool($data['permissions']['p_comptabilite'] ?? false),
            filter::bool($data['permissions']['p_achat'] ?? false),
            filter::bool($data['permissions']['p_moderation'] ?? false)
        );
        http_response_code(200);
        ob_clean();
        echo json_encode($role->jsonSerialize());
    }
    exit;
}

function delete_role() : void
{
    $role = Role::getInstance(filter::int($_GET['id']));
    if ($role) $role->delete();
    http_response_code(200);
    ob_clean();
    echo json_encode(['message' => 'Deleted']);
    exit;
}