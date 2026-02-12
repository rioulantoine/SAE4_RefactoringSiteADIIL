<link rel="shortcut icon" href="/admin/ressources/favicon.png" type="image/x-icon">

<?php
    @session_start();
    $isUserLoggedIn = isset($_SESSION['userid']);
    $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ;
    
    # A MODIFIER POUR LE DEPLOIEMENT - LA LE BASE URL NE FONCTIIONNE PAS 
    $baseUrl = getenv('BASE_URL') ?: 'http://localhost/SAE4/SAE4_RefactoringSiteADIIL/';
    // normaliser le base URL en supprimant les éventuels slashs à la fin
    $base = rtrim($baseUrl, '/');
?>


<!-- HEADER -->
<header>
    <a id="accueil" href="<?php echo $base; ?>/index.php">
        <img src="<?php echo $base; ?>/public/assets/logo.png" alt="Logo de l'ADIIL">
    </a>
    <nav>
        <ul>
            <li>
                <a href="<?php echo $base; ?>/src/View/events.php">Événements</a>
            </li>
            <li>
                <a href="<?php echo $base; ?>/src/View/news.php">Actualités</a>
            </li>
            <li>
                <a href="<?php echo $base; ?>/src/View/shop.php">Boutique</a>
            </li>
            <li>
                <a href="<?php echo $base; ?>/src/View/grade.php">Grades</a>
            </li>
            
            <?php if ($isUserLoggedIn): ?>
                <li>
                    <a href="<?php echo $base; ?>/src/View/agenda.php">Agenda</a>
                </li>
            <?php endif; ?>

            <li>
                <a href="<?php echo $base; ?>/src/View/about.php">À propos</a>
            </li>

            <?php if ($isUserLoggedIn): ?>
                <li>
                    <a href="<?php echo $base; ?>/src/View/account.php">Mon compte</a>
                </li>

                <?php if ($isAdmin): ?>
                  <li>
                      <a id="header_admin" href="<?php echo $base; ?>/src/View/admin/admin.php">Panel Admin</a>
                  </li>
                <?php endif; ?>

            <?php else: ?>
                <li>
                    <a href="<?php echo $base; ?>/src/View/login.php">Se connecter</a>
                </li>
            <?php endif; ?>

      
        </ul>
    </nav>
</header>
