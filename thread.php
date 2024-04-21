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

                <?php
                echo "<form method='post'>";
                echo "<input type='hidden' name='block_user' value='1'>";
                echo "<button type='submit' title='Felhasználó tiltása' class='right flat icon'>";
                echo "<span class='material-symbols-rounded'>block</span></button>";
                echo "</form>";
                ?>
            </div>
            <div class="thread">

                <?php
                session_start();

                if (!isset($_SESSION["user"])) {
                    echo "<p>Nincs bejelentkezve felhasználó.</p>";
                    exit;
                }

                $active_user = $_SESSION["user"];
                if (!isset($_GET['username'])) {
                    echo "<p>Nem sikerült meghatározni a beszélgetés résztvevőjét.</p>";
                    exit;
                }

                $friend_username = $_GET['username'];
                $friendship_file = "data/users/{$active_user}/friends.json";


                if (!file_exists($friendship_file)) {
                    echo "<p>Nem található a felhasználó barátainak listája.</p>";
                    exit;
                }

                $friends_data = json_decode(file_get_contents($friendship_file), true);
                $are_friends = false;
                $thread_id = "";

                foreach ($friends_data as &$friend) {
                    if ($friend['username'] === $friend_username && $friend['relationship'] === 1) {
                        $are_friends = true;
                        $thread_id = $friend['thread'];

                        if ($_SERVER["REQUEST_METHOD"] != "POST") {
                            $friend['seen_last_reaction'] = true;
                        }
                        break;
                    }

                }

                file_put_contents($friendship_file, json_encode($friends_data, JSON_PRETTY_PRINT));

                if (!$are_friends || $thread_id == "") {
                    echo "<p>A két felhasználó nem barátok, vagy nem található a beszélgetés.</p>";
                    exit;
                }

                $thread_file = "data/threads/{$thread_id}.json";
                if (!file_exists($thread_file)) {
                    echo "<p>Nem található a beszélgetés fájlja.</p>";
                    exit;
                }





                $thread_data = json_decode(file_get_contents($thread_file), true);
                usort($thread_data, function ($a, $b) {
                    return $a['posted-at'] - $b['posted-at'];
                });

                echo "<div class='message-container'>";
                foreach ($thread_data as $message) {
                    $username = $message['username'];
                    $timestamp = date('H:i', $message['posted-at']);
                    $message_id = $message['id'];
                    $unsent = $message['unsent'];
                    $message_css_class = ($username === $active_user) ? 'mine' : 'away';

                    echo "<div class='message $message_css_class'>";
                    echo "<span class='bubble'>" . ($unsent ? "Törölt üzenet" : $message['text']) . "</span>";
                    echo "<span class='sent-at'>$timestamp</span>";

                    if ($username === $active_user) {
                        if ($unsent) {
                            echo "<form method='post'>";
                            echo "<input type='hidden' name='restore_message_id' value='$message_id'>";
                            echo "<button type='submit' title='Üzenet visszaállítása' class='restore-message flat round icon vsmall options'><span class='material-symbols-rounded'>undo</span></button>";
                            echo "</form>";
                        } else {
                            echo "<form method='post'>";
                            echo "<input type='hidden' name='delete_message_id' value='$message_id'>";
                            echo "<button type='submit' title='Üzenet törlése' class='delete-message flat round icon vsmall options'><span class='material-symbols-rounded'>delete</span></button>";
                            echo "</form>";
                        }
                    }

                    echo "</div>";
                }
                echo "</div>";

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (isset($_POST['message']) && !empty($_POST['message'])) {
                        processNewMessage($thread_data, $thread_file, $active_user, $thread_id);
                    } elseif (isset($_POST['delete_message_id'])) {
                        handleDeleteMessage($_POST['delete_message_id'], $thread_data, $thread_file, $active_user);
                    } elseif (isset($_POST['restore_message_id'])) {
                        handleRestoreMessage($_POST['restore_message_id'], $thread_data, $thread_file, $active_user);
                    }
                }

                function processNewMessage(&$thread_data, $thread_file, $active_user, $thread_id) {
                    $new_message = array(
                        "id" => end($thread_data)['id'] + 1,
                        "username" => $active_user,
                        "text" => $_POST['message'],
                        "posted-at" => time(),
                        "unsent" => false
                    );
                    $thread_data[] = $new_message;
                    file_put_contents($thread_file, json_encode($thread_data, JSON_PRETTY_PRINT));

                    updateSeenLastReaction($active_user, $thread_id, false);
                    echo "<meta http-equiv='refresh' content='0'>";
                }

                function updateSeenLastReaction($user, $thread_id, $seen) {
                    $file = "data/users/{$user}/friends.json";
                    if (file_exists($file)) {
                        $friends_data = json_decode(file_get_contents($file), true);
                        foreach ($friends_data as &$friend) {
                            if ($friend['thread'] == $thread_id) {
                                $friend['seen_last_reaction'] = $seen;
                                break;
                            }
                        }
                        file_put_contents($file, json_encode($friends_data, JSON_PRETTY_PRINT));
                    }
                }

                function handleDeleteMessage($message_id, &$thread_data, $thread_file, $active_user) {
                    foreach ($thread_data as &$message) {
                        if ($message['id'] == $message_id && $message['username'] == $active_user) {
                            $message['unsent'] = true;
                            $message['original_text'] = $message['text'];
                            $message['text'] = "Törölt üzenet";
                            break;
                        }
                    }
                    file_put_contents($thread_file, json_encode($thread_data, JSON_PRETTY_PRINT));
                    echo "<meta http-equiv='refresh' content='0'>";
                }

                function handleRestoreMessage($message_id, &$thread_data, $thread_file, $active_user) {
                    foreach ($thread_data as &$message) {
                        if ($message['id'] == $message_id && $message['username'] == $active_user && $message['unsent']) {
                            $message['unsent'] = false;
                            $message['text'] = $message['original_text'];
                            unset($message['original_text']);
                            break;
                        }
                    }
                    file_put_contents($thread_file, json_encode($thread_data, JSON_PRETTY_PRINT));
                    echo "<meta http-equiv='refresh' content='0'>";
                }
                ?>


            </div>

            <?php

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['block_user'])) {
                blockUser($friends_data, $friendship_file, $active_user, $friend_username);
            }

            function blockUser(&$friends_data, $friendship_file, $active_user, $blocked_user) {
                foreach ($friends_data as &$friend) {
                    if ($friend['username'] === $blocked_user) {
                        $friend['relationship'] = 2;
                        file_put_contents($friendship_file, json_encode($friends_data, JSON_PRETTY_PRINT));
                        echo "<div class='thread-toolbar bottom my-comment-bar'>";
                        echo "<form method='post' action='thread.php?username=" . "'>";
                        echo "<p>Erre a beszélgetésre nem válaszolhatsz!</p>";
                        echo "</form>";
                        echo "</div>";
                        exit;
                    }
                }
            }


            $relationship_status = 1;

            foreach ($friends_data as $friend) {
                if ($friend['username'] === $friend_username) {
                    $relationship_status = $friend['relationship'];
                    break;
                }
            }


            if ($relationship_status == 2) {
                echo "<p>Erre a beszélgetésre nem válaszolhatsz.</p>";
            } else {
                echo "<div class='thread-toolbar bottom my-comment-bar'>";
                echo "<form method='post' action='thread.php?username=" . htmlspecialchars($friend_username) . "'>";
                echo "<input type='text' placeholder='Ide írd az üzenetet' id='message' name='message' required>";
                echo "<button type='submit' class='flat'><span class='material-symbols-rounded'>send</span></button>";
                echo "</form>";
                echo "</div>";

            }
            ?>

        </section>
    </div>
</main>
</body>
</html>