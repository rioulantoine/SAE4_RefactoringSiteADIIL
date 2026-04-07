<?php

require_once __DIR__ . '/../Model/ModelGrade.php';

$db = new DB();
$products = getGrades($db);

$currentUserGradeId = null;
$currentUserReduction = null;

if (!empty($_SESSION['userid'])) {
    $currentGrade = getUserGrade($db, (int) $_SESSION['userid']);

    if (!empty($currentGrade)) {
        $currentUserGradeId = (int) $currentGrade['id_grade'];
        $currentUserReduction = (float) $currentGrade['reduction_grade'];
    }
}

require_once __DIR__ . '/../View/grade.php';
