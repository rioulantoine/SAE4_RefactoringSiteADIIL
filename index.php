<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Model/database.php';
$dotenv = Dotenv\Dotenv::createMutable(__DIR__);
$dotenv->safeLoad();
$base = $_ENV['BASE_URL'] ?? '/';

session_start();
$page = $_GET['page'] ?? 'accueil';

$isAdmin = ($page === 'admin' || $page === 'admin_panel');

$isApi = str_starts_with($page, 'api_') || in_array($page, ['logout', 'item.php', 'cart.php', 'add_media', 'delete_media']);

if (!$isAdmin && !$isApi) {
    require_once __DIR__ . '/src/Controller/ControllerHeader.php';
}

switch ($page) {
    case 'accueil':
        require_once __DIR__ . '/src/Controller/ControllerAccueil.php';
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
        require_once __DIR__ . '/src/Controller/ControllerNews.php';
        break;

    case 'news_details':
        require_once __DIR__ . '/src/Controller/ControllerNewsDetails.php';
        break;

    case 'shop':
        require_once __DIR__ . '/src/Controller/ControllerShop.php';
        break;

    case 'grade_subscription':
        require_once __DIR__ . '/src/Controller/ControllerGradeSubscription.php';
        break;

    case 'agenda':
        require_once __DIR__ . '/src/View/agenda.php';
        break;

    case 'about':
        require_once __DIR__ . '/src/View/about.php';
        break;
    
    case 'faq':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/faq.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;
    
    case 'account':
        require_once __DIR__ . '/src/Controller/ControllerAccount.php';
        break;

    case 'order':
        require_once __DIR__ . '/src/Controller/ControllerOrder.php';
        break;

    case 'grade':
        require_once __DIR__ . '/src/Controller/ControllerGrade.php';
        break;

    case 'mentions-legales':
        require_once __DIR__ . '/src/View/mentions_legales.php';
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

    //routes api du panel admin
    case 'api_news':
        require_once __DIR__ . '/src/Controller/api/news.php';
        break;
    case 'api_item':
        require_once __DIR__ . '/src/Controller/api/item.php';
        break;
    case 'api_users':
        require_once __DIR__ . '/src/Controller/api/users.php';
        break;
    case 'api_userole':
        require_once __DIR__ . '/src/Controller/api/userole.php';
        break;
    case 'api_roles':
        require_once __DIR__ . '/src/Controller/api/role.php';
        break;
    case 'api_grade':
        require_once __DIR__ . '/src/Controller/api/grade.php';
        break;
    case 'api_event':
        require_once __DIR__ . '/src/Controller/api/event.php';
        break;
    case 'api_accounting':
        require_once __DIR__ . '/src/Controller/api/accounting.php';
        break;
    case 'api_meeting':
        require_once __DIR__ . '/src/Controller/api/meeting.php';
        break;
    case 'api_purchase':
        require_once __DIR__ . '/src/Controller/api/purchase.php';
        break;
    case 'api_logs':
        require_once __DIR__ . '/src/Controller/api/logs.php';
        break;

    case 'login':
        require_once __DIR__ . '/src/Controller/ControllerLogin.php';
        break;

    case 'logout':
        require_once __DIR__ . '/src/Controller/api/logout.php';
        break;

    case 'signin':
        require_once __DIR__ . '/src/Controller/ControllerSignin.php';
        break;

    case 'item.php':
        require_once __DIR__ . '/src/Controller/api/item.php';
        break;

    case 'cart.php':
        require_once __DIR__ . '/src/Controller/api/cart.php';
        break;

    case 'cart':
        require_once __DIR__ . '/src/Controller/ControllerCart.php';
        break;

    case 'add_media':
        require_once __DIR__ . '/src/Controller/api/add_media.php';
        break;

    case 'my_gallery':
        require_once __DIR__ . '/src/Controller/ControllerMyGallery.php';
        break;
        
    case 'delete_media':
        require_once __DIR__ . '/src/Controller/api/delete_media.php';
        break;

    default:
        http_response_code(404);
        require_once __DIR__ . '/src/View/Template/erreur.php';
        break;
}

if (!$isAdmin && !$isApi) {
    require_once __DIR__ . '/src/View/Template/footer.php';
}
?>