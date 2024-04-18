<?php
    session_start(); include "api/users.php"; include "api/acl.php"; include "api/util.php";

    $error = "";
    $completed = false;
    changePassword();

    function changePassword() {
        global $error;
        global $completed;
        if (!isset($_POST["current-pass"]) || !isset($_POST["new-pass"]) || !isset($_POST["new-pass-again"])) {
            return;
        }

        if (!password_verify($_POST["current-pass"], getUserField("password"))) {
            $error = "Helytelen jelszó";
            return;
        }

        $newPass = checkPassword($_POST["new-pass"]);

        if (!$newPass) {
            $error = "A jelszó formátuma nem megfelelő.";
            return;
        }

        if ($newPass != $_POST["new-pass-again"]) {
            $error = "A jelszavak nem egyeznek.";
            return;
        }

        $newPassHash = password_hash($newPass, PASSWORD_DEFAULT);
        changeUserField("password", $newPassHash);
        $completed = true;

    }



?>
<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Jelszó változtatása</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/login.css">
        <link rel="stylesheet" href="css/onboarding.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="login-background <?php include "api/theme.php"?>" onload="hide()">
        <div class="login-window onboarding centered" id="stage1">
            <div class="onboarding-illustration">
                <?php if (!$completed) { ?>
                <img src="img/password_illustration.svg" alt="Illusztráció">
                <?php } else { ?>
                    <img src="img/complete_illustration.svg" alt="Illusztráció">
                <?php } ?>
            </div>
            <?php if (!$completed) { ?>
            <div class="onboarding-content">
                <form method="POST">
                    <div class="form-content">
                        <h1>Jelszó módosítása</h1>
                        <p>A jelszavad módosításához add meg a mostani jelszavad és add meg kétszer az új jelszót.</p>
                        <p>Tartsd észben, hogy <a href="help.php#password" target="_blank">milyen a jó jelszó</a>!</p>
                        <label>
                            <span>Jelenlegi jelszó</span>
                            <input type="password" name="current-pass" required>
                        </label>
                        <label>
                            <span>Új jelszó</span>
                            <input type="password" name="new-pass" required>
                        </label>
                        <label>
                            <span>Új jelszó mégegyszer</span>
                            <input type="password" name="new-pass-again" required>
                        </label>
                        <?php
                            if ($error != "") {
                                echo "<p id='login-error'>$error</p>";
                            }
                        ?>
                    </div>
                    <div class="button-box">
                        <a class="button" href="profile.php">Mégse</a>
                        <button class="cta">OK</button>
                    </div>
                </form>
            </div>
            <?php } else { ?>
            <div class="onboarding-content">
                <div class="form-content">
                    <h1>Sikeres jelszóváltoztatás!</h1>
                    <p>Mostantól ezzel a jelszóval tudsz bejelentkezni az UwUChan-fiókodba.</p>
                </div>
                <div class="button-box">
                    <a class="button cta" href="profile.php">Kész</a>
                </div>
            </div>
            <?php } ?>
        </div>
    </body>
</html>