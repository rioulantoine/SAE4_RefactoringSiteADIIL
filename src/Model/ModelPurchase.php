<?php

require_once __DIR__ . '/database.php';

function getOrderPurchaseHistory($db)
{
    return $db->select(
        "SELECT 'Commande' type_transaction, a.nom_article element, c.qte_commande quantite,
                m.nom_membre, m.prenom_membre, c.date_commande date_transaction,
                c.paiement_commande mode_paiement, c.prix_commande montant,
                c.id_commande, IF(c.statut_commande = 0, 'En attente', 'Récupéré') statut
         FROM COMMANDE c
         INNER JOIN ARTICLE a ON a.id_article = c.id_article
         INNER JOIN MEMBRE m ON m.id_membre = c.id_membre"
    );
}

function getEventPurchaseHistory($db)
{
    return $db->select(
        "SELECT 'Inscription' type_transaction, e.nom_evenement element, 1 quantite,
                m.nom_membre, m.prenom_membre, i.date_inscription date_transaction,
                i.paiement_inscription mode_paiement, i.prix_inscription montant,
                NULL id_commande, 'Récupéré' statut
         FROM INSCRIPTION i
         INNER JOIN EVENEMENT e ON e.id_evenement = i.id_evenement
         INNER JOIN MEMBRE m ON m.id_membre = i.id_membre"
    );
}

function getGradePurchaseHistory($db)
{
    return $db->select(
        "SELECT 'Adhesion' type_transaction, g.nom_grade element, 1 quantite,
                m.nom_membre, m.prenom_membre, ad.date_adhesion date_transaction,
                ad.paiement_adhesion mode_paiement, ad.prix_adhesion montant,
                NULL id_commande, 'Récupéré' statut
         FROM ADHESION ad
         INNER JOIN GRADE g ON g.id_grade = ad.id_grade
         INNER JOIN MEMBRE m ON m.id_membre = ad.id_membre"
    );
}

function getPurchaseHistory($db)
{
    $purchases = array_merge(
        getOrderPurchaseHistory($db),
        getEventPurchaseHistory($db),
        getGradePurchaseHistory($db)
    );

    usort($purchases, function ($a, $b) {
        return strtotime($b['date_transaction']) <=> strtotime($a['date_transaction']);
    });

    return $purchases;
}

function validatePurchaseById($db, $orderId)
{
    $db->query(
        "UPDATE COMMANDE SET statut_commande = 1 WHERE id_commande = ?",
        'i',
        [$orderId]
    );
}