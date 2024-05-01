<?php

/**
* <p><b>API végpont: </b><i>HTML formból, vagy JavaScriptből hívható</i></p>
* <h1>User actions</h1>
* Egy, a felhasználó által triggerelt akciót indít el, ami a következők egyike lehet:
* <p>
* toggle-follow: Üzenőfal követési állapotának megváltoztatása (ha követi, akkor kiköveti és vice versa)<br>
* delete: Fióktörlés kezdeményezése<br>
* sendFriendRequest: Barátkérelmet küld egy másik felhasználónak<br>
* removeFriend: Eltávolít egy barátot<br>
* acceptRequest: Elfogad egy barátkérelmet<br>
* blockUser: Letilt egy másik felhasználót<br>
* unblockUser: Feloldja a tiltást</p>
* <p><b>Megjegyzés: </b>Bizonyos akciók plusz POST-paramétereket igényelnek (pl. a barátkérelemhez kell egy felhasználónév
is, ami a barátnak jelölendő ember felhasználóneve.</p>
<p><b>Megjegyzés (2): </b>Minden akció végrehajtása után visszairányítjuk a felhasználót arra az oldalra, ahonnan jött, így
megteremtve a fluid működés illúzióját. (Kivéve az adattörlés esetében)
</p>
 */

    session_start();
    include "users.php";
    include "posts.php";
    include "comments.php";
    if (!isset($_SESSION["user"])) {
        header("Location: ../403.html");
        exit;
    }

    if (!isset($_POST["action"])) {
        header("HTTP/1.1 400 Bad Request");
        die("Hibás kérés!");
    }

    $redirect_to_referer = false;

    switch ($_POST["action"]) {
        case "toggle-follow": {
            toggleFollow();
            $redirect_to_referer = true;
            break;
        }
        case "delete": {
            deleteEverything();
            break;
        }
        case "sendFriendRequest": {
            sendFriendRequest($_POST["username"],"..");
            $redirect_to_referer = true;
            break;
        }
        case "removeFriend": {
            removeFriend($_POST["username"],"..");
            $redirect_to_referer = true;
            break;
        }
        case "acceptRequest": {
            acceptFriendRequest($_POST["username"],"..");
            $redirect_to_referer = true;
            break;
        }
        case "blockUser": {
            blockUser($_POST["username"], false,"..");
            $redirect_to_referer = true;
            break;
        }
        case "unblockUser": {
            blockUser($_POST["username"],true,"..");
            $redirect_to_referer = true;
            break;
        }
    }

    if ($redirect_to_referer) {
        $referer = $_SERVER["HTTP_REFERER"];
        header("Location: $referer");
    }

    function toggleFollow(): void {
        if (!isset($_POST["board"])) {
            header("HTTP/1.1 400 Bad Request");
            echo "Hibás kérés!";
        }

        $board_name = $_POST["board"];
        if (isBoardFollowed($board_name, "..")) {
            unfollowBoard($board_name, "..");
        } else {
            followBoard($board_name, "..");
        }
    }

/**
 * Végrehajtja a felhasználó fiókjának törlését. A törlés a következő lépésekből áll:
 * 1. Töröljük a profilképet, ha van
 * 2. Levesszük a reakciókat azokról a posztokról, amikre a felhasználó reagált
 * 3. Töröljük a felhasználó által létrehozott posztokat
 * 4. Töröljük a felhasználó kommentjeit
 * 5. Töröljük a barátainak a barátlistájából
 * 6. Végül az összes metaadatot is eltávolítjuk és kijelentkeztetjük.
 * <p><b>BUG</b> (hivatalosan nem tesztelve)<br>
 *  Ha nincs telepítve a GD könyvtár és a posztok képeinél az original lett a thumbnail is, akkor a rendszer azt
 *  2x próbálja majd kitörölni, ami nem lehetséges!
 *  <ul><li>Verzió: v1.0-20240421</li></ul></p>
 * @return void
 */
    function deleteEverything(): void {

        echo "Felkészülés...<br>";
        sleep(1);

        $start = time();

        if (getUserField("profilePictureFilename") != "") {
            echo "-> Profilkép<br>";
            unlink("../data/images/".getUserField("profilePictureFilename"));
        }

        echo "-> Poszt interakciók<br>";
        $likedPosts = getUserField("liked_posts", "..");
        $dislikedPosts = getUserField("disliked_posts", "..");
        foreach ($likedPosts as $post) {
            interactWithPost($post, "like", "..");
        }
        foreach ($dislikedPosts as $post) {
            interactWithPost($post, "dislike", "..");
        }

        echo "-> Posztok<br>";
        $posts = getUserPosts($_SESSION["user"], "..");

        foreach ($posts as $post) {
            deletePost($post, "..");
        }

        echo "-> Kommentek<br>";
        $comments = getUserComments($_SESSION["user"], "..");

        foreach ($comments as $comment) {
            $t = explode("@", $comment);
            $w = $t[0];
            echo $w."<br>";
            if (!file_exists("../data/boards/$w.json")) {
                continue;
            }
            $commentList = getComments($w, "..");
            for ($i = 0; $i < count($commentList); $i++) {
                purgeComments($_SESSION["user"], $commentList[$i]);
            }
            saveComments($w, $commentList, "..");
        }

        echo "-> Kapcsolatok<br>";
        $friends_file = "../data/users/".$_SESSION["user"]."/friends.json";
        $friends = json_decode(file_get_contents($friends_file), false);
        foreach ($friends as $friend) {
            if ($friend->username != "SYSTEM") {
                removeFriend($friend->username, "..");
            } else {
                unlink("../data/threads/" . $friend->thread . ".json");
            }
        }

        echo "-> Metaadatok<br>";
        unlink("../data/users/".$_SESSION["user"]."/followed_boards.json");
        unlink("../data/users/".$_SESSION["user"]."/friends.json");
        unlink("../data/users/".$_SESSION["user"]."/metadata.json");
        unlink("../data/users/".$_SESSION["user"]."/owned_boards.json");
        unlink("../data/users/".$_SESSION["user"]."/posts.json");
        unlink("../data/users/".$_SESSION["user"]."/comments.json");
        rmdir("../data/users/".$_SESSION["user"]);

        echo "Kijelentkezés...<br>";
        $_SESSION["user"] = "";
        session_destroy();

        $end = time();

        echo "Az adattörlés ".$end-$start." másodpercig tartott.";

    }

/**
 * Rekurzív metódus, ami a kommentek törlésére szolgál. Hasonlóan működik, mint a postComment metódusból hívott rekurzív
 * metódus.
 * @param $name
 * @param $comment
 * @return void
 */
    function purgeComments(&$name, &$comment) {
        if ($comment->username == $name) {
            $comment->username = "";
            $comment->text = "[törölt]";
        }
        if (!isset($comment->replies)) {
            return;
        }
        for ($i = 0; $i < count($comment->replies); $i++) {
            if ($comment->replies[$i]->username == $name) {
                $comment->replies[$i]->username = "";
                $comment->replies[$i]->text = "[törölt]";
            }
            purgeComments($name, $comment->replies[$i]);
        }
    }