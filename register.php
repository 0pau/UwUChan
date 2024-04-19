<?php
    include "api/users.php";
    include "api/util.php";

    $error = "";
    try {
        register();
    } catch (Error $err) {
        $error = $err->getMessage();
    }

    function register(): string {
        if (isset($_POST["login"]) && isset($_POST["email"]) && isset($_POST["birthday"]) && isset($_POST["pass"]) && isset($_POST["pass_again"])) {

            $username = checkUsername($_POST["login"]);

            if (!$username) {
                throw new Error("A felhasználónév nem megfelelő");
            }

            if (userExists($_POST["login"])) {
                throw new Error("Ez a felhasználónév már foglalt");
            }

            $email = checkEmail($_POST["email"]);

            if (!$email) {
                throw new Error("Hibás e-mail cím");
            }

            if (isEmailUsed($_POST["email"])) {
                throw new Error("Ezzel az e-mail címmel már regisztráltak");
            }

            $birthday = checkBirthday($_POST["birthday"]);

            if (!$birthday) {
                throw new Error("13 éven aluliak nem regisztrálhatnak");
            }

            $pass = checkPassword($_POST["pass"]);
            if (!$pass) {
                throw new Error("A jelszó formátuma nem megfelelő");
            }

            if ($pass != $_POST["pass_again"]) {
                throw new Error("A jelszavak nem egyeznek");
            }

            $new_user = new stdClass();
            $new_user->nickname = $username;
            $new_user->bio = "";
            $new_user->email = $email;
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
            $new_user->liked_posts = [];
            $new_user->disliked_posts = [];

            $filelist = null;

            if ($_FILES["pfp"]["name"] != "") {
                $filelist = $_FILES["pfp"];
            }

            if (createUser($new_user, $filelist)) {
                session_start();
                $_SESSION["user"] = $new_user->nickname;
                setcookie("remembered-user", "", time()-60*60*24*30);
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
        <link rel="stylesheet" href="css/onboarding.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="login-background">
        <div class="login-window onboarding">
            <div class="onboarding-illustration">
                <img src="img/register_illustration.svg" alt="Illusztráció">
            </div>
            <div class="onboarding-content">
                <form method="post" action="register.php" enctype="multipart/form-data">
                    <div class="color-variants">
                        <img alt="UwUChan-embléma" class="login-window-logo light-variant" src="img/logo.svg">
                        <img alt="UwUChan-embléma" class="login-window-logo dark-variant" src="img/logo-dark.svg">
                    </div>
                    <h1 class="login-window-title">Regisztráció</h1>
                    <p>Már van fiókod? <a href="login.php">Bejelentkezés</a></p>
                    <?php if ($error != "") { ?>
                        <p class="error"><span class="material-symbols-rounded">error</span><?php echo $error ?></p>
                    <?php } ?>
                    <div class="form-content">
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
                    </div>
                    <div class="button-box">
                        <button class="cta">Regisztráció</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>