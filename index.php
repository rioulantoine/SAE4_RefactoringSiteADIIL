<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Model/database.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$base = getenv("BASE_URL");

session_start();
$page = $_GET['page'] ?? 'accueil';


/*$role = null;
if (isset($_SESSION['user']['roles'][0])) {
    $role = get_nom_role($_SESSION['user']['roles'][0]);
}*/

// Router les pages
switch ($page) {
    case 'accueil':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/accueil.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;

    case 'events':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/events.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;

    case 'news':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/news.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;

    case 'shop':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/shop.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;

    case 'grade':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/grade.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;

    case 'agenda':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/agenda.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;

    case 'about':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/about.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;
    
    case 'account':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/account.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;

    case 'order':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/order.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;

    case 'admin':
        require_once __DIR__ . '/src/View/admin/admin.php';
        break;
        
    case 'admin_panel':
        $panel = $_GET['panel'] ?? 'chat';
        $validPanels = [
            'actualites', 'boutique', 'chat', 'comptabilite',
            'evenements', 'grades', 'history', 'logs',
            'reunions', 'roles', 'utilisateurs', 'unauthorized'
        ];

        if (in_array($panel, $validPanels)) {
            require_once __DIR__ . "/src/View/admin/panel/{$panel}.php";
        } else {
             require_once __DIR__ . "/src/View/admin/panel/unauthorized.php";
        }
        break;

    case 'about':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/about.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;

    case 'item.php':
        require_once __DIR__ . '/src/Controller/api/item.php';
        break;

    case 'cart.php':
        require_once __DIR__ . '/src/Controller/api/cart.php';
        break;

    case 'cart':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/cart.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;

    default:
        http_response_code(404);
        require_once __DIR__ . '/src/View/Template/erreur.php';
        break;
}
?>
