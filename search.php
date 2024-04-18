<?php
    session_start();
    include "api/users.php";
    include "api/boards.php";
    include "api/posts.php";

    $t1 = time();

    $foundBoards = [];
    $foundPosts = [];
    $foundPeople = [];

    $error = "";
    if (!isset($_GET["q"]) || trim($_GET["q"]) == "") {
        $error = "Nincs megadva keresési kifejezés.";
    } else {
        $query = strtolower($_GET["q"]) ;
        $dir = new DirectoryIterator("data/boards");
        foreach ($dir as $info) {
            if (!$info->isDot() && $info->isDir()) {
                $file = $info->getBasename();
                $u = json_decode(file_get_contents("data/boards/" . $info->getBasename() . "/metadata.json"));
                if (str_contains($file, $query) || str_contains(strtolower($u->bio), $query)) {
                    $board = new stdClass();
                    $board->name = $info->getBasename();
                    $board->bio = $u->bio;
                    $foundBoards[] = $board;
                }

                $dir2 = new DirectoryIterator("data/boards/$file");
                foreach ($dir2 as $info2) {
                    if (!$info2->isDot() && $info2->getBasename()!="metadata.json") {
                        $post = json_decode(file_get_contents("data/boards/" . $info->getBasename() . "/" . $info2->getBasename()));
                        if (str_contains(strtolower($post->title), $query)) {
                            $foundPosts[] = str_replace(".json", "", $info->getBasename()."/".$info2->getBasename());
                        }
                    }
                }
            }
        }

        $dir = new DirectoryIterator("data/users");
        foreach ($dir as $info) {
            if (!$info->isDot() && $info->isDir()) {
                $file = $info->getBasename();
                if (str_contains($file, $query)) {
                    $foundPeople[] = $file;
                }
            }
        }



        $searchDuration = time() - $t1;
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
        <link rel="stylesheet" href="css/messages.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php include "views/sidebar.php"?>
                <section>
                    <?php if ($error == "") { ?>
                    <div class="section-head">
                        <span class="disabled"><?php echo count($foundBoards)+count($foundPeople)+count($foundPosts) ?> találat, <?php echo $searchDuration ?> ms alatt</span>
                    </div>
                    <?php } ?>
                    <?php if (count($foundPosts) != 0 && $error == "") { ?>
                        <p class="card-header">Posztok</p>
                        <div class="list">
                            <?php
                            foreach ($foundPosts as $post) {
                                getPostCard($post, true);
                            }
                            ?>
                        </div>
                    <?php } ?>
                    <?php if (count($foundBoards) != 0 && $error == "") { ?>
                    <p class="card-header">Üzenőfalak</p>
                    <div class="list">
                        <?php
                            foreach ($foundBoards as $board) {
                                $boardIcon = getBoardIcon($board->name);
                                echo "<div class=\"messages-card-head\">
                                            <img class=\"user-profile-messages-avatar\" src=\"$boardIcon\" alt=\"$board->name ikonja\">
                                            <a href=\"board.php?n=$board->name\" class=\"messages-card-preview\">
                                                <span>$board->name</span>
                                                <p>$board->bio</p>
                                            </a>
                                            </div>";
                            }
                        ?>
                    </div>
                    <?php } ?>
                    <?php if (count($foundPeople) != 0 && $error == "") { ?>
                        <p class="card-header">Emberek</p>
                        <div class="list">
                            <?php
                            foreach ($foundPeople as $person) {
                                $pfp = getUserProfilePicture($person);
                                echo "<div class=\"messages-card-head\">
                                            <img class=\"user-profile-messages-avatar\" src=\"$pfp\" alt=\"$person profilképe\">
                                            <a href=\"profile-other.php?n=$person\" class=\"messages-card-preview\">
                                                <span>$person</span>
                                            </a>
                                            </div>";
                            }
                            ?>
                        </div>
                    <?php } ?>
                    <?php
                        if ($error != "") {
                            echo "<p>$error</p>";
                        }
                    ?>
                </section>
            </div>
        </main>
    </body>
</html>