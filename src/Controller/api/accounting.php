<?php
use model\Accounting;
use model\File;

require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Service/tools.php';
require_once __DIR__ . '/../../Service/filter.php';
require_once __DIR__ . '/../../Model/api/File.php';
require_once __DIR__ . '/../../Model/api/Accounting.php';

ob_start();
ini_set('display_errors', 0);
header('Content-Type: application/json');

set_error_handler(function($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

try {
    tools::checkPermission('p_comptabilite');

    $methode = $_SERVER['REQUEST_METHOD'];

    switch ($methode) {
        case 'GET':
            get_accounting();
            break;
        case 'POST':
            create_accounting();
            break;
        case 'DELETE':
            delete_accounting();
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

function get_accounting(): void
{
    if (isset($_GET['id'])) {
        $id = filter::int($_GET['id']);
        $data = Accounting::getInstance($id);
        if ($data == null) {
            http_response_code(404);
            @ob_clean();
            echo json_encode(["message" => "Not found"]);
            exit;
        }
        $result = $data->jsonSerialize();
    } else {
        $result = Accounting::bulkFetch();
    }

    http_response_code(200);
    @ob_clean();
    echo json_encode($result);
    exit;
}

function create_accounting(): void
{
    if (!isset($_POST['date'], $_POST['nom'])) {
        http_response_code(400);
        @ob_clean();
        echo json_encode(["message" => "Missing parameters"]);
        exit;
    }

    $file = File::saveFile();

    if ($file == null) {
        http_response_code(400);
        @ob_clean();
        echo json_encode(["message" => "File error"]);
        exit;
    }

    $date = filter::date($_POST['date']);
    $nom = filter::string($_POST['nom'], maxLenght: 100);
    $id_membre = filter::int($_SESSION['userid'] ?? 1);

    $compta = Accounting::create($date, $nom, $file->getFileName(), $id_membre);

    http_response_code(201);
    @ob_clean();
    echo json_encode($compta->jsonSerialize());
    exit;
}

function delete_accounting() : void
{
    $id = filter::int($_GET['id']);
    $compta = Accounting::getInstance($id);
    if ($compta) $compta->delete();
    
    http_response_code(200);
    @ob_clean();
    echo json_encode(["message" => "Deleted"]);
    exit;
}