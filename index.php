<?php
    session_start();
    include "api/users.php";
    include "api/boards.php";
    include "api/posts.php";

    $error = "";

    $offset = 0;
    if (isset($_GET["o"])) {
        $offset = intval($_GET["o"]);
    }
    if ($offset < 0) {
        $offset = 0;
    }

    $posts = [];
    $last = false;
    $lastOffset = 0;

    try {
        global $posts;
        global $last;

        if (!file_exists("data/post_activity.dat")) {
            throw new Error("Nem található a posztokat tartalmazó fájl. Jelezd egy adminisztrátornak, hogy frissítse a hírfolyamokat!");
        }

        $file = fopen("data/post_activity.dat", "r");
        $line_count = 0;

        while (!feof($file) && $line_count != $offset + 15) {
            $line = fgets($file);
            $lastOffset++;
            if ($line_count < $offset) {
                $line_count++;
                continue;
            }
            $line_count++;
            if ($line == "") {
                continue;
            }
            if (!isset($_SESSION["user"]) || (isBoardFollowed(explode("/", $line)[0]) && getPostAuthor(trim($line)) != $_SESSION["user"])) {
                $posts[] = trim($line);
            } else {
                $line_count--;
            }
        }

        if (feof($file)) {
            $last = true;
        }

        fclose($file);
    } catch (Error $err) {
        $error = $err->getMessage();
    }

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
                    ?>

                    <?php if ($error) { ?>
                        <div class="no-comments-placeholder">
                            <span class="material-symbols-rounded">sentiment_very_dissatisfied</span>
                            <p>A hírfolyam összeomlott.</p>
                            <p><?php echo $error ?></p>
                        </div>
                    <?php } ?>

                    <?php
                        if (!$error && count(getFollowedBoards()) == 0 && isset($_SESSION["user"])) {
                            include "views/not_following_placeholder.html";
                            $last = true;
                        } else {
                            if (count($posts) != 0) {
                                foreach ($posts as $post) {
                                    getPostCard($post, "post.php?n=$post");
                                }
                            }
                        }
                    ?>
                    <?php if (!$error && $last && count(getFollowedBoards()) != 0 && isset($_SESSION["user"])) { ?>
                        <p class="disabled centered">Elérted a hírfolyam végét.</p>
                    <?php } else if (!$last && !$error) { ?>
                        <a href="index.php?o=<?php echo $lastOffset ?>" class="">További posztok betöltése</a>
                    <?php } ?>
                </section>
            </div>
        </main>
    </body>
</html>