<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Model/database.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$base = $_ENV['BASE_URL'] ?? getenv("BASE_URL");
session_start();
$page = $_GET['page'] ?? 'accueil';

/*$role = null;
if (isset($_SESSION['user']['roles'][0])) {
    $role = get_nom_role($_SESSION['user']['roles'][0]);
}*/

// Router les pages
require_once __DIR__ . '/src/View/Template/header.php';
switch ($page) {
    case 'accueil':
        require_once __DIR__ . '/src/View/accueil.php';
        break;

    case 'events':
        require_once __DIR__ . '/src/View/events.php';
        break;

    case 'news':
        require_once __DIR__ . '/src/View/news.php';
        break;

    case 'shop':
        require_once __DIR__ . '/src/View/shop.php';
        break;

    case 'grade':
        require_once __DIR__ . '/src/View/grade.php';
        break;

    case 'agenda':
        require_once __DIR__ . '/src/View/agenda.php';
        break;

    case 'about':
        require_once __DIR__ . '/src/View/about.php';
        break;
    
    case 'account':
        require_once __DIR__ . '/src/View/account.php';
        break;

    case 'cart':
        require_once __DIR__ . '/src/View/cart.php';
        break;

    case 'order':
        require_once __DIR__ . '/src/View/order.php';
        break;

    case 'admin':
        require_once __DIR__ . '/src/View/admin/admin.php';
        break;

    case 'login':
        require_once __DIR__ . '/src/View/login.php';
        break;

    case 'signin':
        require_once __DIR__ . '/src/View/signin.php';
        break;

    default:
        http_response_code(404);
        require_once __DIR__ . '/src/view/Template/erreur.php';
        break;
}
require_once __DIR__ . '/src/View/Template/footer.php';
?>
