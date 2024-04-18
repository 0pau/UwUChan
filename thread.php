<?php session_start(); include "api/users.php"; include "api/acl.php"?>
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
        <link rel="stylesheet" href="css/thread.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php include "views/sidebar.php" ?>
                <section class="no-padding thread-view">
                    <div class="thread-toolbar top">
                        <a title="Vissza az üzenetekre" class="button icon flat" href="messages.php"><span class="material-symbols-rounded">arrow_back</span></a>

                        <?php
                        session_start();
                        $active_user = isset($_SESSION["user"]) ? $_SESSION["user"] : "";
                        $friends_file = "data/users/$active_user/friends.json";
                        $current_friend = $_GET['username'];

                        if(file_exists($friends_file)) {
                            $friends_json = file_get_contents($friends_file);
                            $friends_data = json_decode($friends_json, true);

                                if($current_friend) {
                                    echo "<span class='user-name'>$current_friend</span>";
                                    if(!isset($_GET['username'])) {
                                        $_GET['username'] = $current_friend;
                                        $redirect_url = http_build_query($_GET);
                                        header("Location: thread.php?$redirect_url");

                                        exit;
                                    }
                                } else {
                                    echo "<p>Nincs olyan barát, akivel a felhasználó beszélget.</p>";
                                }
                            } else {
                                echo "<p>Nincs adat a friends.json fájlban.</p>";
                            }

                        ?>



                        <button title="Felhasználó titltása" class="right flat icon"><span class="material-symbols-rounded">block</span></button>
                    </div>
                    <div class="thread">

                        <?php
                        session_start();
                        $active_user = isset($_SESSION["user"]) ? $_SESSION["user"] : "";
                        $friend_username = $_GET['username'];

                        if ($active_user && $friend_username) {
                            $thread_id = "62346236236236";

                            $thread_file = "data/threads/{$thread_id}.json";

                            if (file_exists($thread_file)) {
                                $thread_json = file_get_contents($thread_file);
                                $thread_data = json_decode($thread_json, true);

                                if ($thread_data) {
                                    foreach ($thread_data as $message) {
                                        $text = $message['text'];
                                        $username = $message['username'];
                                        $timestamp = date('H:i', $message['posted-at']);


                                        $message_css_class = ($username === $active_user) ? 'mine' : 'away';
                                        echo "<div class='message $message_css_class'>";
                                        echo "<span class='bubble'>$text</span>";
                                        echo "<span class='sent-at'>$timestamp</span>";

                                        if ($username === $active_user) {
                                            echo "<div class='options'>";
                                            echo "<button title='Üzenet törlése' class='delete-message flat round icon vsmall'><span class='material-symbols-rounded'>delete</span></button>";
                                            echo "<button title='Válasz' class='reply-message flat round icon vsmall'><span class='material-symbols-rounded'>reply</span></button>";
                                            echo "</div>";
                                        } else {

                                            echo "<div class='options'>";
                                            echo "<button title='Válasz' class='reply-message flat round icon vsmall'><span class='material-symbols-rounded'>reply</span></button>";
                                            echo "</div>";
                                        }

                                        echo "</div>";
                                    }
                                } else {
                                    echo "<p>Nincs üzenet a beszélgetésben.</p>";
                                }
                            } else {
                                echo "<p>Nem található a fájl.</p>";
                            }
                        } else {
                            echo "<p>Nem sikerült meghatározni az aktív felhasználót vagy a beszélgetés résztvevőjét.</p>";
                        }
                        ?>

                    </div>
                    <div class="thread-toolbar bottom my-comment-bar">
                        <input type="text" placeholder="Ide írd az üzenetet" id="message" required><button class="flat"><span class="material-symbols-rounded">send</span></button>
                    </div>


                </section>
            </div>
        </main>
    </body>
</html>