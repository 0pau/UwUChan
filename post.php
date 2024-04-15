<?php
    session_start(); include "api/users.php";

    $post_meta = "";

    if (!isset($_GET["n"])) {
        include "404.html";
        die();
    }

    if (!file_exists("data/boards/".$_GET["n"].".json")) {
        include "404.html";
        die();
    }

    $board = explode("/", $_GET["n"]);
    $board_name = $board[0];
    $post_meta = file_get_contents("data/boards/".$_GET["n"].".json");
    $post_meta = json_decode($post_meta, false);

?>
<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?php echo $post_meta->title ?> - UwUChan</title>
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
                            <a href="profile-other.php?n=<?php echo $post_meta->author ?>">
                                <img class="user-profile-blog-avatar" src="<?php echo getUserProfilePicture($post_meta->author) ?>" alt="Profilkép">
                                <span><?php echo $post_meta->author ?></span>
                            </a>
                            <span class="posted-at-text"><?php
                                echo gmdate("Y. m. d. H:i")
                                ?></span>
                        </div>
                        <div class="post-content">
                            <p class="post-title-mobile"><?php echo $post_meta->title ?></p>
                            <?php if(count($post_meta->images) != 0) {
                                if (count($post_meta->images) == 1) {
                                    $th = "data/images/".$post_meta->images[0]->thumbnail;
                                    $title = $post_meta->images[0]->title;
                                    echo "<a class=\"post-images\" href=\"index.php\">
                                                <img src=\"$th\" alt=\"$title\">
                                                <p>$title</p>
                                            </a>";
                                } else {
                                    echo "<div class=\"post-images\"><div class=\"post-image-stack\">";

                                    foreach ($post_meta->images as $image) {
                                        $th = "data/images/".$image->thumbnail;
                                        $title = $image->title;
                                        echo "<img src=\"$th\" alt=\"$title\">";
                                    }
                                    $count = count($post_meta->images);

                                    echo "</div><p>$count kép</p></div>";
                                }
                            } ?>
                            <div class="post-body">
                                <p class="post-title"><?php echo $post_meta->title ?></p>
                                <p class="post-text"><?php echo $post_meta->body ?></p>
                            </div>
                        </div>
                        <div class="reaction-bar">
                            <a class="button flat hide-text-on-mobile" href="report.php?w=<?php echo $_GET["n"]?>"><span class="material-symbols-rounded">emoji_flags</span><span>Bejelentés</span></a>
                            <a class="button flat hide-text-on-mobile" href="index.php"><span class="material-symbols-rounded">share</span><span>Megosztás</span></a>
                            <button class="flat right"><span class="material-symbols-rounded">thumb_up</span><?php echo $post_meta->likes; ?></button>
                            <button class="flat"><span class="material-symbols-rounded" >thumb_down</span><?php echo $post_meta->dislikes; ?></button>
                        </div>
                    </div>
                    <form class="my-comment-bar">
                        <div>
                            <input type="text" placeholder="Ide írd a hozzászólásod" required><button class="flat"><span class="material-symbols-rounded">send</span></button>
                        </div>
                    </form>
                    <div id="comments" class="list">
                        <?php if (count($post_meta->comments) == 0) { ?>
                        <div class="no-comments-placeholder">
                            <span class="material-symbols-rounded">asterisk</span>
                            <p>Egyelőre nincsenek hozzászólások.</p>
                            <p>Légy te az első hozzászóló!</p>
                        </div>
                        <?php } ?>
                        <!--
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
                        -->
                    </div>
                   <!--

                    -->
                </section>
            </div>
        </main>
    </body>
</html>