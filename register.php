<?php
    include "api/users.php";
    include "api/util.php";

    $error = register();

    function register(): string {
        if (isset($_POST["login"]) && isset($_POST["email"]) && isset($_POST["birthday"]) && isset($_POST["pass"]) && isset($_POST["pass_again"])) {

            //var_dump($_FILES["pfp"]);
            //die();

            if (preg_match("/[A-Za-z0-9_.-]{3,32}/", $_POST["login"]) == 0) {
                return "A felhasználónév nem megfelelő";
            }

            if (user_exists($_POST["login"])) {
                return "A felhasználó már létezik";
            }

            if (preg_match("/^[a-z0-9]+@[a-z0-9]+\.[a-z]{2,5}$/", $_POST["email"]) == 0) {
                return "Hibás email-cím";
            }

            if (is_email_used($_POST["email"])) {
                return "Ezzel az email-címmel már regisztráltak.";
            }

            $birthday = strtotime($_POST["birthday"]);
            $today = strtotime(date("Y-m-d"));

            if ($today-$birthday < (13*365*24*60*60)) {
                return "13 éven aluliak nem regisztrálhatnak";
            }

            if (preg_match("/^[A-Za-z0-9#&@.,:?\"+_-]{8,64}$/", $_POST["pass"]) == 0) {
                return "A jelszó formátuma nem megfelelő";
            }

            if ($_POST["pass"] != $_POST["pass_again"]) {
                return "A jelszavak nem egyeznek";
            }

            $new_user = new stdClass();
            $new_user->nickname = $_POST["login"];
            $new_user->bio = "";
            $new_user->email = $_POST["email"];
            $new_user->birthday = $_POST["birthday"];
            $new_user->password = $_POST["pass"];
            $new_user->privilege = 0;
            $new_user->uwuness = 0;
            $new_user->isUsingDarkMode = false;
            $new_user->isUsingCuteCursor = true;
            $new_user->filterNSFW = false;
            $new_user->profilePictureFilename = "";
            $new_user->lockedUntil = -1;
            $new_user->lockReason = "not_locked";

            $filelist = null;

            if ($_FILES["pfp"]["name"] != "") {
                $filelist = $_FILES["pfp"];
            }

            if (create_user($new_user, $filelist)) {
                echo "Sikeres regisztráció! Hamarosan átirányításra kerülsz...";
                session_start();
                $_SESSION["user"] = $new_user->nickname;
                header("Location: onboarding.php");
            }

        }
        return "";
    }
?>

<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Regisztráció</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/login.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="login-background">
        <div class="login-window">
            <header class="login-window-head">
                <div class="color-variants">
                    <img alt="UwUChan-embléma" class="login-window-logo light-variant" src="img/logo.svg">
                    <img alt="UwUChan-embléma" class="login-window-logo dark-variant" src="img/logo-dark.svg">
                </div>
                <h1 class="login-window-title">Regisztráció</h1>
                <p>Már van fiókod? <a href="login.php">Bejelentkezés</a></p>
            </header>
            <form method="post" action="register.php" enctype="multipart/form-data">
                <label>
                    <span>Felhasználónév</span>
                    <input type="text" name="login" id="login-name" required>
                </label>
                <label>
                    <span>Email-cím</span>
                    <input type="text" name="email" id="email" required>
                </label>
                <label>
                    <span>Profilkép</span>
                    <input type="file" name="pfp" id="pfp" accept="image/*">
                </label>
                <label>
                    <span>Születési dátum <a target="_blank" href="help.php#birthday" class="help-link">Erre miért van szükség?</a></span>
                    <input name="birthday" id="birthday" type="date" required>
                </label>
                <label>
                    <span>Jelszó <a target="_blank" href="help.php#password" class="help-link">Milyen a jó jelszó?</a></span>
                    <input type="password" name="pass" id="password" required>
                </label>
                <label>
                    <span>Jelszó mégegyszer</span>
                    <input type="password" name="pass_again" id="password_again" required>
                </label>
                <label class="horizontal">
                    <input type="checkbox" name="consent" id="consent" required><span>Elolvastam és elfogadom a <a href="help.php#post_rules" target="_blank">szabályzatot</a></span>
                </label>
                <?php
                    if ($error != "") {
                        echo "<p id='login-error'>$error</p>";
                    }
                ?>
                <button class="cta">Regisztráció</button>
            </form>
        </div>
    </body>
</html>