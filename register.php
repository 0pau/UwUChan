<!doctype html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="css/main.css">
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
        <form method="dialog">
            <label for="login-name">Felhasználónév</label>
            <input name="login" id="login-name" required>
            <label for="email">Email-cím</label>
            <input name="email" id="email" required>
            <label for="birthday">Születési dátum <a href="help#birthday" class="help-link">Erre miért van szükség?</a></label>
            <input name="birthday" id="birthday" type="date" required>
            <label for="password">Jelszó <a href="help#password" class="help-link">Milyen a jó jelszó?</a></label>
            <input type="password" name="pass" id="password" required>
            <label for="password_again">Jelszó mégegyszer</label>
            <input type="password" name="pass_again" id="password_again" required>
            <button class="cta">Regisztráció</button>
        </form>
    </div>
</body>
</html>