<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Model/database.php';
$dotenv = Dotenv\Dotenv::createMutable(__DIR__);
$dotenv->safeLoad();
$base = $_ENV['BASE_URL'] ?? '/';

session_start();
$page = $_GET['page'] ?? 'accueil';


/*$role = null;
if (isset($_SESSION['user']['roles'][0])) {
    $role = get_nom_role($_SESSION['user']['roles'][0]);
}*/

// Router les pages
$isAdmin = ($page === 'admin' || $page === 'admin_panel');

if (!$isAdmin) {
    require_once __DIR__ . '/src/View/Template/header.php';
}

switch ($page) {
    case 'accueil':
        require_once __DIR__ . '/src/View/accueil.php';
        break;

    case 'events':
        require_once __DIR__ . '/src/Controller/ControllerEvents.php';
        break;

    case 'event_details':
        require_once __DIR__ . '/src/Controller/ControllerEventDetails.php';
        break;
    case 'event_subscription':
        require_once __DIR__ . '/src/Controller/ControllerEventSubscription.php';
        break;

    case 'news':
        require_once __DIR__ . '/src/View/news.php';
        break;

    case 'news_details':
        require_once __DIR__ . '/src/View/news_details.php';
        break;

    case 'shop':
        require_once __DIR__ . '/src/View/shop.php';
        break;

    case 'grade_subscription':
        require_once __DIR__ . '/src/View/grade_subscription.php';
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

    case 'order':
        require_once __DIR__ . '/src/View/order.php';
        break;

    case 'grade':
        require_once __DIR__ . '/src/View/grade.php';
        break;

    case 'grade_subscription':
        require_once __DIR__ . '/src/View/grade_subscription.php';
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
        require_once __DIR__ . '/src/View/about.php';
        break;

    case 'login':
        require_once __DIR__ . '/src/View/login.php';
        break;

    case 'logout':
        require_once __DIR__ . '/src/Controller/api/logout.php';
        break;

    case 'signin':
        require_once __DIR__ . '/src/View/signin.php';
        break;

    case 'item.php':
        require_once __DIR__ . '/src/Controller/api/item.php';
        break;

    case 'cart.php':
        require_once __DIR__ . '/src/Controller/api/cart.php';
        break;

    case 'cart':
        require_once __DIR__ . '/src/View/cart.php';
        break;

    default:
        http_response_code(404);
        require_once __DIR__ . '/src/View/Template/erreur.php';
        break;
}

if (!$isAdmin) {
    require_once __DIR__ . '/src/View/Template/footer.php';
}

?>
