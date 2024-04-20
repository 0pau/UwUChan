<?php
    session_start();
    include "users.php";
    include "comments.php";
    if (!isset($_SESSION["user"])) {
        header("Location: ../403.html");
        exit;
    }

    if (!isset($_POST["action"])) {
        header("HTTP/1.1 400 Bad Request");
        die("Hibás kérés!");
    }

    switch ($_POST["action"]) {
        case "toggle-follow": {
            toggleFollow();
            break;
        }
        case "delete": {
            deleteEverything();
        }
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
        $referer = $_SERVER["HTTP_REFERER"];
        header("Location: $referer");
    }

    function deleteEverything(): void {
        $start = time();

        echo "Metaadatok törlése<br>";

        if (getUserField("profilePictureFilename") != "") {
            echo " -> Profilkép<br>";
            unlink("../data/images/".getUserField("profilePictureFilename"));
        }

        echo " -> Poszt interakciók<br>";
        $likedPosts = getUserField("liked_posts", "..");
        $dislikedPosts = getUserField("disliked_posts", "..");
        foreach ($likedPosts as $post) {
            interactWithPost($post, "like", "..");
        }
        foreach ($dislikedPosts as $post) {
            interactWithPost($post, "dislike", "..");
        }

        echo " -> Posztok<br>";
        $posts = getUserPosts($_SESSION["user"], "..");

        foreach ($posts as $post) {
            $file = "../data/boards/$post.json";
            $data = file_get_contents($file);
            $data = json_decode($data, false);
            foreach ($data->images as $image) {
                unlink("../data/images/$image->original");
                unlink("../data/images/$image->thumbnail");
            }
            unlink($file);
        }

        echo " -> Kommentek<br>";
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

        echo " -> Metaadatok<br>";
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

    function purgeComments(&$name, &$comment) {
        if ($comment->username == $name) {
            $comment->username = "";
            $comment->text = "[törölt]";
        }
        for ($i = 0; $i < count($comment->replies); $i++) {
            if ($comment->replies[$i]->username == $name) {
                $comment->replies[$i]->username = "";
                $comment->replies[$i]->text = "[törölt]";
            }
            purgeComments($name, $comment->replies[$i]);
        }
    }