<?php session_start(); include "api/users.php" ?>
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
                        <img alt="Profilkép" class="board-backdrop" src="./img/default_user_avatar.png">
                        <img alt="Profilkép" class="board-head-image" src="./img/default_user_avatar.png">
                        <div class="board-head-details">
                            <h1>randomUser52</h1>
                        </div>
                        <div class="board-buttons">
                            <button>Barátkérelem küldése</button>
                        </div>
                    </div>
                    <div class="section-inset">
                        <div class="post-card">
                            <div class="card-head">
                                <a href="profile-other.html">
                                    <img class="user-profile-blog-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                                    <span>randomUser52</span>
                                </a>
                                <span class="material-symbols-rounded">arrow_right</span>
                                <a href="board.php">
                                    <img class="user-profile-blog-avatar" src="img/minta_macsek.jpg" alt="macskak">
                                    <span>macskak</span>
                                </a>
                                <a class="right button icon flat" href="index.php"><span class="material-symbols-rounded">emoji_flags</span></a>
                            </div>
                            <div class="post-content">
                                <a class="post-images" href="index.php">
                                    <img src="./img/blog_macska.jpg" alt="macska">
                                    <p>DSC_3829.jpg</p>
                                </a>
                                <div class="post-fragment">
                                    <a href="post.php" class="post-body">
                                        <p class="post-title">“Doktor úr, ezek a fényre jönnek!”</p>
                                        <p class="post-text">Ahogy ígértem, itt van a kép az új, gyönyörűséges alomról.
                                            A tündérbogárkáim már rendesen szopiznak és nőttön nőnek</p>
                                    </a>
                                    <div class="reaction-bar">
                                        <a class="button flat" href="index.php"><span class="material-symbols-rounded">forum</span>18</a>
                                        <button class="flat right"><span class="material-symbols-rounded">thumb_up</span>2,6E</button>
                                        <button class="flat"><span class="material-symbols-rounded" >thumb_down</span>10</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>