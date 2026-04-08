<?php
use model\File;
use model\Meeting;
use model\Member;

require_once __DIR__ . '/../../Service/filter.php';
require_once __DIR__ . '/../../Model/api/File.php';
require_once __DIR__ . '/../../Model/api/Meeting.php';
require_once __DIR__ . '/../../Model/api/Member.php';
require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Service/tools.php';

ob_start();
ini_set('display_errors', 0);
header('Content-Type: application/json');

set_error_handler(function($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

try {
    tools::checkPermission('p_reunion');

    $methode = $_SERVER['REQUEST_METHOD'];

    switch ($methode) {
        case 'GET':
            get_meetings();
            break;
        case 'POST':
            create_meeting();
            break;
        case 'DELETE':
            delete_meeting();
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

function get_meetings() : void {
    if (isset($_GET['id'])) {
        $id = filter::int($_GET['id']);
        $meeting = Meeting::getInstance($id);

        if ($meeting) {
            $data = $meeting->jsonSerialize();
        } else {
            http_response_code(404);
            @ob_clean();
            echo json_encode(["message" => "Meeting not found"]);
            exit;
        }
    } else {
        $data = Meeting::bulkFetch();
    }
    http_response_code(200);
    @ob_clean();
    echo json_encode($data);
    exit;
}

function create_meeting() : void
{
    if (isset($_POST['date'])) {
        $date = filter::date($_POST['date']);
        $user = Member::getInstance(filter::int($_SESSION['userid'] ?? 1));
        $file = File::saveFile();

        if ($file && $user) {
            $meeting = Meeting::create($date, $file, $user);
            http_response_code(201);
            @ob_clean();
            echo json_encode($meeting->jsonSerialize());
        } else {
            http_response_code(500);
            @ob_clean();
            echo json_encode(["message" => "Error while saving"]);
        }
    } else {
        http_response_code(400);
        @ob_clean();
        echo json_encode(["message" => "Missing parameters"]);
    }
    exit;
}

function delete_meeting() : void
{
    if (isset($_GET['id'])) {
        $id = filter::int($_GET['id']);
        $meeting = Meeting::getInstance($id);
        if ($meeting) {
            $meeting->delete();
            http_response_code(200);
            @ob_clean();
            echo json_encode(["message" => "Deleted"]);
        }
    }
    exit;
}