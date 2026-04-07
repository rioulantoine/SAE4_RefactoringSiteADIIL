<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/cart_style.css">

    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/footer_style.css">

    <script>
        //Fonction pour valider la soumission du formulaire (form-quantity) par la touche "Entrée"
        function pressEnter (event) {
            var code=event.which || event.keyCode;
            if (code==13) { //Code de la touche "Entrée"
                document.getElementById("form-quantity").submit();
            }
        }
    </script>

</head>

<body class="body_margin">



<!--------------->
<!------HTML----->
<!--------------->

<div>
<H1>MON PANIER</H1>

    <!-- Affichage du message de succès ou d'erreur -->
    <div>
        <?php
            if (!empty($flashMessage)) {
                $messageStyle = ($flashType === "error") ? "error-message" : "success-message";
                echo '<div id="' . $messageStyle . '">' . htmlspecialchars($flashMessage) . '</div>';
            }
        ?>
    </div>

    <div>
        <a id="shop-button" href="<?php echo $base; ?>shop">    
            <img src="<?php echo $base; ?>public/assets/fleche_retour.png" alt="Fleche de retour">
            Retourner à la boutique
        </a>
    </div>
</div>

<?php if (!empty($cartItems)) : ?>
<div id='cart-container'>
    <form method="POST" action="<?php echo $base; ?>cart.php?action=update&redirect=<?php echo urlencode($base . 'cart'); ?>" id= "form-quantity">
    <table>
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Sous-total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) :?>
                <tr>
                    <td id='article-cell'>
                        <img src="<?php echo $base; ?>public/api/files/<?php echo $product['image_article']; ?>" alt="Image de l'article" />
                        <p><?= htmlspecialchars($product['nom_article']) ?></p>
                    </td>
                    <td><?= number_format(htmlspecialchars($product['prix_article']), 2, ',', ' ') ?> €</td>                
                    <td><input type='text' name="cart[quantity][<?=$product['id_article']?>]" value="<?= (int)($cartItems[$product['id_article']] ?? 0) ?>" onkeydown="pressEnter(event)"></td>
                    <td><?= number_format((float)$product['prix_article'] * (int)($cartItems[$product['id_article']] ?? 0), 2, ',', ' ') ?> €</td>  
                    <td>
                        <a href="<?php echo $base; ?>cart.php?action=del&id=<?= $product['id_article'] ?>&redirect=<?php echo urlencode($base . 'cart'); ?>" onclick="return confirm('Etes-vous sur de vouloir supprimer cet article du panier ?');">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Nombre d'articles &nbsp : </th>
                    <td><?= (int)$cartCount ?></td>
                </tr>
                <tr>
                    <th>Total &nbsp : </th>
                    <td><?= number_format((float)$cartTotal, 2, ',', ' ') ?> €</td>
                </tr>
                
                <?php if ($totalWithReduc !== null): ?>
                    <tr>
                        <th style="min-width: 400px">Total après réductions &nbsp : </th>
                        <td style="min-width: 50px"><?= number_format((float)$totalWithReduc, 2, ',', ' ') ?> €</td>
                    </tr>
                <?php endif; ?>
            <tfoot>
        </table>
    </form>
</div>
<div>
    <form class="subscription" action="<?php echo $base; ?>order" method="post">
        <?php
        if (!empty($cartItems)) {
            // Encodage du panier entier en JSON et transmission dans un seul champ caché
            echo '<input type="hidden" name="cart" value="' . htmlspecialchars(json_encode($cartItems, JSON_UNESCAPED_UNICODE)) . '">';
        }
        ?>
        <button type="submit" id='order-button'>
            Commander
        </button>
    </form>
</div>

<?php else : ?>
    <p id="empty-cart">Votre panier est vide</p>
<?php endif; ?>

</body>
</html>