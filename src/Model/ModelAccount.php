<?php

require_once __DIR__ . '/database.php';

function getAccountInfo($db, $userId)
{
	return $db->select(
		"SELECT pp_membre, xp_membre, prenom_membre, nom_membre, email_membre, tp_membre, discord_token_membre, nom_grade, image_grade FROM MEMBRE LEFT JOIN ADHESION ON MEMBRE.id_membre = ADHESION.id_membre LEFT JOIN GRADE ON ADHESION.id_grade = GRADE.id_grade WHERE MEMBRE.id_membre = ?;",
		"i",
		[$userId]
	);
}

function getCurrentUserData($db, $userId)
{
	return $db->select(
		"SELECT prenom_membre, nom_membre, email_membre, tp_membre FROM MEMBRE WHERE id_membre = ?",
		"i",
		[$userId]
	);
}

function getAccountPassword($db, $userId)
{
	return $db->select(
		"SELECT password_membre FROM MEMBRE WHERE id_membre = ?",
		"i",
		[$userId]
	);
}

function getExistingEmail($db, $mail, $userId)
{
	return $db->select(
		"SELECT id_membre FROM MEMBRE WHERE email_membre = ? AND id_membre != ?",
		"si",
		[$mail, $userId]
	);
}

function updateAccountPicture($db, $fileName, $userId)
{
	$db->query(
		"UPDATE MEMBRE SET pp_membre = ? WHERE id_membre = ?",
		"si",
		[$fileName, $userId]
	);
}

function updateAccountInfo($db, $name, $lastName, $mail, $tp, $userId)
{
	$db->query(
		"UPDATE MEMBRE SET prenom_membre = ?, nom_membre = ?, email_membre = ?, tp_membre = ? WHERE id_membre = ?",
		"ssssi",
		[$name, $lastName, $mail, $tp, $userId]
	);
}

function updateAccountPassword($db, $hashedPassword, $userId)
{
	$db->query(
		"UPDATE MEMBRE SET password_membre = ? WHERE id_membre = ?",
		"si",
		[$hashedPassword, $userId]
	);
}

function getAccountHistory($db, $userId, $viewAll)
{
	$sql = "SELECT type_transaction, element, quantite, montant, mode_paiement, date_transaction, 
		CASE WHEN recupere = 1 THEN 'Récupéré' ELSE 'Non récupéré' END AS statut 
		FROM HISTORIQUE WHERE nom_utilisateur = (SELECT nom_membre FROM MEMBRE WHERE id_membre = ?) ORDER BY date_transaction DESC";

	if (!$viewAll) {
		$sql .= " LIMIT 6";
	}

	return $db->select(
		$sql,
		"i",
		[$userId]
	);
 
}
