<?php

require_once __DIR__ . '/../Model/ModelGrade.php';

$db = new DB();
$products = getGrades($db);

$currentUserGradeId = null;
$currentUserReduction = null;

if (!empty($_SESSION['userid'])) {
    $currentGrade = getUserGrade($db, $_SESSION['userid']);

    if (!empty($currentGrade)) {
        $currentUserGradeId = $currentGrade['id_grade'];
        $currentUserReduction = $currentGrade['reduction_grade'];
    }
}

require_once __DIR__ . '/../View/grade.php';
