<?php
    session_start();
    include "api/users.php";
    include "api/util.php";
    include "api/comments.php";

    $error = "";

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

    function comment()
    {
        $text = validateText($_POST["text"]);
        $where = $_POST["where"];

        if (!$text) {
            throw new Error("A komment nem tartalmazhat HTML kódot!");
        }

        $replyID = null;
        if ($_POST["replyID"] != "") {
            $replyID = $_POST["replyID"];
        }
        postComment($where, $text, $replyID);
        header("Location: ". $_SERVER["HTTP_REFERER"]);
    }

    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            comment();
        }
    } catch (Error $e) {
        $error = $e->getMessage();
    }

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
        <script>

            var currentlyHighlighted = "";
            function showReplyUI(id, username) {
                hideReplyUI();
                document.getElementById("replyIDField").value = id;
                document.getElementById("reply-indicator-username").innerHTML = username;
                document.getElementById(id).classList.add("highlight");
                document.getElementById("reply-indicator").classList.add("active");
                currentlyHighlighted = id;
            }

            function hideReplyUI() {
                if (currentlyHighlighted != "") {
                    document.getElementById("replyIDField").value = "";
                    document.getElementById("reply-indicator").classList.remove("active");
                    document.getElementById(currentlyHighlighted).classList.remove("highlight");
                }
            }
        </script>
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
                                echo formatDateRelative($post_meta->posted_at, true);
                                ?></span>
                        </div>
                        <div class="post-content">
                            <p class="post-title-mobile"><?php echo $post_meta->title ?></p>
                            <?php if(count($post_meta->images) != 0) {
                                if (count($post_meta->images) == 1) {
                                    $th = "data/images/".$post_meta->images[0]->thumbnail;
                                    $title = $post_meta->images[0]->title;
                                    echo "<a class=\"post-images\" href=\"data/images/".$post_meta->images[0]->original."\">
                                                <img src=\"$th\" alt=\"$title\">
                                                <p>$title</p>
                                            </a>";
                                } else {
                                    echo "<div class=\"post-images\"><div class=\"post-image-stack\">";

                                    foreach ($post_meta->images as $image) {
                                        $th = "data/images/".$image->thumbnail;
                                        $title = $image->title;
                                        echo "<a href=\"data/images/$image->original\"><img src=\"$th\" alt=\"$title\"></a>";
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
                            <form class="right" action="api/post_actions.php" method="POST">
                                <input type="hidden" name="action" value="like">
                                <input type="hidden" name="data" value="<?php echo $_GET["n"]; ?>">
                                <button class="flat <?php if (isPostLiked($_GET["n"]) != -1) echo "accentFg" ?>"><span class="material-symbols-rounded">thumb_up</span><?php echo $post_meta->likes; ?></button>
                            </form>
                            <form action="api/post_actions.php" method="POST">
                                <input type="hidden" name="action" value="dislike">
                                <input type="hidden" name="data" value="<?php echo $_GET["n"]; ?>">
                                <button class="flat <?php if (isPostDisliked($_GET["n"]) != -1) echo "accentFg" ?>"><span class="material-symbols-rounded" >thumb_down</span><?php echo $post_meta->dislikes; ?></button>
                            </form>
                        </div>
                    </div>
                    <form class="my-comment-bar" method="POST">
                        <input type="hidden" name="where" value="<?php echo $_GET["n"] ?>">
                        <input type="hidden" name="replyID" value="" id="replyIDField">
                        <div id="reply-indicator">
                            <p class="disabled" id="reply-indicator-text">Válasz neki: <span id="reply-indicator-username">username</span></p>
                            <span onclick="hideReplyUI()" class="material-symbols-rounded right" id="cancel-reply">cancel</span>
                        </div>
                        <div class="reply-input">
                            <input type="text" placeholder="Ide írd a hozzászólásod" name="text" required><button class="flat"><span class="material-symbols-rounded">send</span></button>
                        </div>
                        <?php if ($error != "") { ?>
                            <p class="comment-error"><?php echo $error ?></p>
                        <?php } ?>
                    </form>
                    <div id="comments" class="">
                        <?php if (count($post_meta->comments) == 0) { ?>
                        <div class="no-comments-placeholder">
                            <span class="material-symbols-rounded">asterisk</span>
                            <p>Egyelőre nincsenek hozzászólások.</p>
                            <p>Légy te az első hozzászóló!</p>
                        </div>
                        <?php } else {
                            $comments = getComments($_GET["n"]);
                            foreach ($comments as $comment) {
                                printComment($comment);
                            }
                        }?>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>