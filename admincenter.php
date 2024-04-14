<?php session_start(); include "api/users.php"; include "api/acl.php"?>
<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Admin Központ - UwUChan</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/mobile.css">
        <link rel="stylesheet" href="css/admin.css">
        <link rel="stylesheet" href="css/thread.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php include "views/sidebar.php"?>
                <section class="no-padding">
                    <?php if (getUserField("privilege") == 1) { ?>
                    <div class="thread-toolbar top">
                        <b>Admin Központ</b>
                        <a title="Módosíthatod az éppen kiírt rendszerüzenetet" class="button icon flat right hide-text-on-mobile"><span class="material-symbols-rounded">edit_note</span><span>Rendszerüzenet</span></a>
                        <a title="Adminok listája" class="button icon flat hide-text-on-mobile"><span class="material-symbols-rounded">supervisor_account</span><span>Adminok</span></span></a>
                        <a title="Üzenőfalak kezelése" class="button icon flat hide-text-on-mobile"><span class="material-symbols-rounded">dashboard</span><span>Üzenőfalak</span></span></a>
                    </div>
                    <div class="section-inset">
                        <?php
                            if (file_exists("data/last_update.txt")) {
                                $build = file_get_contents("data/last_update.txt");
                                echo "<p>Build dátuma: $build</p>";
                            }
                        ?>
                        <div class="admin-dashboard">
                            <div class="dashboard-item">
                                <p>Összes felhasználó</p>
                                <p><?php echo getUserCount() ?></p>
                            </div>
                            <div class="dashboard-item blue">
                                <p>Bírálatra váró tartalmak</p>
                                <p>0</p>
                            </div>
                            <div class="dashboard-item green">
                                <p>Moderált tartalom</p>
                                <p>0</p>
                            </div>
                            <div class="dashboard-item orange">
                                <p>Üzenőfal-létrehozási kérelmek</p>
                                <p>0</p>
                            </div>
                        </div>
                        <div class="tab-bar">
                            <button class="active">Bírálatra vár</button>
                            <button>Moderált elemek</button>
                            <button>Üzenőfal-kérelmek</button>
                        </div>
                        <!--
                        <div id="reports">
                            <div class="post-card">
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
                                    <span class="right">#123456</span>
                                </div>
                                <div class="post-content">
                                    <a class="post-images" href="index.php">
                                        <img src="./img/blog_macska.jpg" alt="macska">
                                        <p>DSC_3829.jpg</p>
                                    </a>
                                    <div class="post-fragment">
                                        <a href="post.php" class="post-body">
                                            <p class="post-title">“Doktor úr, ezek a fényre jönnek!”</p>
                                            <p class="post-text">Ahogy ígértem, itt van a kép az új, gyönyörűséges alomról. A tündérbogárkáim már rendesen szopiznak és nőttön nőnek</p>
                                        </a>
                                        <div class="reaction-bar">
                                            <span>Bejelentés oka: tiltott szó (szop)</span>
                                            <button class="flat right hide-text-on-mobile"><span class="material-symbols-rounded">undo</span><span>Visszaállítás</span></button>
                                            <button class="flat hide-text-on-mobile destructive"><span class="material-symbols-rounded">delete</span><span>Törlés</span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="post-card">
                                <div class="card-head">
                                    <a href="index.php">
                                        <img class="user-profile-blog-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                                        <span>xd43783f</span>
                                    </a>
                                    <span class="material-symbols-rounded">arrow_right</span>
                                    <a href="board.php">
                                        <img class="user-profile-blog-avatar" src="img/default_user_avatar.png" alt="macskak">
                                        <span>politika</span>
                                    </a>
                                    <span class="right">#123456</span>
                                </div>
                                <div class="post-content">
                                    <div class="post-fragment">
                                        <a href="post.php" class="post-body">
                                            <p class="post-text">A k** anyukád!</p>
                                        </a>
                                        <div class="reaction-bar">
                                            <span>Bejelentés oka: személyeskedés</span>
                                            <button class="flat right hide-text-on-mobile"><span class="material-symbols-rounded">undo</span><span>Visszaállítás</span></button>
                                            <button class="flat hide-text-on-mobile destructive"><span class="material-symbols-rounded">delete</span><span>Törlés</span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="post-card">
                                <div class="card-head">
                                    <a href="index.php">
                                        <img class="user-profile-blog-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                                        <span>kaltika</span>
                                    </a>
                                    <span class="right">#123456</span>
                                </div>
                                <div class="post-content">
                                    <div class="post-fragment">
                                        <div class="reaction-bar">
                                            <span>Bejelentés oka: pedofil tartalom terjesztése az oldalon</span>
                                            <button class="flat right hide-text-on-mobile"><span class="material-symbols-rounded">undo</span><span>Visszaállítás</span></button>
                                            <button class="flat hide-text-on-mobile destructive"><span class="material-symbols-rounded">delete</span><span>Törlés</span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        -->
                    </div>
                    <?php } else { ?>
                        <p>Az oldal megtekintéséhez rendszergazdai jogosultság szükséges.</p>
                    <?php } ?>
                </section>
            </div>
        </main>
    </body>
</html>