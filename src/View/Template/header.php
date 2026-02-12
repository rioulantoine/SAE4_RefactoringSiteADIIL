<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $isUserLoggedIn = isset($_SESSION['userid']);
    $isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'];

    // Try getenv first (works if .env is loaded into environment). Fallback: parse .env at project root.
    $baseUrl = getenv('BASE_URL');
    if (!$baseUrl) {
        $envPath = __DIR__ . '/../../../.env';
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                if ($key === 'BASE_URL') {
                    $value = trim($value);
                    // remove optional quotes
                    $value = trim($value, "'\"");
                    $baseUrl = $value;
                    break;
                }
            }
        }
    }

    $baseUrl = rtrim((string)$baseUrl, '/');
?>

<link rel="shortcut icon" href="<?php echo htmlspecialchars($baseUrl ? $baseUrl . '/public/admin/ressources/favicon.png' : '/public/admin/ressources/favicon.png', ENT_QUOTES, 'UTF-8'); ?>" type="image/x-icon">

<!-- HEADER -->
<header>
    <a id="accueil" href="<?php echo htmlspecialchars($baseUrl ? $baseUrl . '/index.php' : '/index.php', ENT_QUOTES, 'UTF-8'); ?>">
        <img src="<?php echo htmlspecialchars($baseUrl ? $baseUrl . '/public/assets/logo.png' : '/public/assets/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="Logo de l'ADIIL">
    </a>
    <nav>
        <ul>
            <li>
                <a href="/events.php">Événements</a>
            </li>
            <li>
                <a href="/news.php">Actualités</a>
            </li>
            <li>
                <a href="/shop.php">Boutique</a>
            </li>
            <li>
                <a href="/grade.php">Grades</a>
            </li>
            
            <?php if ($isUserLoggedIn): ?>
                <li>
                    <a href="/agenda.php">Agenda</a>
                </li>
            <?php endif; ?>

            <li>
                <a href="/about.php">À propos</a>
            </li>

            <?php if ($isUserLoggedIn): ?>
                <li>
                    <a href="/account.php">Mon compte</a>
                </li>

                <?php if ($isAdmin): ?>
                  <li>
                      <a id="header_admin" href="/admin/admin.php">Panel Admin</a>
                  </li>
                <?php endif; ?>

            <?php else: ?>
                <li>
                    <a href="/login.php">Se connecter</a>
                </li>
            <?php endif; ?>

      
        </ul>
    </nav>
</header>
