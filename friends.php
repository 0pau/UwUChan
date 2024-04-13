<?php session_start(); include "api/users.php"; include "api/acl.php"?>
<!DOCTYPE html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Barátok - UwUChan</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/mobile.css">
        <link rel="stylesheet" href="css/messages.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php  include "views/sidebar.php"?>
                <section>
                    <div class="section-head">
                        <h1>Barátok</h1>
                        <div class="tab-bar compact">
                            <a class="button hide-text-on-mobile" href="messages.php"><span class="material-symbols-rounded">chat</span><span>Üzenetek</span></a>
                            <a class="button hide-text-on-mobile active"><span class="material-symbols-rounded">group</span><span>Barátok</span></a>
                        </div>
                    </div>
                    <p class="card-header">Beérkezett barátkérelmek</p>
                    <div class="list">
                        <div class="messages-card-head">
                            <a href="profile-other.php">
                                <img class="user-profile-messages-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                            </a>
                            <div class="messages-card-preview">
                                <span>kalitka</span>
                            </div>
                            <button title="Elfogadás" class="flat icon right"><span class="material-symbols-rounded">done</span></button>
                            <button title="Elutasítás" class="flat icon"><span class="material-symbols-rounded">close</span></button>
                            <button title="Letiltás" class="flat icon"><span class="material-symbols-rounded">block</span></button>
                        </div>
                    </div>
                    <p class="card-header">Barátaim</p>
                    <div class="list">
                        <div class="messages-card-head">
                            <a href="">
                                <img class="user-profile-messages-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                            </a>
                            <div class="messages-card-preview">
                                <span>randomUser52</span>
                            </div>
                            <button title="Barát eltávolítása" class="flat icon right"><span class="material-symbols-rounded">close</span></button>
                            <button title="Letiltás" class="flat icon"><span class="material-symbols-rounded">block</span></button>
                        </div>
                        <div class="messages-card-head">
                            <a href="">
                                <img class="user-profile-messages-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                            </a>
                            <div class="messages-card-preview">
                                <span>teszt_user</span>
                            </div>
                            <button title="Barát eltávolítása" class="flat icon right"><span class="material-symbols-rounded">close</span></button>
                            <button title="Letiltás" class="flat icon"><span class="material-symbols-rounded">block</span></button>
                        </div>
                        <div class="messages-card-head">
                            <a href="">
                                <img class="user-profile-messages-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                            </a>
                            <div class="messages-card-preview">
                                <span>[törölt felhasználó]</span>
                            </div>
                            <button title="Barát eltávolítása" class="flat icon right"><span class="material-symbols-rounded">close</span></button>
                            <button title="Letiltás" class="flat icon"><span class="material-symbols-rounded">block</span></button>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>