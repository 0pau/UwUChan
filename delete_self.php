<?php session_start(); include "api/users.php"; include "api/acl.php"?>
<?php
    if (isset($_POST["confirmed"]) && $_POST["confirmed"] == 1) {
        echo "A törlést megkezdtük.";
        die();
    }
?>
<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Bejelentkezés</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/login.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="login-background <?php include "api/theme.php"?>">
        <form class="login-window" method="post">
            <input type="hidden" name="confirmed" value="1">
            <header class="login-window-head">
                <div class="color-variants">
                    <img alt="UwUChan-embléma" class="login-window-logo light-variant" src="img/logo.svg">
                    <img alt="UwUChan-embléma" class="login-window-logo dark-variant" src="img/logo-dark.svg">
                </div>
                <h1 class="login-window-title">Biztos vagy benne?</h1>
            </header>
            <p>Ha törölteted az összes adatodat, akkor minden általad feltöltött tartalom törlésre kerül:</p>
            <ul>
                <li>Személyes adatok</li>
                <li>Posztok és kommentek</li>
                <li>Képek</li>
                <li>Üzenetek</li>
            </ul>
            <p>A művelet nem fordítható vissza!</p>
            <button class="cta">Fiók és adatok törlése</button>
        </form>
    </body>
</html>