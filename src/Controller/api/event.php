<?php
use model\Event;
use model\File;

require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Service/tools.php';
require_once __DIR__ . '/../../Service/filter.php';
require_once __DIR__ . '/../../Model/api/Event.php';
require_once __DIR__ . '/../../Model/api/File.php';

ob_start();
ini_set('display_errors', 0);

header('Content-Type: application/json');

set_error_handler(function($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

try {
    tools::checkPermission('p_evenement');

    $methode = $_SERVER['REQUEST_METHOD'];

    switch ($methode) {
        case 'GET':
            get_events();
            break;
        case 'POST':
            if (isset($_GET['action']) && $_GET['action'] === 'update_image') {
                update_image();
            } else {
                create_event();
            }
            break;
        case 'PUT':
            if (tools::methodAccepted('application/json')) {
                update_event();
            }
            break;
        case 'DELETE':
            delete_event();
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

function get_events() : void
{
    if (isset($_GET['id']))
    {
        $id = filter::int($_GET['id']);
        $event = Event::getInstance($id);

        if ($event == null) {
            http_response_code(404);
            @ob_clean();
            echo json_encode(['error' => 'Event not found']);
            exit;
        }
        $data = $event->jsonSerialize();
    }
    else
    {
        $data = Event::bulkFetch();
    }

    http_response_code(200);
    @ob_clean();
    echo json_encode($data);
    exit;
}

function create_event() : void
{
    $event = Event::create("Nouvel événement", 10, 50, 0.0, true, "Lieu", date("Y-m-d H:i:s"));

    http_response_code(201);
    @ob_clean();
    echo json_encode($event->jsonSerialize());
    exit;
}

function update_event() : void
{
    $data = json_decode(file_get_contents('php://input'), true);
    
    if(!isset($_GET['id'])) {
        http_response_code(400);
        @ob_clean();
        echo json_encode(['error' => 'ID missing']);
        exit;
    }

    $event = Event::getInstance($_GET['id']);

    if (!$event) {
        http_response_code(404);
        @ob_clean();
        echo json_encode(['error' => 'Event not found']);
        exit;
    }

    $event->update(
        filter::string($data['nom'], maxLenght:100), 
        filter::int($data['xp']), 
        filter::int($data['places'], -100000), 
        filter::float($data['prix']),
        filter::bool($data['reductions']), 
        filter::string($data['lieu'], maxLenght:50), 
        filter::date($data['date'])
    );

    http_response_code(200);
    @ob_clean();
    echo json_encode($event->jsonSerialize());
    exit;
}

function update_image() : void
{
    $image = File::saveImage();

    if (!$image) {
        http_response_code(400);
        @ob_clean();
        echo json_encode(['error' => 'Image could not be processed']);
        exit;
    }

    http_response_code(200);
    @ob_clean();
    echo json_encode(['message' => 'Fichier envoyé, mais non stocké en BDD car la colonne est absente.']);
    exit;
}

function delete_event() : void
{
    $id = filter::int($_GET['id']);
    $event = Event::getInstance($id);

    if (!$event) {
        http_response_code(404);
        @ob_clean();
        echo json_encode(['error' => 'Event not found']);
        exit;
    }

    $event->delete();
    http_response_code(200);
    @ob_clean();
    echo json_encode(['message' => 'Event deleted']);
    exit;
}