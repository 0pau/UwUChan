<?php
    session_start();
    include "api/users.php";
    include "api/acl.php";

    if (!isset($_GET["username"]) || !trim($_GET["username"]) || !userExists($_GET["username"])) {
        include "404.html";
        die();
    }

    $friend = trim($_GET["username"]);
    $friends_file = "data/users/".$_SESSION["user"]."/friends.json";

    $relationship_status = -1;

    if ($friend == "SYSTEM") {
        $relationship_status = 2;
    } else {
        $relationship_status = getRelationship($friend);
    }


    $active_user = $_SESSION["user"];
    $friend_username = $_GET['username'];
    $friendship_file = "data/users/{$active_user}/friends.json";

    $friends_data = json_decode(file_get_contents($friendship_file), true);
    $thread_id = "";

    for ($i = 0; $i < count($friends_data); $i++) {
        if ($friends_data[$i]['username'] == $friend_username) {
            $friends_data[$i]['seenLastInteraction'] = true;
            break;
        }
    }
    file_put_contents($friendship_file, json_encode($friends_data, JSON_PRETTY_PRINT));

?>
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
    <script>
        function scrollToLast() {
            let message = document.querySelector('.message:last-of-type');
            message.scrollIntoView(false);
        }
    </script>
</head>
<body class="<?php include "api/theme.php"?>" onload="scrollToLast()">
<main>
    <?php include "views/header.php" ?>
    <div class="main-flex">
        <?php include "views/sidebar.php" ?>
        <section class="no-padding thread-view">
            <div class="thread-toolbar top">
                <a title="Vissza az üzenetekre" class="button icon flat" href="messages.php"><span class="material-symbols-rounded">arrow_back</span></a>

                <?php
                    $friends_json = file_get_contents($friends_file);
                    $friends_data = json_decode($friends_json, true);

                    if($friend) {
                        echo "<span class='user-name'>$friend</span>";
                    } else {
                        echo "<p>Nincs olyan barát, akivel a felhasználó beszélget.</p>";
                    }
                ?>

                <?php if ($friend != "SYSTEM" && $relationship_status != 2) { ?>
                <form method='post' action='api/user_actions.php'>
                    <input type='hidden' name='action' value='blockUser'>
                    <input type='hidden' name='username' value='<?php echo $friend ?>'>
                    <button type='submit' title='Felhasználó tiltása' class='right flat icon'>
                    <span class='material-symbols-rounded'>block</span></button>
                </form>
                <?php } else if ($friend != "SYSTEM" && $relationship_status == 2) { ?>
                    <form method='post' action='api/user_actions.php'>
                        <input type='hidden' name='action' value='unblockUser'>
                        <input type='hidden' name='username' value='<?php echo $friend ?>'>
                        <button type='submit' title='Tiltás visszavonása' class='right flat icon'>
                            <span class='material-symbols-rounded'>undo</span></button>
                    </form>
                <?php } ?>
            </div>
            <div class="thread">
                <?php

                foreach ($friends_data as &$friend) {
                    if ($friend['username'] === $friend_username) {
                        $thread_id = $friend['thread'];

                        if ($_SERVER["REQUEST_METHOD"] != "POST") {
                            $friend['seen_last_reaction'] = true;
                        }
                        break;
                    }
                }
                $thread_file = "data/threads/{$thread_id}.json";
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

                function processNewMessage(&$thread_data, $thread_file, $active_user) {
                    global $friend;
                    $new_message = array(
                        "id" => end($thread_data)['id'] + 1,
                        "username" => $active_user,
                        "text" => $_POST['message'],
                        "posted-at" => time(),
                        "unsent" => false
                    );
                    $thread_data[] = $new_message;
                    file_put_contents($thread_file, json_encode($thread_data, JSON_PRETTY_PRINT));

                    $firend_file = "data/users/".$friend["username"]."/friends.json";
                    $friend_data = json_decode(file_get_contents($firend_file), true);
                    for ($i = 0; $i < count($friend_data); $i++) {
                        if ($friend_data[$i]['username'] == $_SESSION["user"]) {
                            $friend_data[$i]['seenLastInteraction'] = false;
                            $friend_data[$i]['lastInteraction'] = time();
                            break;
                        }
                    }

                    $my_data_file = "data/users/".$_SESSION["user"]."/friends.json";
                    $my_data = json_decode(file_get_contents($my_data_file), true);
                    for ($i = 0; $i < count($my_data); $i++) {
                        if ($my_data[$i]['username'] == $friend["username"]) {
                            $my_data[$i]['lastInteraction'] = time();
                            break;
                        }
                    }

                    file_put_contents($firend_file, json_encode($friend_data, JSON_PRETTY_PRINT));
                    file_put_contents($my_data_file, json_encode($my_data, JSON_PRETTY_PRINT));

                    echo "<meta http-equiv='refresh' content='0'>";
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

            <?php if ($relationship_status == 2) { ?>
                <div class='thread-toolbar bottom my-comment-bar'><p>Erre a beszélgetésre nem válaszolhatsz.</p></div>
            <?php } else { ?>
                <form class='thread-toolbar bottom my-comment-bar' method='post' action='thread.php?username=<?php echo $friend_username?>'>
                    <input type='text' placeholder='Ide írd az üzenetet' id='message' name='message' required>
                    <button type='submit' class='flat'><span class='material-symbols-rounded'>send</span></button>
                </form>
            <?php } ?>

        </section>
    </div>
</main>
</body>
</html>