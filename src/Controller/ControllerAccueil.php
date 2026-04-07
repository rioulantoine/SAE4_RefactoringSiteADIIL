<?php

$db = new DB();
$isLoggedIn = isset($_SESSION['userid']);

$podium = $db->select(
    "SELECT prenom_membre, xp_membre, pp_membre FROM MEMBRE ORDER BY xp_membre DESC LIMIT 3;"
);

foreach ($podium as &$member) {
    $xpLength = strlen((string) $member['xp_membre']);
    $member['xp_size_class'] = 'xp-size-default';

    if ($xpLength >= 8) {
        $member['xp_size_class'] = 'xp-size-xl';
    } elseif ($xpLength >= 6) {
        $member['xp_size_class'] = 'xp-size-lg';
    }
}
unset($member);

require_once __DIR__ . '/../View/accueil.php';
