<!doctype html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body prefer-dark="false" class="login-background">
    <div class="login-window">
        <div class="login-window-head">
            <div class="color-variants">
                <img alt="UwUChan-embléma" class="login-window-logo light-variant" src="img/logo.svg">
                <img alt="UwUChan-embléma" class="login-window-logo dark-variant" src="img/logo-dark.svg">
            </div>
            <h1 class="login-window-title">Jelentkezz be</h1>
            <p>Nincs fiókod? <a href="register.php">Regisztráció</a></p>
        </div>
        <form method="post">
            <label for="login-name">Felhasználónév</label>
            <input name="login" id="login-name">
            <label for="password">Jelszó</label>
            <input type="password" name="pass" id="password">
            <button class="cta">Bejelentkezés</button>
        </form>
    </div>
</body>
</html>