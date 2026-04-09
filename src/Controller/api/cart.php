<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Model/ModelCart.php';
require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Service/filter.php';

$db = new DB();
$modelCart = new ModelCart($db);

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? '';
$response = ['error' => true, 'message' => 'Action invalide'];

switch ($action) {
    case 'add':
        if (empty($_REQUEST['id'])) {
            $response['message'] = 'Identifiant manquant';
            break;
        }

        $id = Filter::int($_REQUEST['id']);
        if (!$modelCart->doesProductExist($id)) {
            $response['message'] = 'Produit introuvable';
            break;
        }

        $modelCart->addToCart($id);
        $response = [
            'error' => false,
            'message' => 'Produit ajouté au panier',
            'count' => $modelCart->getCartCount($_SESSION['cart']),
            'total' => $modelCart->getCartTotal($_SESSION['cart']),
        ];
        break;

    case 'del':
        if (empty($_REQUEST['id'])) {
            $response['message'] = 'Identifiant manquant';
            break;
        }

        $id = Filter::int($_REQUEST['id']);
        $modelCart->removeFromCart($id);
        $response = [
            'error' => false,
            'message' => 'Produit supprimé du panier',
            'count' => $modelCart->getCartCount($_SESSION['cart']),
            'total' => $modelCart->getCartTotal($_SESSION['cart']),
        ];
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Méthode invalide';
            break;
        }

        if (!empty($_POST['cart']['quantity']) && is_array($_POST['cart']['quantity'])) {
            $modelCart->recalcCart($_POST['cart']['quantity']);
            $response = [
                'error' => false,
                'message' => 'Quantités mises à jour',
                'count' => $modelCart->getCartCount($_SESSION['cart']),
                'total' => $modelCart->getCartTotal($_SESSION['cart']),
            ];
        } else {
            $response['message'] = 'Aucune quantité fournie';
        }
        break;
}

if (isset($_GET['redirect'])) {
    $_SESSION['message'] = $response['message'] ?? '';
    $_SESSION['message_type'] = (!empty($response['error'])) ? 'error' : 'success';

    $loc = $_GET['redirect'];
    if (strpos($loc, '/') === 0) {
        header('Location: ' . $loc);
        exit;
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
