<?php

// Chemins corrigés avec __DIR__
require_once __DIR__ . '/../../Model/database.php';
require_once __DIR__ . '/../../Service/tools.php';
require_once __DIR__ . '/../../Service/filter.php';

// TODO: Remove this line in production
ini_set('display_errors', 1);

header('Content-Type: application/json');

tools::checkPermission('p_achat');

$DB = new DB();

$methode = $_SERVER['REQUEST_METHOD'];

switch ($methode) {
    case 'GET':                      # READ
        get_purchase();
        break;
    case 'PATCH':
        if (tools::methodAccepted('application/json')) {
            update_purchase_status();
        }
        break;
    default:
        # 405 Method Not Allowed
        http_response_code(405);
        break;
}

function get_purchase() : void {
    $db = new DB();
    $data = $db->select(
        "SELECT
            'Commande' AS type_transaction,
            ARTICLE.nom_article AS element,
            COMMANDE.qte_commande AS quantite,
            MEMBRE.nom_membre AS nom_membre,
            MEMBRE.prenom_membre AS prenom_membre,
            COMMANDE.statut_commande AS recupere,
            CASE WHEN COMMANDE.statut_commande = 1 THEN 'Récupéré' ELSE 'Non récupéré' END AS statut,
            COMMANDE.date_commande AS date_transaction,
            COMMANDE.paiement_commande AS mode_paiement,
            COMMANDE.prix_commande AS montant,
            COMMANDE.id_commande AS id_commande
        FROM COMMANDE
        INNER JOIN ARTICLE ON ARTICLE.id_article = COMMANDE.id_article
        INNER JOIN MEMBRE ON MEMBRE.id_membre = COMMANDE.id_membre
        UNION ALL
        SELECT
            'Inscription' AS type_transaction,
            EVENEMENT.nom_evenement AS element,
            1 AS quantite,
            MEMBRE.nom_membre AS nom_membre,
            MEMBRE.prenom_membre AS prenom_membre,
            1 AS recupere,
            'Récupéré' AS statut,
            INSCRIPTION.date_inscription AS date_transaction,
            INSCRIPTION.paiement_inscription AS mode_paiement,
            INSCRIPTION.prix_inscription AS montant,
            NULL AS id_commande
        FROM INSCRIPTION
        INNER JOIN EVENEMENT ON EVENEMENT.id_evenement = INSCRIPTION.id_evenement
        INNER JOIN MEMBRE ON MEMBRE.id_membre = INSCRIPTION.id_membre
        UNION ALL
        SELECT
            'Adhesion' AS type_transaction,
            GRADE.nom_grade AS element,
            1 AS quantite,
            MEMBRE.nom_membre AS nom_membre,
            MEMBRE.prenom_membre AS prenom_membre,
            1 AS recupere,
            'Récupéré' AS statut,
            ADHESION.date_adhesion AS date_transaction,
            ADHESION.paiement_adhesion AS mode_paiement,
            ADHESION.prix_adhesion AS montant,
            NULL AS id_commande
        FROM ADHESION
        INNER JOIN GRADE ON GRADE.id_grade = ADHESION.id_grade
        INNER JOIN MEMBRE ON MEMBRE.id_membre = ADHESION.id_membre"
    );
    echo json_encode(array_reverse($data));
}

function update_purchase_status() : void {
    $id = filter::int($_GET['id']);
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data) || !array_key_exists('recupere', $data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Paramètre recupere requis']);
        return;
    }

    $recupere = filter::bool($data['recupere']) ? 1 : 0;
    $db = new DB();
    $existing = $db->select('SELECT id_commande FROM COMMANDE WHERE id_commande = ?', 'i', [$id]);

    if (empty($existing)) {
        http_response_code(404);
        echo json_encode(['error' => 'Commande introuvable']);
        return;
    }

    $db->query('UPDATE COMMANDE SET statut_commande = ? WHERE id_commande = ?', 'ii', [$recupere, $id]);

    $updated = $db->select(
        "SELECT
            COMMANDE.id_commande AS id_commande,
            COMMANDE.statut_commande AS recupere,
            CASE WHEN COMMANDE.statut_commande = 1 THEN 'Récupéré' ELSE 'Non récupéré' END AS statut
        FROM COMMANDE
        WHERE id_commande = ?",
        'i',
        [$id]
    );

    if (empty($updated)) {
        http_response_code(404);
        echo json_encode(['error' => 'Impossible de récupérer la commande modifiée']);
        return;
    }

    echo json_encode($updated[0]);
}
