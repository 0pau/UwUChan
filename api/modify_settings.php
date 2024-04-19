<?php
    session_start();

    if (!isset($_SESSION["user"])) {
        header("HTTP/1.1 403 Forbidden");
        die("Ehhez a végponthoz nincs hozzáférésed, sorry :3");
    }
    include "users.php";

    if (isset($_POST["extreme-debug-setting"])) {
        if (isset($_POST["extreme_debug_mode"])) {
            $_SESSION["extreme_debug_mode"] = true;
        } else {
            unset($_SESSION["extreme_debug_mode"]);
        }
    }

    $referer = $_SERVER["HTTP_REFERER"];

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
        changeUserField("filterNSFW", true, "..");
    } else {
        changeUserField("filterNSFW", false, "..");
    }
    if (isset($_POST["boards"])) {
        $boardlist = $_POST["boards"];
        foreach ($boardlist as $board) {
            followBoard($board, "..");
        }
    }

    if (str_contains($referer, "onboarding.php")) {
        $stage = intval($_POST["stage"]) + 1;
        header("Location: ../onboarding.php?stage=$stage");
    } else {
        header("Location: " . $referer);
    }