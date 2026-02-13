<?php
// public/api/cart.php - minimal cart API for add/del/update actions
if (session_status() === PHP_SESSION_NONE) session_start();

require_once dirname(__DIR__, 2) . '/src/Model/cart_class.php';

$db = new DB();
$cart = new cart($db);

$action = $_REQUEST['action'] ?? '';

$response = ['error' => true, 'message' => 'Action invalide'];

switch ($action) {
    case 'add':
        if (empty($_REQUEST['id'])) {
            $response['message'] = 'Identifiant manquant';
            break;
        }
        $id = intval($_REQUEST['id']);
        $product = $db->select(
            "SELECT id_article FROM ARTICLE WHERE id_article = ?",
            "i",
            [$id]
        );
        if (empty($product)) {
            $response['message'] = 'Produit introuvable';
            break;
        }
        $cart->add($id);
        $response = [
            'error' => false,
            'message' => 'Produit ajouté au panier',
            'count' => $cart->count(),
            'total' => $cart->total(),
        ];
        break;

    case 'del':
        if (empty($_REQUEST['id'])) {
            $response['message'] = 'Identifiant manquant';
            break;
        }
        $id = intval($_REQUEST['id']);
        $cart->del($id);
        $response = [
            'error' => false,
            'message' => 'Produit supprimé du panier',
            'count' => $cart->count(),
            'total' => $cart->total(),
        ];
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Méthode invalide';
            break;
        }
        if (!empty($_POST['cart']['quantity'])) {
            // cart_class::recalc() lit $_POST et met à jour $_SESSION['cart']
            $cart->recalc();
            $response = [
                'error' => false,
                'message' => 'Quantités mises à jour',
                'count' => $cart->count(),
                'total' => $cart->total(),
            ];
        } else {
            $response['message'] = 'Aucune quantité fournie';
        }
        break;
}

// Support simple redirect for non-AJAX flows
if (isset($_GET['redirect'])) {
    $loc = $_GET['redirect'];
    // basic safety: allow only same-origin redirects to the base URL
    if (strpos($loc, $base) === 0 || strpos($loc, '/') === 0) {
        header('Location: ' . $loc);
        exit;
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
