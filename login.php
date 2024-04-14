<?php
    session_start();

    if (isset($_SESSION["user"])) {
        header("Location: help.php");
    }

    $error = login();

    function login(): string {

        if (isset($_POST["login"]) && isset($_POST["pass"])) {

            if (!is_dir("data/users/".$_POST["login"])) {
                return "Hibás felhasználónév, vagy jelszó";
            }

            $json = file_get_contents("data/users/".$_POST["login"]."/metadata.json");
            $user = json_decode($json, JSON_UNESCAPED_UNICODE);
            if (!password_verify($_POST["pass"], $user["password"])) {
                return "Hibás felhasználónév, vagy jelszó";
            }
            $_SESSION["user"] = $_POST["login"];
            header("Location: index.php");

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
        <title>Bejelentkezés</title>
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
                <h1 class="login-window-title">Jelentkezz be</h1>
                <p>Nincs fiókod? <a href="register.php">Regisztráció</a></p>
            </header>
            <form method="post">
                <label>
                    <span>Felhasználónév</span>
                    <input type="text" name="login" id="login-name" required>
                </label>
                <label>
                    <span>Jelszó</span>
                    <input type="password" name="pass" id="password" required>
                </label>
                <label class="horizontal">
                    <input type="checkbox" name="remember"><span>Jegyezz meg</span>
                </label>
                <?php
                    if ($error != "") {
                        echo "<p id='login-error'>$error</p>";
                    }
                ?>
                <button class="cta">Bejelentkezés</button>
            </form>
        </div>
    </body>
</html>