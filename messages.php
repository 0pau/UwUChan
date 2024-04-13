<?php session_start(); include "api/users.php"; include "api/acl.php"?>
<!DOCTYPE html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Kapcsolatok - UwUChan</title>
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
                        <h1>Üzenetek</h1>
                        <div class="tab-bar compact">
                            <a class="button hide-text-on-mobile active"><span class="material-symbols-rounded">chat</span><span>Üzenetek</span></a>
                            <a class="button hide-text-on-mobile" href="friends.php"><span class="material-symbols-rounded">group</span><span>Barátok</span></a>
                        </div>
                    </div>
                    <div class="list">
                        <div class="messages-card-head">
                            <a href="profile-other.php">
                                <img class="user-profile-messages-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                            </a>
                            <a href="thread.php" class="messages-card-preview">
                                <span>randomUser52</span>
                                <p>Ugye milyen aranyosak??</p>
                            </a>
                            <span class="time-since-last">2 perce</span>
                            <a title="Törlés" class="right button icon flat" href=""><span class="material-symbols-rounded">delete</span></a>
                        </div>
                        <div class="messages-card-head">
                            <a href="">
                                <img class="user-profile-messages-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                            </a>
                            <div class="messages-card-preview">
                                <span>teszt_user</span>
                                <p>UwU!!!</p>
                            </div>
                            <span class="time-since-last">1 napja</span>
                            <a title="Törlés" class="right icon button flat" href=""><span class="material-symbols-rounded">delete</span></a>
                        </div>
                        <div class="messages-card-head">
                            <a href="">
                                <img class="user-profile-messages-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                            </a>
                            <div class="messages-card-preview">
                                <span>[törölt felhasználó]</span>
                                <p>xdddd</p>
                            </div>
                            <span class="time-since-last">15 napja</span>
                            <a title="Törlés" class="right icon button flat" href=""><span class="material-symbols-rounded">delete</span></a>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>