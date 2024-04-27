<?php

/**
 <p><b>API végpont: </b><i>HTML formból, vagy JavaScriptből hívható</i></p>
 <h1>Modify settings</h1>
 Egy felhasználó profilbeállításait módosítja.
 */

    session_start();

    if (!isset($_SESSION["user"])) {
        header("Location: ../403.html");
        exit;
    }
    include "users.php";

    $referer = $_SERVER["HTTP_REFERER"];
    if (isset($_POST["extreme-debug-setting"])) {
        if (isset($_POST["extreme_debug_mode"])) {
            $_SESSION["extreme_debug_mode"] = true;
        } else {
            unset($_SESSION["extreme_debug_mode"]);
        }
        header("Location: " . $referer);
        exit();
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