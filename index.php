<?php
    session_start();
    include "api/users.php";
?>
<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>UwUChan</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/mobile.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php include "views/sidebar.php"?>
                <section>
                    <div class="section-head">
                        <h1>Hírfolyam</h1>
                        <?php if (isset($_SESSION["user"])) { ?>
                        <a href="submit.php" class="button cta"><span class="material-symbols-rounded">history_edu</span>Új poszt írása</a>
                        <?php } ?>
                    </div>
                    <?php
                        include "api/system_messages.php";
                        getSystemMessage();
                        include "api/users.php";
                    ?>
                    <p>Majd itt lesz valami releváns kontent</p>
                    <!--
                    <div class="post-card">
                        <div class="card-head">
                            <a href="index.html">
                                <img class="user-profile-blog-avatar" src="img/system_message_avatar.png" alt="Profilkép">
                                <span>Rendszerüzenet</span>
                            </a>
                        </div>
                        <div class="post-content">
                            <div class="post-fragment">
                                <div class="post-body">
                                    <p class="post-title">Változások az oldal felépítésében</p>
                                    <p class="post-text">
                                        Hi!<br>
                                        Ezt az üzenetet azért látod, mert bizonyos új változásokat léptettünk életbe az oldal működésével, kinézetével kapcsolatban. Az üzeneteidet és a barátaidat a bal oldalon (vagy telefonon alul) található oldalsáv 'Kapcsolatok' menüpontja alatt éred el.<br><br>
                                        <br>
                                        Ui: A rendszerüzenetek az elévülésükig nem tűnnek el.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    -->
                    <!--
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
                            <a title="Bejelentés" class="right button icon flat" href="report.html"><span class="material-symbols-rounded">emoji_flags</span></a>
                        </div>
                        <div class="post-content">
                             <a class="post-images" href="index.html">
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
                                    <a class="button flat" href="post.php"><span class="material-symbols-rounded">forum</span>18</a>
                                    <button class="flat right"><span class="material-symbols-rounded">thumb_up</span>2,6E</button>
                                    <button class="flat"><span class="material-symbols-rounded" >thumb_down</span>10</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="post-card">
                        <div class="card-head">
                            <a href="index.html">
                                <img class="user-profile-blog-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                                <span>valaki423</span>
                            </a>
                            <span class="material-symbols-rounded">arrow_right</span>
                            <a href="index.html">
                                <img class="user-profile-blog-avatar" src="img/minta_trip.jpg" alt="trip">
                                <span>trip</span>
                            </a>
                            <a title="Bejelentés" class="right button icon flat" href="index.html"><span class="material-symbols-rounded">emoji_flags</span></a>
                        </div>
                        <div class="post-content">
                            <a class="post-images" href="index.html">
                                <div class="post-image-stack">
                                    <img src="img/minta_kekes.jpg" alt="kekes">
                                    <img src="img/minta_kekes2.jpg" alt="kekes2">
                                </div>
                                <p>5 kép</p>
                            </a>
                            <div class="post-fragment">
                                <a href="index.html" class="post-body">
                                    <p class="post-title">A Kékestető</p>
                                    <p class="post-text">A napsütéses órák száma jóval az országos átlag feletti, meghaladja az évi kétezret; különösen magas a napsütéses órák száma szeptember–októberben és januártól március végéig. 2011-ben itt mérték az országban a legkevesebb napsütéses órát: ebben az évben mindössze 2198 napsütéses óra volt Kékestetőn.</p>
                                </a>
                                <div class="reaction-bar">
                                    <a class="button flat" href="index.html"><span class="material-symbols-rounded">forum</span>0</a>
                                    <button class="flat right"><span class="material-symbols-rounded">thumb_up</span>12</button>
                                    <button class="flat"><span class="material-symbols-rounded" >thumb_down</span>3</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    -->
                </section>
            </div>
        </main>
    </body>
</html>