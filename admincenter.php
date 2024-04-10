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
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body>
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <nav>
                    <div>
                        <a href="index.php"><span class="material-symbols-rounded">home</span><span class="nav-item-title">Hírfolyam</span></a>
                        <a href="messages.php"><span class="material-symbols-rounded">3p</span><span class="nav-item-title">Üzenetek és barátok</span></a>
                        <a href="admincenter.php" class="current"><span class="material-symbols-rounded">build</span><span class="nav-item-title">Admin Központ</span></a>
                    </div>
                    <div class="followed-boards">
                        <div class="followed-list">
                            <p class="nav-header">Követett üzenőfalak</p>
                            <a href="board.php"><img alt="macskak" src="img/minta_macsek.jpg"><span class="nav-item-title">macskak</span></a>
                        </div>
                    </div>
                    <div>
                        <p class="nav-header">Információk, visszajelzés</p>
                        <a href="help/index.html"><span class="material-symbols-rounded">help</span><span class="nav-item-title">Tudakozó</span></a>
                        <a class="disabled" href="404.html"><span class="material-symbols-rounded">how_to_vote</span><span class="nav-item-title">Ötletdoboz</span></a>
                    </div>
                </nav>
                <section>
                    <?php if (getUserField("privilege") == 1) { ?>
                    <div class="section-head">
                        <h1>Admin Központ</h1>
                    </div>
                    <div class="admin-warning">
                        <span class="material-symbols-rounded">emergency_home</span>
                        <p>Egy rendszerüzenet ki van írva, melynek lejárati dátuma: 2024. 06. 04.</p>
                        <div class="button-box">
                            <button>Szerkesztés</button>
                            <button>Eltávolítás</button>
                        </div>
                    </div>
                    <div class="admin-dashboard">
                        <div class="dashboard-item">
                            <p>Összes felhasználó</p>
                            <p><?php echo getUserCount() ?></p>
                        </div>
                        <div class="dashboard-item blue">
                            <p>Új bejelentések / Bírálatra váró tartalmak</p>
                            <p>2 / 13</p>
                        </div>
                        <div class="dashboard-item green">
                            <p>Moderált tartalom</p>
                            <p>51</p>
                        </div>
                        <div class="dashboard-item orange">
                            <p>Üzenőfal-létrehozási kérelmek</p>
                            <p>3</p>
                        </div>
                    </div>
                    <div class="tab-bar">
                        <button class="active">Bírálatra vár</button>
                        <button>Moderált elemek</button>
                        <button>Üzenőfal-kérelmek</button>
                    </div>
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
                    <?php } else { ?>
                    <p>Az oldal megtekintéséhez rendszergazdai jogosultság szükséges.</p>
                    <?php } ?>
                </section>
            </div>
        </main>
    </body>
</html>