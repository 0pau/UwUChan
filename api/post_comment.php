<?php

    session_start();

    include "comments.php";

    if (!isset($_SESSION["user"])) {
        die("Nincs hozzáférésed ehhez a végponthoz, sorry :3");
    }

    if (!isset($_POST["text"]) || !isset($_POST["where"]) || !isset($_POST["replyID"])) {
        die("Hibás kérés");
    }

    $text = $_POST["text"];
    $where = $_POST["where"];

    $replyID = null;
    if ($_POST["replyID"] != "") {
        $replyID = $_POST["replyID"];
    }

    postComment($where, $text, $replyID, "..");

    $referer = $_SERVER["HTTP_REFERER"];
    header("Location: $referer");