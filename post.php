<?php session_start(); include "api/users.php" ?>
<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>"Doktor úr, ezek a fényre jönnek!" - UwUChan</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/mobile.css">
        <link rel="stylesheet" href="css/post.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php include "views/sidebar.php"?>
                <section class="no-padding">
                    <div class="post-view">
                        <div class="card-head">
                            <a href="profile-other.php">
                                <img class="user-profile-blog-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                                <span>randomUser52</span>
                            </a>
                            <span class="material-symbols-rounded">arrow_right</span>
                            <a href="board.php">
                                <img class="user-profile-blog-avatar" src="img/minta_macsek.jpg" alt="macskak">
                                <span>macskak</span>
                            </a>
                        </div>
                        <div class="post-content">
                            <p class="post-title-mobile">“Doktor úr, ezek a fényre jönnek!”</p>
                            <a class="post-images" href="index.php">
                                <img src="./img/blog_macska.jpg" alt="macska">
                                <p>DSC_3829.jpg</p>
                            </a>
                            <div class="post-body">
                                <p class="post-title">“Doktor úr, ezek a fényre jönnek!”</p>
                                <p class="post-text">Ahogy ígértem, itt van a kép az új, gyönyörűséges alomról.
                                    A tündérbogárkáim már rendesen szopiznak és nőttön nőnek</p>
                            </div>
                        </div>
                        <div class="reaction-bar">
                            <a class="button flat hide-text-on-mobile" href="report.php"><span class="material-symbols-rounded">emoji_flags</span><span>Bejelentés</span></a>
                            <a class="button flat hide-text-on-mobile" href="index.php"><span class="material-symbols-rounded">share</span><span>Megosztás</span></a>
                            <button class="flat right"><span class="material-symbols-rounded">thumb_up</span>12</button>
                            <button class="flat"><span class="material-symbols-rounded" >thumb_down</span>3</button>
                        </div>
                    </div>
                    <form class="my-comment-bar">
                        <div>
                            <input type="text" placeholder="Ide írd a hozzászólásod" required><button class="flat"><span class="material-symbols-rounded">send</span></button>
                        </div>
                    </form>
                    <div id="comments" class="list">
                        <div class="post-comment-card">
                            <div class="card-head">
                                <a href="index.php">
                                    <img class="post-profile-messages-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                                    <span>abc123</span>
                                    <span class="post-profile-message-sent-time">15 perce</span>
                                </a>
                            </div>
                            <p>Jaj, egyem a kis szívüket!!</p>
                            <div class="reaction-bar">
                                <a href="index.php"><span class="post-comments">3 válasz</span></a>
                                <button class="flat right hide-text-on-mobile"><span class="material-symbols-rounded">reply</span><span>Válasz</span></button>
                                <button class="flat hide-text-on-mobile"><span class="material-symbols-rounded">flag</span><span>Bejelentés</span></button>
                            </div>
                        </div>
                        <div class="post-comment-card">
                            <div class="card-head">
                                <a href="index.php">
                                    <img class="post-profile-messages-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                                    <span>abc123</span>
                                    <span class="post-profile-message-sent-time">15 perce</span>
                                </a>
                            </div>
                            <p>Jaj, egyem a kis szívüket!!</p>
                            <div class="reaction-bar">
                                <a href="index.php"><span class="post-comments">3 válasz</span></a>
                                <button class="flat right hide-text-on-mobile"><span class="material-symbols-rounded">reply</span><span>Válasz</span></button>
                                <button class="flat hide-text-on-mobile"><span class="material-symbols-rounded">flag</span><span>Bejelentés</span></button>
                            </div>
                        </div>
                    </div>
                   <!--
                    <div class="no-comments-placeholder">
                        <span class="material-symbols-rounded">asterisk</span>
                        <p>Egyelőre nincsenek hozzászólások.</p>
                        <p>Légy te az első hozzászóló!</p>
                    </div>
                    -->
                </section>
            </div>
        </main>
    </body>
</html>