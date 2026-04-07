<?php

require_once __DIR__ . '/../Model/ModelCart.php';

$db = new DB();
$modelCart = new ModelCart($db);

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartItems = $_SESSION['cart'];
$productIds = array_keys($cartItems);
$products = $modelCart->getProductsByIds($productIds);

$cartCount = 0;
$cartTotal = 0.0;
foreach ($products as $product) {
    $productId = (int) $product['id_article'];
    $quantity = (int) ($cartItems[$productId] ?? 0);
    $cartCount += $quantity;
    $cartTotal += (float)$product['prix_article'] * $quantity;
}

$totalWithReduc = null;
if (!empty($_SESSION['userid']) && !empty($products)) {
    $reductionPercent = $modelCart->getMemberReductionPercent((int) $_SESSION['userid']);

    if ($reductionPercent !== null && $reductionPercent > 0) {
        $userReduction = 1 - ($reductionPercent / 100);
        $totalWithReduc = 0.0;

        foreach ($products as $product) {
            $productId = (int) $product['id_article'];
            $quantity = (int) ($cartItems[$productId] ?? 0);

            if (!empty($product['reduction_article'])) {
                $totalWithReduc += (float)$product['prix_article'] * $quantity * $userReduction;
            } else {
                $totalWithReduc += (float)$product['prix_article'] * $quantity;
            }
        }
    }
}

$flashMessage = $_SESSION['message'] ?? null;
$flashType = $_SESSION['message_type'] ?? null;
unset($_SESSION['message'], $_SESSION['message_type']);

require_once __DIR__ . '/../View/cart.php';
