<?php
use model\File;
use model\News;

require_once __DIR__ . '/../../Service/filter.php';
require_once __DIR__ . '/../../Model/api/News.php';
require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Service/tools.php';
require_once __DIR__ . '/../../Model/api/File.php';

ob_start();
ini_set('display_errors', 0);
header('Content-Type: application/json');

set_error_handler(function($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

try {
    tools::checkPermission('p_actualite');
    $methode = $_SERVER['REQUEST_METHOD'];

    switch ($methode) {
        case 'GET':
            get_news();
            break;
        case 'POST':
            if (isset($_GET['action']) && $_GET['action'] === 'update_image') {
                update_image();
            } else {
                create_news();
            }
            break;
        case 'PUT':
            if (tools::methodAccepted('application/json')) {
                update_news();
            }
            break;
        case 'DELETE':
            delete_news();
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

function get_news() : void
{
    if (isset($_GET['id'])) {
        $news = News::getInstance(filter::int($_GET['id']));
        if (!$news) {
            http_response_code(404);
            echo json_encode(['error' => 'News not found']);
            exit;
        }
        $data = $news->jsonSerialize();
    } else {
        $data = News::bulkFetch();
    }
    http_response_code(200);
    @ob_clean();
    echo json_encode($data);
    exit;
}

function create_news() : void
{
    $id_membre = isset($_SESSION['userid']) ? filter::int($_SESSION['userid']) : 1;
    
    $titre = isset($_POST['titre']) ? filter::string($_POST['titre'], maxLenght: 100) : "Nouvelle actualité";
    $contenu = isset($_POST['contenu']) ? filter::string($_POST['contenu'], maxLenght: 1000) : "Description";
    
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = File::saveImage();
        if (!$image) {
            http_response_code(400);
            @ob_clean();
            echo json_encode(['error' => 'Erreur lors de l\'upload de l\'image']);
            exit;
        }
    }
    
    $news = News::create($titre, $contenu, date("Y-m-d H:i:s"), $id_membre, $image);
    http_response_code(201);
    @ob_clean();
    echo json_encode($news->jsonSerialize());
    exit;
}

function update_news() : void
{
    $news = News::getInstance(filter::int($_GET['id']));
    if (!$news) {
        http_response_code(404);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $id_membre = isset($_SESSION['userid']) ? filter::int($_SESSION['userid']) : 1;
    $news->update(
        filter::string($data['name'], maxLenght: 100),
        filter::string($data['description'], maxLenght: 1000),
        filter::string($data['date']),
        $id_membre
    );
    http_response_code(200);
    @ob_clean();
    echo json_encode($news->jsonSerialize());
    exit;
}

function update_image() : void
{
    $news = News::getInstance(filter::int($_GET['id']));
    if (!$news) {
        http_response_code(404);
        exit;
    }
    $image = File::saveImage();
    if (!$image) {
        http_response_code(400);
        exit;
    }
    @$news->getImage()?->deleteFile();
    $news->updateImage($image);
    http_response_code(200);
    @ob_clean();
    echo json_encode($news->jsonSerialize());
    exit;
}

function delete_news() : void
{
    $news = News::getInstance(filter::int($_GET['id']));
    if ($news) $news->delete();
    http_response_code(200);
    @ob_clean();
    echo json_encode(['message' => 'Deleted']);
    exit;
}