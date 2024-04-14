<?php
    session_start();
    include "users.php";
    if (!isset($_SESSION["user"])) {
        header("HTTP/1.1 403 Forbidden");
        die("Ehhez a végponthoz nincs hozzáférésed, sorry :3");
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
    }

    $referer = $_SERVER["HTTP_REFERER"];
    header("Location: $referer");

    function toggleFollow() {
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