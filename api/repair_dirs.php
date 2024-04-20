<?php

    session_start();
    if (!isset($_SESSION["user"]) || getUserField("privilege") < 1) {
        header("Location: ../403.html");
        exit;
    }

    if (!is_dir("../data")) {
        mkdir("../data");
    }
    if (!is_dir("../data/users")) {
        mkdir("../data/users");
    }
    if (!is_dir("../data/boards")) {
        mkdir("../data/boards");
    }
    if (!is_dir("../data/threads")) {
        mkdir("../data/threads");
    }
    if (!is_dir("../data/reports")) {
        mkdir("../data/reports");
    }
    if (!is_dir("../data/requests")) {
        mkdir("../data/requests");
    }
    if (!is_dir("../data/images")) {
        mkdir("../data/images");
    }

    $referer = $_SERVER["HTTP_REFERER"];
    header("Location: $referer");