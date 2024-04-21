<?php

    include_once "api/user.php";

    $page = $_SERVER["REQUEST_URI"];
    $page = explode("/", $page);
    $page = end($page);
    $page = explode("?", $page);
    $page = ($page)[0];
    $page = str_replace(".php", "", $page);

    if ($page == "board") {
        $page = $_GET["n"];
    }

?>
<nav>

    <div>
        <a href="index.php" <?php if ($page == "index") echo "class=\"current\"" ?>><span class="material-symbols-rounded">home</span><span class="nav-item-title">Hírfolyam</span></a>
        <?php if (isset($_SESSION["user"])) { ?>
        <a href="messages.php" <?php if ($page == "messages" || $page == "friends" || $page == "thread") echo "class=\"current\"" ?>>
            <span class="material-symbols-rounded">3p</span>
            <span class="nav-item-title">Üzenetek és barátok</span>
            <?php if (getUnreadCount() != 0) { ?>
                <span class="unread-badge"><?php echo getUnreadCount(); ?></span>
            <?php } ?>
        </a>
        <?php } ?>
        <?php if (isset($_SESSION["user"]) && getUserField("privilege") == 1) { ?>
        <a href="admincenter.php" <?php if ($page == "admincenter") echo "class=\"current\"" ?>><span class="material-symbols-rounded">build</span><span class="nav-item-title">Admin Központ</span></a>
        <?php } ?>
    </div>
    <div class="followed-boards">
        <?php if (getFollowedCount() != 0) { ?>
        <div class="followed-list">
            <p class="nav-header">Követett üzenőfalak</p>
            <?php
                include_once "api/boards.php";
                $boards = getMostRankedBoards();
                $c = 0;
                foreach ($boards as $board) {
                    $icon = getBoardIcon($board->name);

                    $selected = "";
                    if ($page == $board->name) {
                        $selected = "class=\"current\"";
                    }

                    echo "<a $selected href=\"board.php?n=$board->name\"><img alt=\"$board->name\" src=\"$icon\"><span class=\"nav-item-title\">$board->name</span></a>";
                    if ($c == 2) {
                        break;
                    }
                    $c++;
                }
            ?>
            <a href="follow-list.php" id="show-all-followed">Összes megjelenítése</a>
        </div>
        <?php } ?>
    </div>
    <a href="follow-list.php" class="mobile <?php if ($page == "follow-list") echo "current" ?>"><span class="material-symbols-rounded">dashboard</span><span class="nav-item-title">Követett üzenőfalak</span></a>
    <div>
        <p class="nav-header">Információk, visszajelzés</p>
        <a href="help.php" <?php if ($page == "help") echo "class=\"current\"" ?>><span class="material-symbols-rounded">help</span><span class="nav-item-title">Tudakozó</span></a>
    </div>
</nav>