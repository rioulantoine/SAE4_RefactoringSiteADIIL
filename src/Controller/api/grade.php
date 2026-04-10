<?php
use model\File;
use model\Grade;

require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Service/tools.php';
require_once __DIR__ . '/../../Service/filter.php';
require_once __DIR__ . '/../../Model/api/Grade.php';
require_once __DIR__ . '/../../Model/api/File.php';

ob_start();
ini_set('display_errors', 0);
header('Content-Type: application/json');

set_error_handler(function($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

try {
    tools::checkPermission('p_grade');

    $methode = $_SERVER['REQUEST_METHOD'];

    switch ($methode) {
        case 'GET':
            get_grades();
            break;
        case 'POST':
            if (isset($_GET['action']) && $_GET['action'] === 'update_image') {
                update_image();
            } else {
                create_grade();
            }
            break;
        case 'PUT':
            if (tools::methodAccepted('application/json')) {
                update_grade();
            }
            break;
        case 'DELETE':
            delete_grade();
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

function get_grades() : void
{
    if (isset($_GET['id']))
    {
        $id = filter::int($_GET['id']);
        $grades = Grade::getInstance($id);

        if ($grades === null) {
            http_response_code(404);
            @ob_clean();
            echo json_encode(['error' => 'Grade not found']);
            exit;
        }
        
    } else {
        $grades = Grade::bulkFetch();
    }

    http_response_code(200);
    @ob_clean();
    echo json_encode($grades);
    exit;
}

function create_grade() : void
{
    $grade = Grade::create("Nouveau grade", "Ceci est un nouveau grade", 10.99, null, 0);

    http_response_code(201);
    @ob_clean();
    echo json_encode($grade->jsonSerialize());
    exit;
}

function update_grade() : void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        @ob_clean();
        echo json_encode(['error' => 'Please provide an id']);
        exit;
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['name'], $data['description'], $data['price'], $data['reduction'])) {
        http_response_code(400);
        @ob_clean();
        echo json_encode(['error' => 'Incomplete data']);
        exit;
    }
    
    $id = filter::int($_GET['id']);
    $name = filter::string($data['name'], maxLenght: 100);
    $description = filter::string($data['description'], maxLenght: 500);
    $price = filter::float($data['price']);
    $reduction = filter::int($data['reduction']);

    $grade = Grade::getInstance($id);

    if ($grade === null) {
        http_response_code(404);
        @ob_clean();
        echo json_encode(['error' => 'Grade not found']);
        exit;
    }

    $grade->update($name, $description, $price, $reduction);

    http_response_code(200);
    @ob_clean();
    echo json_encode($grade->jsonSerialize());
    exit;
}

function update_image() : void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        @ob_clean();
        echo json_encode(['error' => 'Please provide an id']);
        exit;
    }

    $id = filter::int($_GET['id']);
    $grade = Grade::getInstance($id);

    if ($grade === null) {
        http_response_code(404);
        @ob_clean();
        echo json_encode(['error' => 'Grade not found']);
        exit;
    }

    $image = File::saveImage();

    if (!$image) {
        http_response_code(400);
        @ob_clean();
        echo json_encode(['error' => 'Image could not be processed']);
        exit;
    }

    $oldImage = $grade->jsonSerialize()['image_grade'];
    if ($oldImage !== 'default.png' && $oldImage !== 'N/A' && $oldImage !== 'grade.webP' && !str_starts_with($oldImage, 'http')) {
        $deleteFile = File::getFile($oldImage);
        $deleteFile?->deleteFile();
    }

    $grade->updateImage($image);

    http_response_code(200);
    @ob_clean();
    echo json_encode($grade->jsonSerialize());
    exit;
}

function delete_grade() : void
{
    if (!isset($_GET['id'])) {
        http_response_code(400);
        @ob_clean();
        echo json_encode(['error' => 'Please provide an id']);
        exit;
    }

    $id = filter::int($_GET['id']);
    $grade = Grade::getInstance($id);

    if ($grade === null) {
        http_response_code(404);
        @ob_clean();
        echo json_encode(['error' => 'Grade not found']);
        exit;
    }

    @$grade->delete();

    http_response_code(200);
    @ob_clean();
    echo json_encode(['message' => 'Grade deleted']);
    exit;
}