<?php

require_once __DIR__ . '/database.php';

function getShopProducts($db, $searchTerm, $filters, $orderBy)
{
    //Construction de la requête SQL
    $query = "SELECT * FROM ARTICLE";
    $whereClauses = ["deleted = false"];
    $params = [];

    // Ajout de la recherche par nom
    if (!empty($searchTerm)) {
        $whereClauses[] = "nom_article LIKE ?";
        $params[] = '%' . $searchTerm . '%';
    }

    // Ajout des filtres par catégorie
    if (!empty($filters)) {
        $placeholders = implode(', ', array_fill(0, count($filters), '?'));
        $whereClauses[] = "categorie_article IN ($placeholders)";
        $params = array_merge($params, $filters);
    }

    // Ajout des clauses WHERE
    if (!empty($whereClauses)) {
        $query .= " WHERE " . implode(' AND ', $whereClauses);
    }

    // Ajout du tri
    if ($orderBy === 'price_asc') {
        $query .= ' ORDER BY prix_article ASC';
    } elseif ($orderBy === 'price_desc') {
        $query .= ' ORDER BY prix_article DESC';
    } elseif ($orderBy === 'name_asc') {
        $query .= ' ORDER BY nom_article ASC';
    } elseif ($orderBy === 'name_desc') {
        $query .= ' ORDER BY nom_article DESC';
    }

    // Exécution de la requête
    $types = str_repeat('s', count($params));
    if (empty($params)) {
        return $db->select($query);
    }

    return $db->select($query, $types, $params);
}
