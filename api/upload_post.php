<?php

    session_start();
    include_once "users.php";

    include "boards.php";
    include "util.php";

    if (!isset($_SESSION["user"])) {
        die("Nincs hozzáférésed ehhez a végponthoz, sorry :3");
    }

    if (!isset($_POST["board-name"]) || !isset($_POST["post-title"]) || !isset($_POST["post-body"])) {
        die("Hiba történt.");
    }

    $board = $_POST["board-name"];
    $title = trim($_POST["post-title"]);
    $body = trim($_POST["post-body"]);

    if (!boardExists($board, "..")) {
        die("A megadott üzenőfal nem létezik.");
    }

    $words = explode(" ", $body);

    $body = "";
    foreach ($words as $word) {
        if (str_starts_with($word, "http://")||str_starts_with($word, "https://")||str_starts_with($word, "ftp://")) {
            $word = "<a href=\"$word\">$word</a>";
        }
        $body = $body." ".$word;
    }

    $post = new stdClass();
    $post->title = $title;
    $post->body = $body;
    $post->author = $_SESSION["user"];
    $post->likes = 0;
    $post->dislikes = 0;
    $post->posted_at = time();

    $images = [];
    if (count($_FILES["images"]["name"]) != 0) {
        for ($i = 0; $i < count($_FILES["images"]["name"]); $i++) {

            if ($_FILES["images"]["name"][$i] == "") {
                continue;
            }

            $img = new stdClass();
            $img->title = $_FILES["images"]["name"][$i];
            $fileSeparated = explode(".", $img->title);
            $ext = end($fileSeparated);

            $filenames = saveImageWithThumbnail($ext, $_FILES["images"]["tmp_name"][$i], "..");
            $img->original = $filenames[0];
            $img->thumbnail = $filenames[1];

            $images[] = $img;
        }
    }

    $post->images = $images;
    $post->comments = [];

    $data = json_encode($post, JSON_UNESCAPED_UNICODE);

    $boardInfo = getBoardInfo($board, "..");
    $number = $boardInfo->post_count + 1;
    $boardInfo->post_count = $number;

    saveBoardInfo($board, $boardInfo, "..");
    savePostToUser($_SESSION["user"], "$board/$number", "..");

    changeUserField("uwuness", getUserField("uwuness", "..")+1, "..");

    file_put_contents("../data/boards/$board/$number.json", $data);

    header("Location: ../post.php?n=$board/$number");