<?php

// Importer les fichiers
require_once 'database.php';
require_once 'files_save.php';
require_once 'cart_class.php';
require_once '../Model/ModelAddCart.php';

// Connexion à la base de donnees
$db = new DB();

// Initialisation du panier
$cart = new cart($db);

$json = array('error' => true);

if(isset($_GET['id'])){
    if(!doesProductExistForCart($db, $_GET['id'])){
        $json['message'] = "Ce produit n'existe pas"; 
    } else {
        $cart->add($_GET['id']);
        $json['error'] = false;
        $json['total'] = $cart->total();
        $json['count'] = $cart->count();
        $json['message'] = "Le produit a bien été ajouté à votre panier";
    }
    $json['message'] ="Vous n'avez pas ajouté de produit à ajouter au panier";
}

echo json_encode($json);