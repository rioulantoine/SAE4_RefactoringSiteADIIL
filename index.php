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

    case 'grades':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/grades.php';
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

    case 'admin':
        require_once __DIR__ . '/src/View/admin/admin.php';
        break;

    case 'about':
        require_once __DIR__ . '/src/View/Template/header.php';
        require_once __DIR__ . '/src/View/about.php';
        require_once __DIR__ . '/src/View/Template/footer.php';
        break;

    default:
        http_response_code(404);
        require_once __DIR__ . '/src/view/Template/erreur.php';
        break;
}
?>
