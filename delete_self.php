<?php session_start(); include "api/users.php"; include "api/acl.php"?>
<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Adatok törlése</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/login.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
        <script>
            function hide() {
                document.getElementById("stage2").style.display = "none";
                document.getElementById("stage3").style.display = "none";
            }

            function startDelete() {
                document.getElementById("stage1").style.display = "none";
                document.getElementById("stage2").style.display = "block";
                let xhr = new XMLHttpRequest();

                xhr.open("POST", "api/user_actions.php");
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                xhr.onload = function() {
                    document.getElementById("log").innerHTML = xhr.response;
                    document.getElementById("stage2").style.display = "none";
                    document.getElementById("stage3").style.display = "block";
                }
                xhr.send("action=delete");

            }
        </script>
    </head>
    <body class="login-background <?php include "api/theme.php"?>" onload="hide()">
        <div class="login-window" id="stage1">
            <header class="login-window-head">
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
            <button class="cta" onclick="startDelete()">Fiók és adatok törlése</button>
        </div>
        <div class="login-window" id="stage2">
            <header class="login-window-head">
                <h1 class="login-window-title">Törlés folyamatban...</h1>
            </header>
            <p>A művelet az általad felhalmozott adatmennyiségtől függően akár hosszabb ideig is eltarthat.</p>
            <div class="pb animated"></div>
        </div>
        <div class="login-window" id="stage3">
            <header class="login-window-head">
                <h1 class="login-window-title">Az adattörlés befejeződött</h1>
            </header>
            <p>A fiókodat és az összes hozzá tartozó adatod töröltük a rendszerből.</p>
            <details>
                <summary>Törlési napló</summary>
                <p id="log"></p>
            </details>
            <a href="login.php" class="button cta">Vissza a bejelentkezéshez</a>
        </div>
    </body>
</html>