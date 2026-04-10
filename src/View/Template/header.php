<link rel="shortcut icon" href="<?php echo $base; ?>public/admin/ressources/favicon.png" type="image/x-icon">


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
                <a class="<?php echo isActive($currentPage, ['events', 'event_details', 'event_subscription']); ?>" href="<?php echo $base; ?>events">Événements</a>
            </li>
            <li>
                <a class="<?php echo isActive($currentPage, ['news', 'news_details']); ?>" href="<?php echo $base; ?>news">Actualités</a>
            </li>
            <li>
                <a class="<?php echo isActive($currentPage, ['shop', 'cart', 'order']); ?>" href="<?php echo $base; ?>shop">Boutique</a>
            </li>
            <li>
                <a class="<?php echo isActive($currentPage, ['grade', 'grade_subscription']); ?>" href="<?php echo $base; ?>grade">Grades</a>
            </li>
            
            <?php if ($isUserLoggedIn): ?>
                <li>
                    <a class="<?php echo isActive($currentPage, ['agenda']); ?>" href="<?php echo $base; ?>agenda">Agenda</a>
                </li>
            <?php endif; ?>

            <li>
                <a class="<?php echo isActive($currentPage, ['about']); ?>" href="<?php echo $base; ?>about">À propos</a>
            </li>

            <?php if ($isUserLoggedIn): ?>
                <li>
                    <a class="<?php echo isActive($currentPage, ['account']); ?>" href="<?php echo $base; ?>account">Mon compte</a>
                </li>

                <?php if ($isAdmin): ?>
                  <li>
                      <a id="header_admin" class="<?php echo isActive($currentPage, ['admin', 'admin_panel']); ?>" href="<?php echo $base; ?>admin">Panel Admin</a>
                  </li>
                <?php endif; ?>

            <?php else: ?>
                <li>
                    <a class="<?php echo isActive($currentPage, ['login', 'signin']); ?>" href="<?php echo $base; ?>login">Se connecter</a>
                </li>
            <?php endif; ?>

      
        </ul>
    </nav>
</header>
<script src="<?php echo $base; ?>public/scripts/header_menu.js"></script>
