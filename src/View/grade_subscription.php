<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adhérer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/grade_subscription_style.css">

    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/footer_style.css">
</head>

<body class="body_margin">




<!--------------->
<!--------------->
<!------HTML----->
<!--------------->


<h1>MON ADHESION</h1>

<div>
    <a id="cart-button" href="<?php echo $base; ?>grade">
        <img src="<?php echo $base; ?>public/assets/fleche_retour.png" alt="Fleche de retour">
        Retourner aux grades
    </a>
</div>

<div>
    <div>
        <table>
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Grade <?php echo htmlspecialchars($grade['nom_grade']); ?></td>
                    <td>1</td>
                    <td><?= number_format(htmlspecialchars($grade['prix_grade']), 2, ',', ' ') ?> €</td>
                    <td><?= number_format(htmlspecialchars($grade['prix_grade']), 2, ',', ' ') ?> €</td>
                </tr>
            </tbody>
        </table>

        <h3>Total &nbsp : &nbsp<?= number_format(htmlspecialchars($grade['prix_grade']), 2, ',', ' ') ?> €</h3>
    </div>

    <div>    
        <h3>Paiement</h3>

        <label for="mode_paiement">Mode de Paiement :</label>
        <select id="mode_paiement" name="mode_paiement" required>
            <option value="carte_credit">Carte de Crédit</option>
            <option value="paypal">PayPal</option>
        </select><br><br>
        <div id="carte_credit" class="mode_paiement_fields">
            <form method="POST" action="<?php echo $base; ?>grade_subscription?id=<?= $id_grade ?>">
                <input type="hidden" name="mode_paiement" value="carte_credit">

                <label for="numero_carte">Numéro de Carte :</label>
                <input type="text" id="numero_carte" name="numero_carte" placeholder="XXXX XXXX XXXX XXXX" value="<?php echo htmlspecialchars($savedCardNumber); ?>" required><br><br>

                <label for="expiration">Date d'Expiration :</label>
                <input type="text" id="expiration" name="expiration" placeholder="MM/AA" value="<?php echo htmlspecialchars($savedExpiration); ?>" required><br><br>

                <label for="cvv">CVV :</label>
                <input type="text" id="cvv" name="cvv" placeholder="XXX" required><br><br>

                <label for="remember_payment" class="remember-payment">
                    <input type="checkbox" id="remember_payment" name="remember_payment" value="1" <?php echo !empty($savedCardNumber) ? 'checked' : ''; ?>>
                    Enregistrer pour les prochaines commandes
                </label><br><br>

                <button type="submit" id="finalise-order-button">Valider l'adhésion</button>
            </form>
        </div>
        <div id="paypal" class="mode_paiement_fields" style="display: none;">
            <form method="POST" action="<?php echo $base; ?>grade_subscription?id=<?= $id_grade ?>">
                <input type="hidden" name="mode_paiement" value="paypal">

                <button type="button" id="paypal-button">Se connecter à PayPal</button><br><br>
                    
                <button type="submit" id="finalise-order-button">Valider l'adhésion'</button>
            </form>
        </div>
    </div>
</div>



<script>
    document.getElementById('mode_paiement').addEventListener('change', function() {
        var modePaiement = this.value;
        if (modePaiement === 'carte_credit') {
            document.getElementById('carte_credit').style.display = 'block';
            document.getElementById('paypal').style.display = 'none';
        } else if (modePaiement === 'paypal') {
            document.getElementById('carte_credit').style.display = 'none';
            document.getElementById('paypal').style.display = 'block';
        }
    });
</script>

</body>
</html>