<?php
use model\File;
use model\Item;

require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Service/tools.php';
require_once __DIR__ . '/../../Service/filter.php';
require_once __DIR__ . '/../../Model/api/Item.php';
require_once __DIR__ . '/../../Model/api/File.php';

ob_start();

// 1. On interdit formellement à PHP d'afficher du HTML
ini_set('display_errors', 0); 
header('Content-Type: application/json');

// 2. On transforme TOUTES les alertes et erreurs PHP en exceptions
set_error_handler(function($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

// 3. On enveloppe tout ton routeur dans un bloc de sécurité
try {
    tools::checkPermission('p_boutique');

    $methode = $_SERVER['REQUEST_METHOD'];

    switch ($methode) {
        case 'GET':
            get_items();
            break;
        case 'POST':
            if (isset($_GET['action']) && $_GET['action'] === 'update_image') {
                update_image();
            } else {
                create_item();
            }
            break;
        case 'PUT':
            if (tools::methodAccepted('application/json')) {
                update_item();
            }
            break;
        case 'DELETE':
            delete_item();
            break;
        default:
            http_response_code(405);
            break;
    }
} catch (\Throwable $e) {
    // Si PHP plante n'importe où, on nettoie tout et on renvoie l'erreur en JSON !
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur PHP : ' . $e->getMessage() . ' (Fichier ' . basename($e->getFile()) . ', Ligne ' . $e->getLine() . ')'
    ]);
    exit;
}

function get_items() : void
{
    if (isset($_GET['id']))
    {
        $id = Filter::int($_GET['id']);
        $item = Item::getInstance($id);

        if (!$item) {
            http_response_code(404);
            ob_clean();
            echo json_encode(['error' => 'Item not found']);
            return;
        }

    } else {
        $item = Item::bulkFetch();
    }

    http_response_code(200);
    ob_clean();
    echo json_encode($item);
}

function create_item() : void
{
   $item = Item::create("Nouvel article", 1, 0, 1.0, 1.99, null);

   http_response_code(201);
   ob_clean();
   echo $item;
}

function update_item() : void
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($_GET['id'], $data['name'], $data['xp'], $data['stocks'], $data['reduction'], $data['price']))
    {
        http_response_code(400);
        ob_clean();
        echo json_encode(['error' => 'Missing parameters']);
        return;
    }

    $id = Filter::int($_GET['id']);
    $name = Filter::string($data['name'], maxLenght: 100);
    $xp = Filter::int($data['xp']);
    $stocks = Filter::int($data['stocks'], min: -100000);
    $reduction = (float)Filter::bool($data['reduction']); 
    $price = Filter::float($data['price']);

    $item = Item::getInstance($id);

    if (!$item)
    {
        http_response_code(404);
        ob_clean();
        echo json_encode(['error' => 'Item not found']);
        return;
    }

    $item->update($name, $xp, $stocks, $reduction, $price);

    ob_clean();
    echo $item;
}

function update_image() : void
{
    if (!isset($_GET['id']))
    {
        http_response_code(400);
        ob_clean();
        echo json_encode(['error' => 'Missing parameters']);
        return;
    }

    $item = Item::getInstance(Filter::int($_GET['id']));

    if (!$item)
    {
        http_response_code(404);
        ob_clean();
        echo json_encode(['error' => 'Item not found']);
        return;
    }

    $imageName = File::saveImage();

    if (!$imageName)
    {
        http_response_code(400);
        ob_clean();
        echo json_encode(['error' => 'Image could not be processed']);
        return;
    }

    @$item->getImage()?->deleteFile();

    $item->updateImage($imageName);

    ob_clean();
    echo $item;
}

function delete_item() : void
{
    if (!isset($_GET['id']))
    {
        http_response_code(400);
        ob_clean();
        echo json_encode(['error' => 'Missing parameters']);
        return;
    }

    $id = Filter::int($_GET['id']);
    $item = Item::getInstance($id);

    if (!$item)
    {
        http_response_code(404);
        ob_clean();
        echo json_encode(['error' => 'Item not found']);
        return;
    }

    @$item->delete();

    http_response_code(200);
    ob_clean();
    echo json_encode(['message' => 'Item deleted']);
}