<?php
    session_start();
    include "api/users.php";
    include "api/boards.php";
    $board_name = "";
    if (isset($_GET["n"])) {
        $board_name = $_GET["n"];
    } else {
        include "404.html";
        die();
    }
    if (!boardExists($board_name)) {
        include "404.html";
        die();
    }
    $board_meta = getBoardInfo($board_name);

    if ($board_meta == null) {
        include "404.html";
        die();
    }

    $board_icon = getBoardIcon($board_name);

    $created_at = gmdate("Y. m. d.", $board_meta->created);
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
                <?php
                    saveBoardVisit($board_name);
                    include "views/sidebar.php";
                ?>
                <section class="no-padding">
                    <div class="board-head">
                        <img alt="Üzenőfal ikonja" class="board-backdrop" src="<?php echo $board_icon ?>">
                        <img alt="Üzenőfal ikonja" class="board-head-image" src="<?php echo $board_icon ?>">
                        <div class="board-head-details">
                            <h1><?php echo $board_name ?></h1>
                            <p><?php echo $board_meta->bio ?></p>
                            <p id="board-creation-date">Létrehozva: <?php echo $created_at ?></p>
                        </div>
                        <form method="post" action="api/user_actions.php">
                            <input type="hidden" name="board" value="<?php echo $board_name ?>">
                            <input type="hidden" name="action" value="toggle-follow">
                            <?php if (isBoardFollowed($board_name)) { ?>
                            <button>Követés eltávolítása</button>
                            <?php } else { ?>
                            <button>Követés</button>
                            <?php }?>
                        </form>
                    </div>
                    <div class="section-inset">
                        <div class="section-head">
                            <a href="submit.php?board=<?php echo $board_name ?>" class="button cta right"><span class="material-symbols-rounded">history_edu</span>Új poszt írása</a>
                        </div>
                        <?php
                            if ($board_meta->post_count == 0) {
                                include "views/no_post_placeholder.html";
                            }
                        ?>
                        <!--
                        <div class="post-card">
                            <div class="card-head">
                                <a href="profile-other.php">
                                    <img class="user-profile-blog-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                                    <span>randomUser52</span>
                                </a>
                                <span class="material-symbols-rounded">arrow_right</span>
                                <a href="board.html">
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
                                        <a class="button flat" href="post.php"><span class="material-symbols-rounded">forum</span>18</a>
                                        <button class="flat right"><span class="material-symbols-rounded">thumb_up</span>2,6E</button>
                                        <button class="flat"><span class="material-symbols-rounded" >thumb_down</span>10</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="post-card">
                            <div class="card-head">
                                <a href="profile-other.php">
                                    <img class="user-profile-blog-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                                    <span>randomUser52</span>
                                </a>
                                <span class="material-symbols-rounded">arrow_right</span>
                                <a href="board.html">
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
                        <div class="post-card">
                            <div class="card-head">
                                <a href="profile-other.php">
                                    <img class="user-profile-blog-avatar" src="img/default_user_avatar.png" alt="Profilkép">
                                    <span>randomUser52</span>
                                </a>
                                <span class="material-symbols-rounded">arrow_right</span>
                                <a href="board.html">
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
                            -->
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>