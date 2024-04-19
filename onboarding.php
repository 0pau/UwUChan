<?php
    session_start();
    include "api/users.php";
    include "api/boards.php";
    $user = "ismeretlen";
    if (isset($_SESSION["user"])) {
        $user = $_SESSION["user"];
    }

    $stage = 1;
    if (isset($_GET["stage"])) {
        $stage = intval($_GET["stage"]);
    }
    if ($stage > 4 || $stage < 1) {
        $stage = 404;
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
    <body class="login-background">
        <div class="login-window onboarding">
            <?php if (isset($_SESSION["user"])) { ?>
            <div class="onboarding-illustration">
                <img src="
                <?php
                echo match ($stage) {
                    1 => "img/onboarding.svg",
                    2 => "img/selection_illustration.svg",
                    3 => "img/settings_illustration.svg",
                    4 => "img/complete_illustration.svg",
                    404 => "img/onboarding-error.svg",
                };
                ?>
                " alt="Illusztráció">
            </div>
            <?php if ($stage == 1) { ?>
            <div class="onboarding-content">
                <div class="form-content">
                    <h1>Üdvözlünk a fedélzeten, kedves <?php echo $user ?>!</h1>
                    <p>Örülünk, hogy Te is csatlakoztál az UwUChan közösségéhez! Az itt töltött időd személyre szabásához elvégezhetsz néhány alapvető beállítást.</p>
                    <p>A beállításvarázsló segítségével</p>
                    <ul>
                        <li>Elkezdhetsz követni néhány üzenőfalat</li>
                        <li>Saját ízlésedre szabhatod az UwUChan felületét</li>
                    </ul>
                    <p>A beállításvarázsló indításához kattints a Tovább lehetőségre, ha viszont nem szeretnél ezzel foglalkozni, kattints az Inkább kihagyom feliratú gombra.</p>
                </div>
                <div class="button-box">
                    <a href="index.php" class="button">Inkább kihagyom</a>
                    <a class="button cta" href="?stage=2">Tovább</a>
                </div>
            </div>
            <?php } ?>
            <?php if ($stage == 2) { ?>
            <div class="onboarding-content">
                <h1>Üzenőfalak</h1>
                <p>Válaszd ki az alábbi listából azon üzenőfalakat, amik a Te érdeklődési körödnek megfelelnek. Amelyik üzenőfalat kiválasztod, azonnal követésre kerül.</p>
                <form method="POST" action="api/modify_settings.php">
                    <input type="hidden" name="stage" value="2">
                    <div class="form-content">
                        <div class="list" id="boardList">
                            <?php
                                $boardList = getRandomBoards();
                                foreach ($boardList as $board) {
                                    $icon = getBoardIcon($board);
                                    echo "<div class='board-item'><input type='checkbox' name='boards[]' value='$board'><label><img alt='$board ikonja' src='$icon'>$board</label></div>";
                                }

                            ?>
                        </div>
                    </div>
                    <div class="button-box">
                        <button class="cta">Tovább</button>
                    </div>
                </form>
            </div>
            <?php } ?>
            <?php if ($stage == 3) { ?>
                <div class="onboarding-content">
                    <form method="POST" action="api/modify_settings.php">
                        <input type="hidden" name="stage" value="3">
                        <div class="form-content">
                            <h1>Profilbeállítások</h1>
                            <p>Alakítsd ízlésed szerint az UwUChan felületét!</p>
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
                            <button class="cta">Következő</button>
                        </div>
                    </form>
                </div>
            <?php } ?>
            <?php if ($stage == 4) { ?>
                <div class="onboarding-content">
                    <form method="POST" action="api/modify_settings.php">
                        <input type="hidden" name="stage" value="3">
                        <div class="form-content">
                            <h1>Kész is vagyunk!</h1>
                            <p>Az UwUChan-fiókod mostmár készen áll a használatra.</p>
                        </div>
                        <div class="button-box">
                            <a href="index.php" class="button cta">Kész</a>
                        </div>
                    </form>
                </div>
            <?php } ?>
            <?php if ($stage == 404) { ?>
                <div class="onboarding-content">
                    <form method="POST" action="api/modify_settings.php">
                        <input type="hidden" name="stage" value="3">
                        <div class="form-content">
                            <h1>Hiba történt</h1>
                            <p>A beállításvarázsló nem elérhető.</p>
                        </div>
                        <div class="button-box">
                            <a href="index.php" class="button cta">Kész</a>
                        </div>
                    </form>
                </div>
            <?php } ?>
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