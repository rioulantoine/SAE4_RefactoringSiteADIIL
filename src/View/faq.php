
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - ADIIL</title>
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/general_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/header_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/footer_style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>public/styles/faq_style.css">
</head>

<body class="body_margin">

<?php require_once __DIR__ . '/Template/header.php' ?>

<H1 class='titre_section_apropos'>FOIRE AUX QUESTIONS</H1>

<section class="faq_container">
    
    <div class="faq_item">
        <div class="faq_question">
            <h3>Est-ce que Tom peut m'avancer 5€ pour un café ?</h3>
        </div>
        <div class="faq_answer">
            <p>Impossible. Tom, notre comptable, a une vue tellement précise qu'il a déjà calculé l'usure de la moquette si tu te déplaces jusqu'à la machine. Toute sortie de cash non justifiée par un tableur Excel de 40 colonnes entraîne une exclusion immédiate de l'IUT (et possiblement une malédiction sur ton code).</p>
        </div>
    </div>

    <div class="faq_item">
        <div class="faq_question">
            <h3>Pourquoi Mathis ne répond-il pas à mes SMS ?</h3>
        </div>
        <div class="faq_answer">
            <p>Mathis est chargé de communication. S'il ne peut pas te répondre via un post Instagram avec un filtre vintage ou une vidéo TikTok en 4K, il considère que l'information n'existe pas. Essaie de lui envoyer un pigeon voyageur avec un logo ADIIL, il appréciera le sens du design.</p>
        </div>
    </div>

    <div class="faq_item">
        <div class="faq_question">
            <h3>Enzo est-il vraiment le Président ou juste un bot IA ?</h3>
        </div>
        <div class="faq_answer">
            <p>La légende raconte qu'Enzo a été compilé en C++ en 2004. C'est pour cela qu'il est un leader naturel : il ne dort jamais, consomme uniquement de la caféine et peut gérer 15 projets en multithreading. S'il commence à dire "Segmentation Fault", débranchez-le et rebranchez-le.</p>
        </div>
    </div>

    <div class="faq_item">
        <div class="faq_question">
            <h3>J'ai perdu un document, Gémino peut m'aider ?</h3>
        </div>
        <div class="faq_answer">
            <p>Gémino, le Secrétaire, ne "retrouve" pas les documents. Il les fait apparaître par la seule force de son organisation. Attention : si ton document n'est pas trié par ordre alphabétique, date décroissante et taux d'humidité, Gémino te regardera avec une déception si profonde que tu finiras par trier tes propres chaussettes par couleur le soir même.</p>
        </div>
    </div>

    <div class="faq_item">
        <div class="faq_question">
            <h3>Que font Axel et Julien quand ils ne sont pas en cours ?</h3>
        </div>
        <div class="faq_answer">
            <p>Ils testent la résistance des chaises du BDE et cherchent de nouvelles idées d'événements. Leur dernière idée : une LAN de minage de cryptomonnaie sur les calculatrices du département. C'est "frais", c'est "innovant", mais le département informatique n'est pas encore au courant.</p>
        </div>
    </div>

</section>

<?php require_once __DIR__ . '/Template/footer.php' ?>

</body>
</html>