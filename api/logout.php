<?php

/**
<h1>Kijelentkeztető szkript</h1>
Kijelentkezteti a felhasználót. Törli a munkamenetet és átirányít a főoldalra.
 */

session_start();
$_SESSION["user"] = "";
session_destroy();
header("Location: ../index.php");