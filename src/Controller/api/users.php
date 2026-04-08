<?php
use model\File;
use model\Member;

require_once __DIR__ . '/../../Model/api/Member.php';
require_once __DIR__ . '/../../Model/api/File.php';
require_once __DIR__ . '/../../Service/filter.php';
require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Service/tools.php';

ob_start();
ini_set('display_errors', 0);
header('Content-Type: application/json');

set_error_handler(function($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

try {
    tools::checkPermission('p_utilisateur');

    $methode = $_SERVER['REQUEST_METHOD'];

    switch ($methode) {
        case 'GET':
            get_users();
            break;
        case 'POST':
            if (isset($_GET['action']) && $_GET['action'] === 'update_image') {
                update_image();
            } else {
                create_user();
            }
            break;
        case 'PUT':
            if (tools::methodAccepted('application/json')) {
                update_user();
            }
            break;
        case 'DELETE':
            delete_user();
            break;
        default:
            http_response_code(405);
            break;
    }
} catch (\Throwable $e) {
    @ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Erreur PHP : ' . $e->getMessage()]);
    exit;
}

function get_users() : void {
    if (isset($_GET['id'])) {
        $id = filter::int($_GET['id']);
        $user = Member::getInstance($id);

        if ($user) {
            $data = $user->jsonSerialize();
        } else {
            http_response_code(404);
            @ob_clean();
            echo json_encode(["message" => "User not found"]);
            exit;
        }
    } else {
        $data = Member::bulkFetch();
    }

    http_response_code(200);
    @ob_clean();
    echo json_encode($data);
    exit;
}

function create_user() : void
{
    $user = Member::create("Nom", "Prenom", "prenom.nom@univ-lemans.fr", null, "21a");
    http_response_code(201);
    @ob_clean();
    echo json_encode($user->jsonSerialize());
    exit;
}

function update_user() : void
{
    $data = json_decode(file_get_contents('php://input'), true);
    $user = Member::getInstance(filter::int($_GET['id']));

    if ($user && isset($data['name'], $data['firstname'], $data['email'])) {
        $user->update(
            filter::string($data['name']), 
            filter::string($data['firstname']), 
            filter::email($data['email']), 
            filter::string($data['tp']), 
            filter::int($data['xp'])
        );

        http_response_code(200);
        @ob_clean();
        echo json_encode($user->jsonSerialize());
    } else {
        http_response_code(404);
        @ob_clean();
        echo json_encode(["message" => "Not found or missing params"]);
    }
    exit;
}

function update_image(): void
{
    $user = Member::getInstance(filter::int($_GET['id']));
    if (!$user) {
        http_response_code(404);
        exit;
    }

    $newImage = File::saveImage();
    if (!$newImage) {
        http_response_code(400);
        exit;
    }

    @$user->getProfilePic()?->deleteFile();
    $user->updateProfilePic($newImage);

    http_response_code(200);
    @ob_clean();
    echo json_encode($user->jsonSerialize());
    exit;
}

function delete_user() : void
{
    $user = Member::getInstance(filter::int($_GET['id']));
    if ($user) $user->delete();
    http_response_code(200);
    @ob_clean();
    echo json_encode(["message" => "Deleted"]);
    exit;
}