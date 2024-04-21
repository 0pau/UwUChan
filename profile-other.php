<?php
session_start();
include "api/users.php";

if (!isset($_GET["n"]) || trim($_GET["n"]) == "") {
    include "404.html";
    die();
}

if (!userExists($_GET["n"])) {
    include "404.html";
    die();
}

$profileUsername = $_GET["n"];
$posts = [];
if ($profileUsername != "SYSTEM") {
    $posts = getUserPosts($profileUsername);
}

?>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profil - <?php echo htmlspecialchars($profileUsername); ?> - UwUChan</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/mobile.css">
    <link rel="stylesheet" href="css/board.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body class="<?php include "api/theme.php" ?>">
<main>
    <?php include "views/header.php"; ?>
    <div class="main-flex">
        <?php include "views/sidebar.php"; ?>
        <section class="no-padding">
            <div class="board-head">
                <img alt="Profilkép" class="board-backdrop" src="<?php echo getUserProfilePicture($profileUsername); ?>">
                <img alt="Profilkép" class="board-head-image" src="<?php echo getUserProfilePicture($profileUsername); ?>">
                <div class="board-head-details">
                    <h1><?php echo $profileUsername; ?></h1>
                </div>

                <?php

                $current_user = $_SESSION["user"];
                $profile_user = $profileUsername;

                if ($current_user === $profile_user) {
                    echo "Saját magad nem tudod bejelölni!";
                }

                $relationship_status = 999;
                if (isset($_SESSION["user"])) {
                    $relationship_status = getRelationship($profileUsername, true);
                }


                if ($current_user != $profile_user && $profile_user != "SYSTEM") {
                    switch ($relationship_status) {
                        case -2:
                            echo '<form action="api/user_actions.php" method="post"><input type="hidden" name="username" value="' . $profileUsername . '"><input type="hidden" name="action" value="sendFriendRequest"><button type="submit">Barátkérelem küldése</button></form>';
                            break;
                        case -1:
                            echo '<form action="api/user_actions.php" method="post"><input type="hidden" name="username" value="' . $profileUsername . '"><input type="hidden" name="action" value="removeFriend"><button type="submit">Barátkérelem visszavonása</button></form>';
                            break;
                        case 0:
                            echo '<form action="api/user_actions.php" method="post"><input type="hidden" name="username" value="' . $profileUsername . '"><input type="hidden" name="action" value="acceptRequest"><button type="submit">Barátkérelem elfogadása</button></form>';
                            break;
                        case 1:
                            echo '<form action="api/user_actions.php" method="post"><input type="hidden" name="username" value="' . $profile_user . '"><input type="hidden" name="action" value="removeFriend"><button type="submit">Barát eltávolítása</button></form><form action="api/user_actions.php" method="post"><input type="hidden" name="username" value="' . $profile_user . '"><input type="hidden" name="action" value="blockUser"><button type="submit">Letiltás</button></form>';
                            break;
                        case 2:
                            echo '<form action="api/user_actions.php" method="post"><input type="hidden" name="username" value="' . $profileUsername . '"><input type="hidden" name="action" value="unblockUser"><button type="submit">Tiltás visszavonása</button></form>';
                            break;
                    }
                }
                ?>

            </div>
            <div class="section-inset">
                <?php if (count($posts) == 0 && $profileUsername != "SYSTEM") { ?>
                    <div class="no-comments-placeholder">
                        <span class="material-symbols-rounded">asterisk</span>
                        <p>Ennek a felhasználónak nincsenek posztjai.</p>
                    </div>
                <?php } else if ($profileUsername == "SYSTEM") {
                    include "views/easter_egg.html";
                 } else {
                    error_reporting(E_ALL);
                    include_once "api/posts.php";

                    foreach ($posts as $post) {
                        getPostCard($post, "post.php?n=$post");
                    }
                }?>
            </div>
        </section>
    </div>
</main>
</body>
</html>