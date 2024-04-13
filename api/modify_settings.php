<?php
    include "users.php";
    session_start();

    if (!isset($_SESSION["user"])) {
        header("HTTP/1.1 403 Forbidden");
        echo "Ehhez a végponthoz nincs hozzáférésed, sorry :3";
    }

    if (isset($_POST["darkmode"])) {
        changeUserField("isUsingDarkMode", true, "..");
    } else {
        changeUserField("isUsingDarkMode", false, "..");
    }
    if (isset($_POST["cute_cursor"])) {
        changeUserField("isUsingCuteCursor", true, "..");
    } else {
        changeUserField("isUsingCuteCursor", false, "..");
    }
    if (isset($_POST["show_nsfw"])) {
        changeUserField("isNSFWAllowed", true, "..");
    } else {
        changeUserField("isNSFWAllowed", false, "..");
    }

    $referer = $_SERVER["HTTP_REFERER"];
    header("Location: ".$referer);