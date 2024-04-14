<?php
    session_start(); include "api/users.php";
    $user = "ismeretlen";
    if (isset($_SESSION["user"])) {
        $user = $_SESSION["user"];
    }
?>
<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Első beállítás varázsló</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/login.css">
        <link rel="stylesheet" href="css/onboarding.css">
        <link rel="stylesheet" href="css/profile.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <script>
        function getBoards() {
            let bl = document.getElementById("boardList");
            bl.innerHTML = "";
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "api/getRandomBoards.php");
            xhr.onload = function() {
                let list = JSON.parse(xhr.response);
                list.forEach((element)=>{
                    bl.innerHTML += "<div class='board-item'><input name='boards[]' value='"+element+"' type='checkbox' id='"+element+"-b'><label for='"+element+"-b'>"+element+"</label></div>";
                });

            };
            xhr.send();
        }
    </script>
    <body class="login-background" onload="getBoards()">
        <div class="login-window onboarding">
            <?php if (isset($_SESSION["user"])) { ?>
            <div class="onboarding-illustration">
                <img src="img/onboarding.svg" alt="Illusztráció">
            </div>
            <div class="onboarding-content">
                <h1>Üdvözlünk a fedélzeten, kedves <?php echo $user ?>!</h1>
                <p>Örülünk, hogy Te is csatlakoztál az UwUChan közösségéhez! Az itt töltött időd személyre szabásához elvégezhetsz néhány alapvető beállítást.</p>
                <form method="POST" action="api/modify_settings.php">
                    <div class="form-content">
                        <p class="card-header">Ajánlott üzenőfalak</p>
                        <div class="list" id="boardList"></div>
                        <p class="card-header">Alapvető profilbeállítások</p>
                        <div class="list">
                            <div class="profile-setting-item">
                                <span class="material-symbols-rounded">clear_night</span>
                                <div>
                                    <p>Sötét téma használata</p>
                                </div>
                                <input type="checkbox" id="darkmode-switch" name="darkmode" <?php if (getUserField("isUsingDarkMode")) echo "checked" ?>>
                            </div>
                            <div class="profile-setting-item">
                                <img src="img/cursor.png" alt="Kurzor">
                                <div>
                                    <p>Cuki kurzor</p>
                                </div>
                                <input type="checkbox" id="cute-cursor-switch" name="cute_cursor" <?php if (getUserField("isUsingCuteCursor")) echo "checked" ?>>
                            </div>
                            <div class="profile-setting-item">
                                <span class="material-symbols-rounded">explicit</span>
                                <div>
                                    <p>Felnőtt tartalom szűrése</p>
                                </div>
                                <input type="checkbox" id="nsfw-switch" name="show_nsfw" <?php if (getUserField("filterNSFW")) echo "checked" ?>>
                            </div>
                        </div>
                    </div>
                    <div class="button-box">
                        <a href="index.php" class="button">Inkább kihagyom</a>
                        <button class="button cta">Kész</button>
                    </div>
                </form>
            </div>
            <?php } else { ?>
            <div class="onboarding-illustration">
                <img src="img/onboarding-error.svg" alt="Illusztráció">
            </div>
            <div class="onboarding-content">
                <h1>Hiba</h1>
                <p>Az onboarding felületet nem lehet felhasználói fiók nélkül használni.</p>
                <a href="login.php">Bejelentkezés</a>
            </div>
            <?php }?>

        </div>
    </body>
</html>