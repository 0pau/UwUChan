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
        <link rel="stylesheet" href="css/onboarding.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
        <script>
            function hide() {
                document.getElementById("stage2").style.display = "none";
                document.getElementById("stage3").style.display = "none";
            }

            function startDelete() {
                document.getElementById("stage1").style.display = "none";
                document.getElementById("stage2").style.display = "flex";
                let xhr = new XMLHttpRequest();

                xhr.open("POST", "api/user_actions.php");
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                xhr.onload = function() {
                    document.getElementById("log").innerHTML = xhr.response;
                    document.getElementById("stage2").style.display = "none";
                    document.getElementById("stage3").style.display = "flex";
                }
                xhr.send("action=delete");

            }
        </script>
    </head>
    <body class="login-background" onload="hide()">
        <div class="login-window onboarding" id="stage1">
            <div class="onboarding-illustration">
                <img src="img/delete_illustration.svg" alt="Illusztráció">
            </div>
            <div class="onboarding-content">
                <div class="form-content">
                    <h1 class="login-window-title">Minden adat törlése?</h1>
                    <p>Ha törölteted az összes adatodat, akkor minden általad feltöltött tartalom törlésre kerül:</p>
                    <ul>
                        <li>Személyes adatok</li>
                        <li>Posztok és kommentek</li>
                        <li>Képek</li>
                        <li>Üzenetek</li>
                    </ul>
                    <p>A művelet nem fordítható vissza!</p>
                </div>
                <div class="button-box">
                    <button class="cta" onclick="startDelete()">Fiók és adatok törlése</button>
                </div>
            </div>
        </div>
        <div class="login-window onboarding" id="stage2">
            <div class="onboarding-illustration">
                <img src="img/delete_illustration.svg" alt="Illusztráció">
            </div>
            <div class="onboarding-content">
                <h1>Törlés folyamatban...</h1>
                <p>A művelet az általad felhalmozott adatmennyiségtől függően akár hosszabb ideig is eltarthat.</p>
                <div class="pb animated"></div>
            </div>
        </div>
        <div class="login-window onboarding" id="stage3">
            <div class="onboarding-illustration">
                <img src="img/complete_illustration.svg" alt="Illusztráció">
            </div>
            <div class="onboarding-content">
                <div class="form-content">
                    <h1 class="login-window-title">Az adattörlés sikeresen befejeződött.</h1>
                    <p>A fiókodat és az összes hozzá tartozó adatod töröltük a rendszerből.</p>
                    <p>Reméljük, hamarosan újra látunk!</p>
                    <details>
                        <summary>Törlési napló</summary>
                        <p id="log"></p>
                    </details>
                </div>
                <div class="button-box">
                    <a href="login.php?deleted" class="button cta">Vissza a bejelentkezéshez</a>
                </div>
            </div>
        </div>
    </body>
</html>