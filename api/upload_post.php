<?php

    session_start();

    include "boards.php";

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
    $post->images = [];
    $post->comments = [];

    $data = json_encode($post, JSON_UNESCAPED_UNICODE);

    $boardInfo = getBoardInfo($board, "..");
    $number = $boardInfo->post_count + 1;
    $boardInfo->post_count = $number;

    saveBoardInfo($board, $boardInfo, "..");

    file_put_contents("../data/boards/$board/$number.json", $data);

    //TODO: Redirect user to the new post:
    header("Location: ../post.php?n=$board/$number");