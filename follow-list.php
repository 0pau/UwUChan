<?php
    session_start();
    include "api/users.php";
    include "api/acl.php";
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
                        <h1>Követett üzenőfalak</h1>
                    </div>
                    <div class="list">
                        <?php
                            $followedBoards = getFollowedBoards();
                            foreach ($followedBoards as $board) {
                                $boardIcon = getBoardIcon($board->name);
                                echo "<a class='board-list-item' href='board.php?n=$board->name'>
                                    <img src='$boardIcon' alt='$board->name ikonja'>
                                    $board->name
                                    </a>";
                            }

                        ?>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>