<?php session_start(); include "api/users.php"; include "api/acl.php"?>
<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Profilom</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/mobile.css">
        <link rel="stylesheet" href="css/profile.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php include "views/sidebar.php"?>
                <section>
                    <div class="section-head">
                        <h1>Profilbeállítások</h1>
                    </div>
                    <form action="api/modify_settings.php" method="post">
                        <p class="card-header">Felület személyre szabása</p>
                        <div class="list">
                            <div class="profile-setting-item">
                                <span class="material-symbols-rounded">clear_night</span>
                                <div>
                                    <p>Sötét téma használata</p>
                                    <p>Megkönnyíti az olvasást sötét környezetben</p>
                                </div>
                                <input type="checkbox" id="darkmode-switch" name="darkmode" <?php if (getUserField("isUsingDarkMode")) echo "checked" ?>>
                            </div>
                            <div class="profile-setting-item">
                                <img src="img/cursor.png" alt="Kurzor">
                                <div>
                                    <p>Cuki kurzor</p>
                                    <p>Számítógépen aranyos, UwUChan stílusúvá varázsolja a kurzorodat</p>
                                </div>
                                <input type="checkbox" id="cute-cursor-switch" name="cute_cursor" <?php if (getUserField("isUsingCuteCursor")) echo "checked" ?>>
                            </div>
                            <div class="profile-setting-item">
                                <span class="material-symbols-rounded">explicit</span>
                                <div>
                                    <p>Felnőtt tartalom szűrése</p>
                                    <p>A felnőtteknek szánt posztokat és üzenőfalakat a rendszer elrejti az oldalsávon, a feedben és a keresési találatoknál.</p>
                                </div>
                                <input type="checkbox" id="nsfw-switch" name="show_nsfw" <?php if (getUserField("filterNSFW")) echo "checked" ?>>
                            </div>
                            <div class="profile-setting-item">
                                <span>A változtatások érvénybe léptetéséhez ne felejsd el elmenteni a beállításaidat -&gt;</span>
                                <button class="right cta">Mentés</button>
                            </div>
                        </div>
                    </form>
                    <div>
                        <p class="card-header">Adataim</p>
                        <div class="list">
                            <a class="profile-setting-item">
                                <span class="material-symbols-rounded">account_circle</span>
                                <div>
                                    <p>Profilkép beállítása</p>
                                    <p>A profilképed megjelenik az általad feltöltött posztoknál és kommenteknél.</p>
                                </div>
                            </a>
                            <a class="profile-setting-item">
                                <span class="material-symbols-rounded">badge</span>
                                <div>
                                    <p>Felhasználónév</p>
                                    <p><?php echo getUserField("nickname"); ?></p>
                                </div>
                            </a>
                            <a class="profile-setting-item">
                                <span class="material-symbols-rounded">mail</span>
                                <div>
                                    <p>E-mail cím</p>
                                    <p><?php echo getUserField("email"); ?></p>
                                </div>
                            </a>
                            <a class="profile-setting-item">
                                <span class="material-symbols-rounded">calendar_month</span>
                                <div>
                                    <p>Születési idő</p>
                                    <p><?php echo str_replace("-", ". ", getUserField("birthday")); ?></p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div>
                        <p class="card-header">Veszélyzóna</p>
                        <div class="list">
                            <a class="profile-setting-item destructive" href="delete_self.php">
                                <span class="material-symbols-rounded">delete</span>
                                <div>
                                    <p>Fiók végleges törlése</p>
                                    <p>Ezzel minden adatodat töröljük a rendszerből. Az általad írt kommentek és üzenetek helyén a [törölt] felirat fog megjelenni.</p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div>
                        <p class="card-header">Debug</p>
                        <div class="list">
                            <a class="profile-setting-item" href="onboarding.php">
                                <span class="material-symbols-rounded">select_window</span>
                                <div>
                                    <p>Onboarding futtatása</p>
                                    <p>A regisztráció után megjelenő beállításvarázslót futtatja.</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>