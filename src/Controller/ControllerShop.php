<?php
// Importer les fichiers
require_once __DIR__ . '/../Service/files_save.php';
require_once __DIR__ . '/../Model/cart_class.php';  
require_once __DIR__ . '/../Model/ModelShop.php';

// Connexion à la base de donnees
$db = new DB();

// Initialisation du panier
$cart = new cart($db);
$cartCount = $cart->count();


// Gestion de la recherche, des filtres et tris

//Traitement du formulaire
$filters = [];
$orderBy = "name_asc";
$searchTerm = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset'])) {
        $filters = [];
        $orderBy = "name_asc";
        $searchTerm = "";
    } else {
        if (isset($_POST['category'])) {
            $filters = $_POST['category'];
        }
        if (isset($_POST['sort'])) {
            $orderBy = $_POST['sort'];
        }
        if (!empty($_POST['search'])) {
            $searchTerm = $_POST['search'];
        }
    }
}

$products = getShopProducts($db, $searchTerm, $filters, $orderBy);

require_once __DIR__ . '/../View/shop.php';