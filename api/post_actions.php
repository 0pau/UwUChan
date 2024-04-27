<?php

/**
<p><b>API végpont: </b><i>HTML formból, vagy JavaScriptből hívható</i></p>
<h1>Post actions</h1>
Egy posztra reagálás like vagy dislike formájában. lásd: users.php/interactWithPost
 */

    session_start();

    include "users.php";

    if (!isset($_SESSION["user"])) {
        header("Location: ../login.php");
    }

    if (!isset($_POST["action"]) || !isset($_POST["data"])) {
        die("Hibás kérés");
    }

    interactWithPost($_POST["data"],$_POST["action"], "..");

    $referer = $_SERVER["HTTP_REFERER"];
    header("Location: " . $referer);