<?php

require_once __DIR__ . '/database.php';

class ModelCart
{
    private DB $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    public function getProductsByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $query = "SELECT * FROM ARTICLE WHERE id_article IN ($placeholders)";
        $types = str_repeat('i', count($ids));

        return $this->db->select($query, $types, $ids);
    }

    public function getMemberReductionPercent(int $userId): ?float
    {
        $memberReduction = $this->db->select(
            "SELECT reduction_grade
             FROM ADHESION
             INNER JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade
             WHERE ADHESION.id_membre = ? AND reduction_grade > 0
             LIMIT 1",
            "i",
            [$userId]
        );

        if (empty($memberReduction)) {
            return null;
        }

        return (float) ($memberReduction[0]['reduction_grade'] ?? 0);
    }

    public function doesProductExist(int $productId): bool
    {
        $result = $this->db->select(
            "SELECT id_article FROM ARTICLE WHERE id_article = ?",
            "i",
            [$productId]
        );

        return !empty($result);
    }

    public function getStocksByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $results = $this->db->select(
            "SELECT id_article, stock_article FROM ARTICLE WHERE id_article IN ($placeholders)",
            str_repeat('i', count($ids)),
            $ids
        );

        return array_column($results, 'stock_article', 'id_article');
    }

    public function getCartTotal(array $cart): float
    {
        $total = 0.0;
        if (empty($cart)) {
            return $total;
        }

        $products = $this->getProductsByIds(array_keys($cart));
        foreach ($products as $product) {
            $productId = $product['id_article'];
            $quantity = $cart[$productId] ?? 0;
            $total += $product['prix_article'] * $quantity;
        }

        return $total;
    }

    public function getCartCount(array $cart): int
    {
        return array_sum($cart);
    }

    public function addToCart(int $productId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]++;
        } else {
            $_SESSION['cart'][$productId] = 1;
        }
    }

    public function removeFromCart(int $productId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
    }

    public function recalcCart(array $quantities): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $productIds = array_keys($_SESSION['cart']);
        if (empty($productIds)) {
            return;
        }

        $stocks = $this->getStocksByIds($productIds);
        foreach ($_SESSION['cart'] as $productId => $currentQuantity) {
            if (!isset($quantities[$productId])) {
                continue;
            }

            $newQuantity = $quantities[$productId];
            if (!is_numeric($newQuantity) || intval($newQuantity) != $newQuantity || $newQuantity <= 0) {
                continue;
            }

            $newQuantity = intval($newQuantity);
            if (isset($stocks[$productId]) && $newQuantity > $stocks[$productId]) {
                $_SESSION['cart'][$productId] = $stocks[$productId];
            } else {
                $_SESSION['cart'][$productId] = $newQuantity;
            }
        }
    }
}
