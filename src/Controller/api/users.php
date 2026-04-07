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
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur PHP : ' . $e->getMessage() . ' (Ligne ' . $e->getLine() . ')'
    ]);
    exit;
}

function get_users() : void {
    if (isset($_GET['id'])) {
        $id = filter::int($_GET['id']);

        $data = Member::getInstance($id);

        if ($data) {
            $data = $data->toJsonWithRoles();
        } else {
            http_response_code(404);
            ob_clean();
            echo json_encode(["message" => "User not found"]);
            exit;
        }

    } else {
        $data = Member::bulkFetch();
    }

    http_response_code(200);
    ob_clean();
    echo json_encode($data);
    exit;
}

function create_user() : void
{
    $user = Member::create("Nom", "Prenom", "prenom.nom@univ-lemans.fr", null, "21a");

    http_response_code(201);
    ob_clean();
    echo json_encode($user->toJsonWithRoles());
    exit;
}

function update_user() : void
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name'], $data['firstname'], $data['email'], $data['tp'], $data['xp'], $_GET['id'])) {
        http_response_code(400);
        ob_clean();
        echo json_encode(["message" => "Missing parameters"]);
        exit;
    }

    $id = filter::int($_GET['id']);
    $name = filter::string($data['name'], maxLenght: 100);
    $surname =  filter::string($data['firstname'], maxLenght: 100);
    $email = filter::email($data['email'], maxLenght: 100);
    $tp = filter::string($data['tp'], maxLenght: 3);
    $xp = filter::int($data['xp']);

    $user = Member::getInstance($id);

    if ($user) {
        $user->update($name, $surname, $email, $tp, $xp);

        http_response_code(200);
        ob_clean();
        echo json_encode($user->toJsonWithRoles());
    } else {
        http_response_code(404);
        ob_clean();
        echo json_encode(["message" => "User not found"]);
    }
    exit;
}

function update_image(): void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        ob_clean();
        echo json_encode(["message" => "Missing parameters"]);
        exit;
    }

    $id = filter::int($_GET['id']);
    $user = Member::getInstance($id);

    if (!$user) {
        http_response_code(404);
        ob_clean();
        echo json_encode(["message" => "User not found"]);
        exit;
    }

    $newImage = File::saveImage();

    if (!$newImage) {
        http_response_code(415);
        ob_clean();
        echo json_encode(["message" => "Image could not be processed"]);
        exit;
    }

    $oldPp = $user->toJson()['pp_membre'];
    if ($oldPp !== 'default.png' && $oldPp !== 'N/A' && !str_starts_with($oldPp, 'http')) {
        $deleteFile = File::getFile($oldPp);
        $deleteFile?->deleteFile();
    }

    $user->updateProfilePic($newImage);

    http_response_code(200);
    ob_clean();
    echo json_encode($user->toJsonWithRoles());
    exit;
}

function delete_user() : void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        ob_clean();
        echo json_encode(["message" => "Missing parameters"]);
        exit;
    }

    $id = filter::int($_GET['id']);
    $user = Member::getInstance($id);

    if (!$user) {
        http_response_code(404);
        ob_clean();
        echo json_encode(["message" => "User not found"]);
        exit;
    }

    @$user->delete();

    http_response_code(200);
    ob_clean();
    echo json_encode(["message" => "User deleted"]);
    exit;
}