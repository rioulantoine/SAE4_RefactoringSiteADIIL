<?php


require_once __DIR__ . '/../../Service/tools.php';


if(!isset($_SESSION['userid'])){
    header('Location: ' . $base . 'login');
    exit();
}
if (!(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'])){
    header('Location: ' . $base . 'interdit');
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BDE - Administration</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <link rel="shortcut icon" href="<?php echo $base; ?>public/admin/ressources/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="<?php echo $base; ?>public/admin/styles/general.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/admin/styles/admin.css">

</head>
<body id="main">

    <!-- Navigation -->
    <nav>
        <a id="accueil" href="<?php echo $base; ?>accueil">
            ADIIL - Admin
        </a>
        <input type="checkbox" id="admin-menu-toggle" class="menu-toggle" aria-label="Ouvrir le menu admin">
        <label for="admin-menu-toggle" class="menu-burger" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </label>

        <div class="admin-menu">
            <ul>

            <li perm="chat">
                <img src="<?php echo $base; ?>public/admin/ressources/panels_icons/chat.svg" alt="Icone du chat">
                <p>Chat</p>
            </li>
            
            <?php
                if (tools::hasPermission('p_boutique')){
                    echo '
                        <li perm="boutique">
                            <img src="' . $base . 'public/admin/ressources/panels_icons/boutique.svg" alt="Icone de la boutique">
                            <p>Boutique</p>
                        </li>
                        ';
                }
            ?>
            
            <?php
                if (tools::hasPermission('p_utilisateur')){
                    echo '
            <li perm="utilisateurs">
                <img src="' . $base . 'public/admin/ressources/panels_icons/users.svg" alt="Icone des utilisateurs">
                <p>Utilisateurs</p>
            </li>
                        ';
                }
            ?>
            
            <?php
                if (tools::hasPermission('p_grade')){
                    echo '
            <li perm="grades">
                <img src="' . $base . 'public/admin/ressources/panels_icons/grades.svg" alt="Icone des grades">
                <p>Grades</p>
            </li>
                        ';
                }
            ?>
            
            <?php
                if (tools::hasPermission('p_evenement')){
                    echo '
            <li perm="evenements">
                <img src="' . $base . 'public/admin/ressources/panels_icons/events.svg" alt="Icone des événements">
                <p>Evenements</p>
            </li>
                        ';
                }
            ?>
            
            <?php
                if (tools::hasPermission('p_comptabilite')){
                    echo '
            <li perm="comptabilite">
                <img src="' . $base . 'public/admin/ressources/panels_icons/comptabilite.svg" alt="Icone de la comptabilite">
                <p>Comptabilite</p>
            </li>
                        ';
                }
            ?>
            
            <?php
                if (tools::hasPermission('p_reunion')){
                    echo '
            <li perm="reunions">
                <img src="' . $base . 'public/admin/ressources/panels_icons/reunions.svg" alt="Icone des réunions">
                <p>Réunions</p>
            </li>
                        ';
                }
            ?>

            <?php
                if (tools::hasPermission('p_role')){
                    echo '
            <li perm="roles">
                <img src="' . $base . 'public/admin/ressources/panels_icons/roles.svg" alt="Icone des roles">
                <p>Rôles</p>
            </li>
                        ';
                }
            ?>

            <?php
                if (tools::hasPermission('p_actualite')){
                    echo '
            <li perm="actualites">
                <img src="' . $base . 'public/admin/ressources/panels_icons/actualite.svg" alt="Icone des actualités">
                <p>Actualités</p>
            </li>
                        ';
                }
            ?>

            <?php
                if (tools::hasPermission('p_boutique')){
                    echo '
            <li perm="history">
                <img src="' . $base . 'public/admin/ressources/panels_icons/history.svg" alt="Icone de l\'historique d\'achat">
                <p>Historique d\'achats</p>
            </li>
                        ';
                }
            ?>

            <?php
                if (tools::hasPermission('p_log')){
                    echo '
            <li perm="logs">
                <img src="' . $base . 'public/admin/ressources/panels_icons/logs.svg" alt="Icone des logs du serveur">
                <p>Logs du serveur</p>
            </li>
                        ';
                }
            ?>

            </ul>
        </div>
    </nav>

    <!-- Permissions -->
    <main>
        <iframe frameborder="0" id="content" src="<?php echo $base; ?>index.php?page=admin_panel&panel=chat"></iframe>
    </main>

    <!-- SCRIPT -->
    <script>
        window.base = "<?php echo $base; ?>";
    </script>
    <script type="module" src="<?php echo $base; ?>public/admin/scripts/admin.js"></script>

</body>
</html>