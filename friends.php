<?php session_start(); include "api/users.php"; include "api/acl.php"?>
<!DOCTYPE html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Barátok - UwUChan</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/mobile.css">
        <link rel="stylesheet" href="css/messages.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php include "views/sidebar.php"?>
                <section>
                    <div class="section-head">
                        <h1>Barátok</h1>
                        <div class="tab-bar compact">
                            <a class="button hide-text-on-mobile" href="messages.php"><span class="material-symbols-rounded">chat</span><span>Üzenetek</span></a>
                            <a class="button hide-text-on-mobile active"><span class="material-symbols-rounded">group</span><span>Barátok</span></a>
                        </div>
                    </div>

                    <?php
                    $file_path = 'data/users/'.$_SESSION["user"].'/friends.json';
                    $default_profile_picture = 'img/default_user_avatar.png';

                    if (file_exists($file_path)) {
                        $json_tomb = json_decode(file_get_contents($file_path), true);

                        $baratok = [];
                        $baratkerelmek = [];
                        foreach ($json_tomb as $barat) {
                            if ($barat['relationship'] == 1) {
                                $baratok[] = $barat;
                            } elseif ($barat['relationship'] == 0) {
                                $baratkerelmek[] = $barat;
                            }
                        }
                    }
                    ?>

                    <p class="card-header">Beérkezett barátkérelmek</p>
                    <div class="list">
                        <?php
                        if (!empty($baratkerelmek)) {
                            foreach ($baratkerelmek as $barat) {
                                $profilkep = isset($barat['profile_picture']) ? $barat['profile_picture'] : $default_profile_picture;
                                $felhasznalonev = $barat['username'];
                                $relationship = isset($barat['relationship']) ? $barat['relationship'] : 0;

                                if ($relationship == 2) {
                                    echo "<div class=\"messages-card-head\" style=\"color: gray;\">
                        <img class=\"user-profile-messages-avatar\" src=\"$profilkep\" alt=\"Profilkép\">
                        <div class=\"messages-card-preview\">
                            <span>$felhasznalonev - LETILTVA</span>
                        </div>
                      </div>";
                                } else {

                                    echo "<div class=\"messages-card-head\">
                        <a href=\"profile-other.php\">
                        <img class=\"user-profile-messages-avatar\" src=\"$profilkep\" alt=\"Profilkép\">
                        </a>
                      <div class=\"messages-card-preview\">
                        <span>$felhasznalonev</span>
                      </div>
                    <form method=\"post\" action=\"friends.php\">
                        <input type=\"hidden\" name=\"friend_username\" value=\"$felhasznalonev\">
                        <input type=\"hidden\" name=\"action\" value=\"accept\">
                        <button type=\"submit\" title=\"Elfogadás\" class=\"flat icon right\"><span class=\"material-symbols-rounded\">done</span></button>
                    </form>
                
                    <form method=\"post\" action=\"friends.php\">
                        <input type=\"hidden\" name=\"friend_username\" value=\"$felhasznalonev\">
                        <input type=\"hidden\" name=\"action\" value=\"remove\">
                        <button type=\"submit\" title=\"Elutasítás\" class=\"flat icon\"><span class=\"material-symbols-rounded\">close</span></button>
                    </form>
                
                    <form method=\"post\" action=\"friends.php\">
                        <input type=\"hidden\" name=\"friend_username\" value=\"$felhasznalonev\">
                        <input type=\"hidden\" name=\"action\" value=\"block\">
                        <button type=\"submit\" title=\"Letiltás\" class=\"flat icon\"><span class=\"material-symbols-rounded\">block</span></button>
                    </form>
                    </div>";
                                }
                            }
                        } else {
                            echo "Nincs függőben lévő barátkérelmed.";
                        }
                        ?>


                    </div>
                    <p class="card-header">Barátaim</p>
                    <div class="list">

                        <?php
                        if (!empty($baratok)) {
                            foreach ($baratok as $barat) {
                                $profilkep = isset($barat['profile_picture']) ? $barat['profile_picture'] : $default_profile_picture;
                                $felhasznalonev = $barat['username'];
                                $relationship = $barat['relationship'];
                                $style = ($relationship == 2) ? 'style="color: gray;"' : '';

                                echo "<div class=\"messages-card-head\" $style>
                <a href=\"\">
                <img class=\"user-profile-messages-avatar\" src=\"$profilkep\" alt=\"Profilkép\">
                </a>
            
                <div class=\"messages-card-preview\">
                    <span>$felhasznalonev</span>
                </div>
        
                <form method=\"post\" action=\"friends.php\">
                    <input type=\"hidden\" name=\"friend_username\" value=\"$felhasznalonev\">
                    <input type=\"hidden\" name=\"action\" value=\"remove\">
                    <button type=\"submit\" title=\"Barát eltávolítása\" class=\"flat icon right\"><span class=\"material-symbols-rounded\">close</span></button>
                </form>
        
                <form method=\"post\" action=\"friends.php\">
                    <input type=\"hidden\" name=\"friend_username\" value=\"$felhasznalonev\">
                    <input type=\"hidden\" name=\"action\" value=\"block\">
                    <button type=\"submit\" title=\"Letiltás\" class=\"flat icon\"><span class=\"material-symbols-rounded\">block</span></button>
                </form>
            </div>";
                            }
                        } else {
                            echo "<p>Nincs egy barátod sem.</p>";
                        }
                        ?>



                        <?php
                        session_start();

                        if (!isset($_SESSION["user"])) {
                            die("Nincs bejelentkezve felhasználó.");
                        }

                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            if (isset($_POST["action"]) && isset($_POST["friend_username"])) {
                                $action = $_POST["action"];
                                $friend_username = $_POST["friend_username"];
                                $file_path = 'data/users/' . $_SESSION["user"] . '/friends.json';
                                $sender_file_path = 'data/users/' . $friend_username . '/friends.json';

                                $friend_exists = false;
                                $user_json = [];

                                if (file_exists($file_path)) {
                                    $user_json = json_decode(file_get_contents($file_path), true);

                                    foreach ($user_json as $key => &$friend) {
                                        if ($friend["username"] === $friend_username) {
                                            $friend_exists = true;

                                            if ($friend["relationship"] === 2) {
                                                die("Nem küldhetsz barátkérést, mert ez a felhasználó már letiltott téged.");
                                            }

                                            if ($action === "accept") {
                                                $friend["relationship"] = 1;
                                                $friend["seen_last_reaction"] = true;

                                                if (file_exists($sender_file_path)) {
                                                    $sender_json = json_decode(file_get_contents($sender_file_path), true);
                                                    $sender_json[] = [
                                                        "username" => $_SESSION["user"],
                                                        "relationship" => 1,
                                                        "thread" => "",
                                                        "seen_last_reaction" => true
                                                    ];

                                                    $uuid = generateUUID();
                                                    $thread_file_path = 'data/threads/' . $uuid . '.json';
                                                    file_put_contents($thread_file_path, json_encode([]));

                                                    $friend["thread"] = $uuid;
                                                    foreach ($sender_json as &$sender_friend) {
                                                        if ($sender_friend["username"] === $_SESSION["user"]) {
                                                            $sender_friend["thread"] = $uuid;
                                                            break;
                                                        }
                                                    }

                                                    file_put_contents($sender_file_path, json_encode($sender_json, JSON_PRETTY_PRINT));
                                                }
                                            }

                                            if ($action === "remove") {
                                                unset($user_json[$key]);

                                                if (file_exists($sender_file_path)) {
                                                    $sender_json = json_decode(file_get_contents($sender_file_path), true);
                                                    foreach ($sender_json as $sender_key => $sender_friend) {
                                                        if ($sender_friend["username"] === $_SESSION["user"]) {
                                                            unset($sender_json[$sender_key]);

                                                            $thread_id = $sender_friend["thread"];
                                                            $thread_file_path = 'data/threads/' . $thread_id . '.json';
                                                            if (file_exists($thread_file_path)) {
                                                                unlink($thread_file_path);
                                                            }
                                                            break;
                                                        }
                                                    }
                                                    file_put_contents($sender_file_path, json_encode(array_values($sender_json), JSON_PRETTY_PRINT));
                                                }
                                            }

                                            if ($action === "block") {
                                                $friend["relationship"] = 2;
                                            }
                                            break;
                                        }
                                    }
                                    file_put_contents($file_path, json_encode(array_values($user_json), JSON_PRETTY_PRINT));
                                } else {
                                    die("Felhasználó barátjainak listája nem található.");
                                }
                            } else {
                                die("Hiányzó adatok a kérésben.");
                            }
                        }

                        function generateUUID() {
                            return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                                mt_rand(0, 0xffff),
                                mt_rand(0, 0x0fff) | 0x4000,
                                mt_rand(0, 0x3fff) | 0x8000,
                                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
                            );
                        }
                        ?>

                    </div>
                </section>
            </div>
        </main>
    </body>
</html>