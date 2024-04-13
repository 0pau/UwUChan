<?php
    $page = $_SERVER["REQUEST_URI"];
    $page = explode("/", $page);
    $page = end($page);
    $page = explode("?", $page);
    $page = ($page)[0];
    $page = str_replace(".php", "", $page);
?>
<nav>
    <div>
        <a href="index.php" <?php if ($page == "index") echo "class=\"current\"" ?>><span class="material-symbols-rounded">home</span><span class="nav-item-title">Hírfolyam</span></a>
        <a href="messages.php" <?php if ($page == "messages" || $page == "friends") echo "class=\"current\"" ?>><span class="material-symbols-rounded">3p</span><span class="nav-item-title">Üzenetek és barátok</span></a>
        <?php if (isset($_SESSION["user"]) && getUserField("privilege") == 1) { ?>
        <a href="admincenter.php" <?php if ($page == "admincenter") echo "class=\"current\"" ?>><span class="material-symbols-rounded">build</span><span class="nav-item-title">Admin Központ</span></a>
        <?php } ?>
    </div>
    <div class="followed-boards">
        <?php if (getFollowedCount() != 0) { ?>
        <div class="followed-list">
            <p class="nav-header">Követett üzenőfalak</p>
            <!--<a href="board.php"><img alt="macskak" src="img/minta_macsek.jpg"><span class="nav-item-title">macskak</span></a>-->
            <?php
                $boards = getMostRankedBoards();
                $c = 0;
                foreach ($boards as $board) {
                    echo "<a href=\"board.php?n=$board->name\"><img alt=\"$board->name\" src=\"img/default_user_avatar.png\"><span class=\"nav-item-title\">$board->name</span></a>";
                    if ($c == 5) {
                        break;
                    }
                    $c++;
                }
            ?>
        </div>
        <?php } ?>
    </div>
    <div>
        <p class="nav-header">Információk, visszajelzés</p>
        <a href="help/index.html"><span class="material-symbols-rounded">help</span><span class="nav-item-title">Tudakozó</span></a>
    </div>
</nav>