<?php
require_once __DIR__ . '/../Service/files_save.php';
require_once __DIR__ . '/../Model/ModelMyGallery.php';

$db = new DB();

        $isLoggedIn = isset($_SESSION["userid"]);
        $limit = 10;

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if (isset($_GET["show"]) && ctype_digit($_GET["show"])) {
                $limit = (int) $_GET["show"];
            }

            if(isset($_GET['eventid']) && $isLoggedIn){

                $eventid = $_GET['eventid'];
                $userid = $_SESSION["userid"];
            }else {
                header("Location: /index.php");
                exit;
            }
        }

        $event = getMyGalleryEvent($db, $eventid);

        $medias = getMyGalleryMedias($db, $userid, $eventid, $limit);
require_once __DIR__ . '/../View/api/my_gallery.php';