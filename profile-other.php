<?php
session_start();
include "api/users.php";

if (!isset($_GET["n"])) {
    include "404.html";
    die();
}

if (!userExists($_GET["n"])) {
    include "404.html";
    die();
}

$profileUsername = $_GET["n"];
$posts = getUserPosts($profileUsername);
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
                    <h1><?php echo htmlspecialchars($profileUsername); ?></h1>
                </div>

                <?php
                $current_user = $_SESSION['user'];
                $profile_user = $_GET['n'];

                if ($current_user === $profile_user) {
                    echo "Saját magad nem tudod bejelölni!";
                    exit;
                }

                $current_user_file = "data/users/{$current_user}/friends.json";
                $profile_user_file = "data/users/{$profile_user}/friends.json";
                $relationship_status = null;
                $request_pending = false;

                if (file_exists($current_user_file)) {
                    $friends = json_decode(file_get_contents($current_user_file), true);
                    foreach ($friends as $friend) {
                        if ($friend['username'] === $profile_user) {
                            $relationship_status = $friend['relationship'];
                            break;
                        }
                    }
                }

                if (file_exists($profile_user_file)) {
                    $profile_friends = json_decode(file_get_contents($profile_user_file), true);
                    foreach ($profile_friends as $friend) {
                        if ($friend['username'] === $current_user && $friend['relationship'] === 0) {
                            $request_pending = true;
                            break;
                        }
                    }
                }

                if ($relationship_status === 1) {
                    echo '<form action="friends.php" method="post">
                            <input type="hidden" name="friend_username" value="' . htmlspecialchars($profile_user) . '">
                            <input type="hidden" name="action" value="remove">
                            <button type="submit">Barát eltávolítása</button>
                        </form>
                        <form action="friends.php" method="post">
                            <input type="hidden" name="friend_username" value="' . htmlspecialchars($profile_user) . '">
                            <input type="hidden" name="action" value="block">
                            <button type="submit">Barát blokkolása</button>
                        </form>';
                                } elseif ($relationship_status === 0) {
                                    echo 'Barátkérelem már elküldve.';
                                } elseif ($request_pending) {
                                    echo '<form action="friends.php" method="post">
                            <input type="hidden" name="friend_username" value="' . htmlspecialchars($profile_user) . '">
                            <input type="hidden" name="action" value="accept">
                            <button type="submit">Barátkérelem Elfogadása</button>
                        </form>';
                                } else {
                                    echo '<form action="friends.php" method="post">
                            <input type="hidden" name="friend_username" value="' . htmlspecialchars($profile_user) . '">
                            <input type="hidden" name="action" value="send_request">
                            <button type="submit">Barátkérelem küldése</button>
                        </form>';
                                }
                                ?>


            </div>
            <div class="section-inset">
                <?php if (count($posts) == 0) { ?>
                    <div class="no-comments-placeholder">
                        <span class="material-symbols-rounded">asterisk</span>
                        <p>Ennek a felhasználónak nincsenek posztjai.</p>
                    </div>
                <?php } else {
                    include_once "api/posts.php";
                    foreach ($posts as $post) {
                        getPostCard($post);
                    }
                } ?>
            </div>
        </section>
    </div>
</main>
</body>
</html>