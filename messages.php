<?php session_start(); include "api/users.php"; include "api/acl.php"?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kapcsolatok - UwUChan</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/mobile.css">
    <link rel="stylesheet" href="css/messages.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body class="<?php include "api/theme.php"?>">
<main>
    <?php include "views/header.php" ?>
    <div class="main-flex">
        <?php  include "views/sidebar.php"?>
        <section>
            <div class="section-head">
                <h1>Üzenetek</h1>
                <div class="tab-bar compact">
                    <a class="button hide-text-on-mobile active"><span class="material-symbols-rounded">chat</span><span>Üzenetek</span></a>
                    <a class="button hide-text-on-mobile" href="friends.php"><span class="material-symbols-rounded">group</span><span>Barátok</span></a>
                </div>
            </div>
            <div class="list">

                <?php
                session_start();

                $file_path = 'data/users/' . $_SESSION["user"] . '/friends.json';
                $default_profile_picture = 'img/default_user_avatar.png';
                $current_user = $_SESSION["user"];
                $message_count = 0;

                if (file_exists($file_path)) {
                    $json_tomb = json_decode(file_get_contents($file_path), true);

                    foreach ($json_tomb as $barat) {
                        $nev = $barat['username'];
                        if ($barat['relationship'] === 1) {
                            $thread_file_path = 'data/threads/' . $barat['thread'] . '.json';
                            if (file_exists($thread_file_path)) {
                                $thread_messages = json_decode(file_get_contents($thread_file_path), true);

                                usort($thread_messages, function($a, $b) {
                                    return $b['id'] - $a['id'];
                                });

                                $last_message = reset($thread_messages);
                                $sender_prefix = ($last_message['username'] === $current_user) ? '[Te]: ' : '';

                                echo "<div class='messages-card-head'>
                    <a href='profile-other.php?username=$nev'>
                    <img class='user-profile-messages-avatar' src='$default_profile_picture' alt='Profilkép'>
                    </a>
                    
                    <a href='thread.php?username=$nev' class='messages-card-preview'>
                    <span>" . $barat['username'] . "</span>
                    <p>" . $sender_prefix . $last_message['text'] . "</p>
                    </a>
                    <span class='time-since-last'>2 perce</span>
                    </div>";

                                $message_count++;
                            }
                        }
                    }

                    if ($message_count === 0) {
                        echo "<p>Nincs egy üzeneted sem</p>";
                    }
                }
                ?>




        </section>
    </div>
</main>
</body>
</html>