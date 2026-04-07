<link rel="shortcut icon" href="/admin/ressources/favicon.png" type="image/x-icon">

<?php
    @session_start();
    $isUserLoggedIn = isset($_SESSION['userid']);
    $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ;
?>


<!-- HEADER -->
<header>
    <a id="accueil" href="<?php echo $base; ?>accueil">
        <img src="<?php echo $base; ?>public/assets/logo.png" alt="Logo de l'ADIIL">
    </a>
    <input type="checkbox" id="menu-toggle" class="menu-toggle" aria-label="Ouvrir le menu principal">
    <label for="menu-toggle" class="menu-burger" aria-hidden="true">
        <span></span>
        <span></span>
        <span></span>
    </label>
    <nav id="header-nav">
        <ul>
            <li>
                <a href="<?php echo $base; ?>events">Événements</a>
            </li>
            <li>
                <a href="<?php echo $base; ?>news">Actualités</a>
            </li>
            <li>
                <a href="<?php echo $base; ?>shop">Boutique</a>
            </li>
            <li>
                <a href="<?php echo $base; ?>grade">Grades</a>
            </li>
            
            <?php if ($isUserLoggedIn): ?>
                <li>
                    <a href="<?php echo $base; ?>agenda">Agenda</a>
                </li>
            <?php endif; ?>

            <li>
                <a href="<?php echo $base; ?>about">À propos</a>
            </li>

            <?php if ($isUserLoggedIn): ?>
                <li>
                    <a href="<?php echo $base; ?>account">Mon compte</a>
                </li>

                <?php if ($isAdmin): ?>
                  <li>
                      <a id="header_admin" href="<?php echo $base; ?>admin">Panel Admin</a>
                  </li>
                <?php endif; ?>

            <?php else: ?>
                <li>
                    <a href="<?php echo $base; ?>login">Se connecter</a>
                </li>
            <?php endif; ?>

      
        </ul>
    </nav>
</header>
<script src="<?php echo $base; ?>public/scripts/header_menu.js"></script>
