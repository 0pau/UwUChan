<?php
    session_start();

    if (isset($_SESSION["user"])) {
        header("Location: index.php");
    }

    $remembered = "";
    $error = login();

    function login(): string {
        global $remembered;

        if (isset($_POST["login"]) && isset($_POST["pass"])) {

            if (!is_dir("data/users/".$_POST["login"])) {
                return "Hibás felhasználónév, vagy jelszó";
            }

            $json = file_get_contents("data/users/".$_POST["login"]."/metadata.json");
            $user = json_decode($json, JSON_UNESCAPED_UNICODE);
            if (!password_verify($_POST["pass"], $user["password"])) {
                return "Hibás felhasználónév, vagy jelszó";
            }

            if (isset($_POST["remember-me"])) {
                setcookie("remembered-user", $_POST["login"], time()+60*60*24*30);
            } else {
                setcookie("remembered-user", "", time()-60*60*24*30);
            }

            $_SESSION["user"] = $_POST["login"];
            header("Location: index.php");

        } else {
            if (isset($_GET["deleted"])) {
                setcookie("remembered-user", "", time()-60*60*24*30);
            } else {
                if (isset($_COOKIE["remembered-user"])) {
                    $remembered = $_COOKIE["remembered-user"];
                }
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
        <title>Bejelentkezés</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/login.css">
        <link rel="stylesheet" href="css/onboarding.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="login-background">
        <div class="login-window onboarding">
            <div class="onboarding-illustration">
                <img src="img/login_illustration.svg" alt="Illusztráció">
            </div>
            <div class="onboarding-content">
                <div class="color-variants">
                    <img alt="UwUChan-embléma" class="login-window-logo light-variant" src="img/logo.svg">
                    <img alt="UwUChan-embléma" class="login-window-logo dark-variant" src="img/logo-dark.svg">
                </div>
                <h1 class="login-window-title">Jelentkezz be</h1>
                <p>Nincs fiókod? <a href="register.php">Regisztráció</a></p>
                <?php if ($error != "") { ?>
                    <p class="error"><span class="material-symbols-rounded">error</span><?php echo $error ?></p>
                <?php } ?>
                <form method="post">
                    <div class="form-content">
                        <label>
                            <span>Felhasználónév</span>
                            <input type="text" name="login" id="login-name" required <?php if ($remembered != "") echo "value='$remembered'" ?>>
                        </label>
                        <label>
                            <span>Jelszó</span>
                            <input type="password" name="pass" id="password" required>
                        </label>
                        <label class="horizontal has-description">
                            <input type="checkbox" name="remember-me" <?php if ($remembered != "") echo "checked"?>><span>Jegyezz meg</span>
                        </label>
                        <p class="card-description">A felhasználóneveded 30 napra megjegyezzük, így nem kell mindig beírnod.</p>
                    </div>
                    <div class="button-box">
                        <button class="cta">Bejelentkezés</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>