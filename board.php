<?php
session_start();

const POST_PER_SCREEN = 10;

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

$created_at = date("Y. m. d.", $board_meta->created);

$pager = 0;
if (isset($_GET["p"])) {
    $pager = intval($_GET["p"]);
}
?>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $board_name ?> - UwUChan</title>
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
                <?php if (isset($_SESSION["user"])) { ?>
                    <form method="post" action="api/user_actions.php">
                        <input type="hidden" name="board" value="<?php echo $board_name ?>">
                        <input type="hidden" name="action" value="toggle-follow">
                        <?php if (isBoardFollowed($board_name)) { ?>
                            <button>Követés eltávolítása</button>
                        <?php } else { ?>
                            <button>Követés</button>
                        <?php }?>
                    </form>
                <?php }?>
            </div>
            <div class="section-inset">
                <div class="section-head">
                    <a href="submit.php?board=<?php echo $board_name ?>" class="button cta right"><span class="material-symbols-rounded">history_edu</span>Új poszt írása</a>
                </div>
                <?php
                $is_last = true;

                if ($board_meta->post_count == 0) {
                    include "views/no_post_placeholder.html";
                } else {

                    include "api/posts.php";
                    $last_id = $board_meta->post_count;

                    if ($pager != 0) {
                        $last_id -= $pager*POST_PER_SCREEN;
                    }

                    $shown_count = 0;
                    $is_last = false;

                    if ($last_id < 1) {
                        $is_last = true;
                        $pager = 0;
                    }

                    error_reporting(E_ALL);

                    while ($shown_count < POST_PER_SCREEN && !$is_last) {

                        if (getPostCard("$board_name/$last_id", "post.php?n=$board_name/$last_id")) {
                            $shown_count++;
                        }
                        $last_id--;

                        if ($last_id == 0) {
                            $is_last = true;
                        }
                    }

                }
                ?>
                <?php if (!$is_last || $pager != 0) { ?>
                    <div class="pager">
                        <a href="board.php?n=<?php echo $board_name."&p=".($pager-1)?>" class="button flat icon <?php if ($pager == 0) echo "disabled" ?>"><span class="material-symbols-rounded">chevron_left</span></a>
                        <span><?php echo $pager+1; ?>. oldal</span>
                        <a href="board.php?n=<?php echo $board_name."&p=".($pager+1)?>" class="button flat icon <?php if ($is_last) echo "disabled" ?>"><span class="material-symbols-rounded">chevron_right</span></a>
                    </div>
                <?php } else {
                    echo "<p class='disabled centered'>Elérted az üzenőfal végét.</p>";
                } ?>
            </div>
        </section>
    </div>
</main>
</body>
</html>