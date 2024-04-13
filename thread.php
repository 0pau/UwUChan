<?php session_start(); include "api/users.php"; include "api/acl.php"?>
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
        <link rel="stylesheet" href="css/thread.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php include "views/sidebar.php" ?>
                <section class="no-padding thread-view">
                    <div class="thread-toolbar top">
                        <a title="Vissza az üzenetekre" class="button icon flat" href="messages.php"><span class="material-symbols-rounded">arrow_back</span></a>
                        <span class="user-name">randomUser52</span>
                        <button title="Felhasználó titltása" class="right flat icon"><span class="material-symbols-rounded">block</span></button>
                    </div>
                    <div class="thread">
                        <div class="message mine">
                            <span class="bubble">Tetszett a kiscicás posztod :)</span>
                            <span class="sent-at">09:11</span>
                            <div class="options">
                                <button title="Üzenet törlése" class="flat round icon vsmall"><span class="material-symbols-rounded">delete</span></button>
                                <button title="Válasz" class="flat round icon vsmall"><span class="material-symbols-rounded">reply</span></button>
                            </div>
                        </div>
                        <div class="message away">
                            <span class="bubble">Ugye milyen aranyosak??</span>
                            <span class="sent-at">11:20</span>
                            <div class="options">
                                <button title="Válasz" class="flat round icon vsmall"><span class="material-symbols-rounded">reply</span></button>
                            </div>
                        </div>
                    </div>
                    <div class="thread-toolbar bottom my-comment-bar">
                        <input type="text" placeholder="Ide írd az üzenetet" id="message" required><button class="flat"><span class="material-symbols-rounded">send</span></button>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>