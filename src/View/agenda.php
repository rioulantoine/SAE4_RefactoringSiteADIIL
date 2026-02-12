<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php $base = rtrim(getenv('BASE_URL') ?: 'http://localhost/SAE4/SAE4_RefactoringSiteADIIL/', '/'); ?>
    <link rel="stylesheet" href="<?php echo $base; ?>/public/styles/planner_style.css">

    <link rel="stylesheet" href="<?php echo $base; ?>/public/styles/general_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/public/styles/footer_style.css">
    
</head>
<body class="body_margin">

<!--------------->
<!------PHP------>
<!--------------->

<?php 

// Importer les fichiers
require_once __DIR__ . '/Template/header.php';
require_once dirname(__DIR__) . '/Model/database.php';
require_once dirname(__DIR__, 2) . '/temp-site/files_save.php';
?>



<!--------------->
<!------HTML----->
<!--------------->

<H1>Agenda</H1>
<div>
    <iframe src="https://edt.gemino.dev">
    </iframe>
</div>


<?php require_once __DIR__ . '/Template/footer.php' ?>
</body>
</html>