<?php
// includes (use safe absolute paths)
require_once __DIR__ . '/../Model/ModelOrder.php';
require_once __DIR__ . '/../Model/cart_class.php';
require_once __DIR__ . '/../Service/files_save.php';

// Connexion à la base de donnees
$db = new DB();

// Initialisation du panier
$cart = new cart($db);

$isLoggedIn = isset($_SESSION['userid']);
if (!$isLoggedIn) {
    header('Location: ' . $base . '/login');
    exit;
}

$userid = $_SESSION['userid'];

// Récupérer le panier
if (empty($_SESSION['cart'])) {
    header('Location: ' . $base . 'cart');
    exit;
}

// Calculer le total de la commande
$total = 0;
$cart = $_SESSION['cart'];
$productIds = array_keys($cart);
$products = getOrderProducts($db, $productIds);

$cart_items = [];
foreach ($products as $product) {
    if (
        $product['stock_article'] > 0 && $_SESSION['cart'][$product['id_article']] > $product['stock_article']
    ) {
        $cart[$product['id_article']] = $product['stock_article'];
    }

    $cart_items[$product['id_article']] = [
        'nom_article' => $product['nom_article'], // Ajout du nom de l'article
        'prix_article' => $product['prix_article'],
        'quantite' => $cart[$product['id_article']],
    ];
    $total += $product['prix_article'] * $cart[$product['id_article']];
}

$totalWithReduc = null;
$adherant = getUserAdhesionWithReduction($db, $userid);
if (!empty($adherant)) {
    $reductionGrade = floatval($adherant[0]['reduction_grade'] ?? 0);
    $user_reduction = 1 - ($reductionGrade / 100);
    $totalWithReduc = 0;

    foreach ($products as $product) {
        if (!empty($product['reduction_article'])) {
            $totalWithReduc += $product['prix_article'] * $_SESSION['cart'][$product['id_article']] * $user_reduction;
        } else {
            $totalWithReduc += $product['prix_article'] * $_SESSION['cart'][$product['id_article']];
        }
    }
}

$savedPaymentInfo = isset($_SESSION['saved_payment_info']) ? $_SESSION['saved_payment_info'] : [];
$savedCardNumber = isset($savedPaymentInfo['numero_carte']) ? $savedPaymentInfo['numero_carte'] : '';
$savedExpiration = isset($savedPaymentInfo['expiration']) ? $savedPaymentInfo['expiration'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mode_paiement']) && !empty($_POST['mode_paiement'])) {
        $mode_paiement = $_POST['mode_paiement'];

        if (
            $mode_paiement === 'carte_credit'
            && isset($_POST['remember_payment'])
            && $_POST['remember_payment'] === '1'
            && isset($_POST['numero_carte'], $_POST['expiration'])
        ) {
            $_SESSION['saved_payment_info'] = [
                'numero_carte' => $_POST['numero_carte'],
                'expiration' => $_POST['expiration'],
            ];
        }

        // Enregistrer la commande dans la base de données
        foreach ($cart_items as $product_id => $item) {
            buyArticle($db, $userid, $product_id, $item['quantite'], $mode_paiement);
        }
        $_SESSION['cart'] = [];

        $_SESSION['message'] = 'Commande réalisée avec succès !';
        $_SESSION['message_type'] = 'success';

        header('Location: ' . $base . 'cart');
        exit;
    } else {
    }
}

require_once __DIR__ . '/../View/order.php';
