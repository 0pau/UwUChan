<?php
    session_start(); include "api/users.php";

    if (!isset($_GET["n"])) {
        include "404.html";
        die();
    }

    if (!userExists($_GET["n"])) {
        include "404.html";
        die();
    }

    $posts = getUserPosts($_GET["n"]);
?>
<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Macskák - UwUChan</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/mobile.css">
        <link rel="stylesheet" href="css/board.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php include "views/sidebar.php"?>
                <section class="no-padding">
                    <div class="board-head">
                        <img alt="Profilkép" class="board-backdrop" src="<?php echo getUserProfilePicture($_GET["n"]) ?>">
                        <img alt="Profilkép" class="board-head-image" src="<?php echo getUserProfilePicture($_GET["n"]) ?>">
                        <div class="board-head-details">
                            <h1><?php echo $_GET["n"] ?></h1>
                        </div>
                        <?php if (isset($_SESSION["user"])) { ?>
                        <div class="board-buttons">
                            <button>Barátkérelem küldése</button>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="section-inset">
                        <?php if (count($posts) == 0) { ?>
                            <div class="no-comments-placeholder">
                                <span class="material-symbols-rounded">asterisk</span>
                                <p>Ennek a felhasználónak nincsenek posztjai.</p>
                            </div>
                        <?php } else {
                            include_once "api/posts.php";
                            foreach ($posts as $post) {
                                getPostCard($post);
                            }
                        }?>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>