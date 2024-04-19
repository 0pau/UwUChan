<?php

    $error = "";
    $success = false;

    session_start(); include "api/users.php"; include "api/acl.php"; include "api/util.php";
    $p = "details";
    if (isset($_GET["page"]) && $_GET["page"] == "settings") {
        $p = "settings";
    } else {
        try {
            $success = modifyData();
        } catch (Error $err) {
            $error = $err->getMessage();
        }
    }


    function modifyData() : bool {

        if (!isset($_POST["email"]) || !isset($_POST["login"]) || !isset($_POST["birthday"]) || !isset($_POST["pfpChanged"])) {
            return false;
        }

        if ($_POST["pfpChanged"]) {
            if (getUserField("profilePictureFilename") != "") {
                unlink("data/images/".getUserField("profilePictureFilename"));
            }

            $ext = explode(".", $_FILES["pfpFile"]["name"][0]);
            $ext = end($ext);

            $filename = saveImage($ext, $_FILES["pfpFile"]["tmp_name"][0]);
            changeUserField("profilePictureFilename", $filename);
        }

        $email = checkEmail($_POST["email"]);

        if (!$email) {
            throw new Error("Az e-mail cím formátuma nem megfelelő.");
        }

        if ($email != "" && $email != getUserField("email")) {
            if (isEmailUsed($email)) {
                throw new Error("A megadott e-mail címmel már regisztráltak.");
            } else {
                changeUserField("email", $email);
            }
        }

        $birthday = checkBirthday($_POST["birthday"]);
        if (!$birthday) {
            throw new Error("13 éven aluliak nem használhatják az UwUChan-t.");
        }
        if ($birthday != getUserField("birthday")) {
            changeUserField("birthday", $birthday);
        }

        $newName = checkUsername($_POST["login"]);
        if (!$newName) {
            throw new Error("A felhasználónév formátuma nem megfelelő.");
        }

        if ($newName != $_SESSION["user"]) {
            if (userExists($newName)) {
                throw new Error("Ez a felhasználónév már foglalt.");
            }
            changeUserName($newName);
        }

        return true;
    }


?>
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
        <script>

            let oldPfp = "<?php echo getUserProfilePicture($_SESSION["user"]); ?>"

            function changePfpPreview() {
                let input = document.getElementById("pfpFile");
                let fr = new FileReader();
                fr.onload = function (){
                    document.getElementById("pfpPreview").src = fr.result;
                }
                fr.readAsDataURL(input.files[0]);
                document.getElementById("pfpChanged").value = "1";
            }

            function removePfp() {
                let input = document.getElementById("pfpFile");
                document.getElementById("pfpPreview").src = "img/default_user_avatar.png";
                input.value = null;
                document.getElementById("pfpChanged").value = "1";
            }
        </script>
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php include "views/sidebar.php"?>
                <section class="no-padding">
                    <div class="tab-bar">
                        <a class="button <?php if ($p == "details") echo "active" ?>" href="profile.php">Adataim</a>
                        <a class="button <?php if ($p == "settings") echo "active" ?>" href="?page=settings">Beállítások</a>
                    </div>
                    <?php if ($p == "details") { ?>
                    <div class="section-inset">
                        <?php if ($error != "") { ?>
                            <p class="error"><span class="material-symbols-rounded">error</span><?php echo $error ?></p>
                        <?php } if ($success) { ?>
                            <p class="success"><span class="material-symbols-rounded">check_circle</span>Az adataidat elmentettük.</p>
                        <?php } ?>
                        <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="pfpChanged" id="pfpChanged" value="0">
                        <div class="list">
                            <div>
                                <p class="card-header">Profilkép</p>
                                <div id="pfp-change-ui" class="profile-detail-item">
                                    <img id="pfpPreview" alt="Profilkép előnézete" src="<?php echo getUserProfilePicture($_SESSION["user"]); ?>">
                                    <div class="button-box">
                                        <!--<button>Új profilkép feltöltése</button>-->
                                        <div class="image-upload-button button">
                                            <input id="pfpFile" name="pfpFile[]" type="file" accept="image/*" onchange="changePfpPreview()">
                                            Profilkép feltöltése
                                        </div>
                                        <span class="button" onclick="removePfp()">Profilkép törlése</span>
                                    </div>
                                </div>
                                <p class="card-header">Felhasználónév</p>
                                <div class="profile-detail-item">
                                    <input type="text" name="login" value="<?php echo $_SESSION["user"]; ?>">
                                </div>
                                <p class="card-header">E-mail cím</p>
                                <div class="profile-detail-item">
                                    <input type="email" name="email" value="<?php echo getUserField("email"); ?>">
                                </div>
                                <p class="card-header">Születési idő</p>
                                <div class="profile-detail-item">
                                    <input type="date" name="birthday" value="<?php echo getUserField("birthday"); ?>">
                                </div>
                                <p class="card-header">Jelszó</p>
                                <a class="profile-setting-item" href="change_password.php">
                                    <span class="material-symbols-rounded">key</span>
                                    <div>
                                        <p>Jelszó módosítása</p>
                                        <p>Az adatok biztonsága érdekében a jelszó változtatását külön oldalon kell elvégezned</p>
                                    </div>
                                </a>
                            </div>
                            <div class="profile-setting-item">
                                <div>
                                    <p>A változtatások érvénybe léptetéséhez ne felejtsd el elmenteni a beállításaidat!</p>
                                    <p>Csak azon adataidat írjuk át, aminek értéke megváltozik.</p>
                                </div>
                                <button class="right cta">Mentés</button>
                            </div>
                        </div>
                    </form>
                    </div>
                    <?php } else { ?>
                    <div class="section-inset">
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
                                    <span>A változtatások érvénybe léptetéséhez ne felejtsd el elmenteni a beállításaidat -&gt;</span>
                                    <button class="right cta">Mentés</button>
                                </div>
                            </div>
                        </form>
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
                        <?php if (getUserField("privilege") == 1) { ?>
                        <div>
                            <p class="card-header">Fejlesztői beállítások, információk</p>
                            <p class="card-description">Ezeket az UwUChan fejelsztőinek szánjuk hibakeresésre.</p>
                            <div class="list">
                                <a class="profile-setting-item" href="onboarding.php">
                                    <span class="material-symbols-rounded">select_window</span>
                                    <div>
                                        <p>Onboarding futtatása</p>
                                        <p>A regisztráció után megjelenő beállításvarázslót futtatja.</p>
                                    </div>
                                </a>
                                <a class="profile-setting-item" href="api/repair_dirs.php">
                                    <span class="material-symbols-rounded">folder_managed</span>
                                    <div>
                                        <p>Könyvtárak helyreállítása</p>
                                        <p>Létrehozza az UwUChan működéséhez elengedhetetlen könyvtárakat.</p>
                                    </div>
                                </a>
                                <form action="api/modify_settings.php" method="POST" id="debugger">
                                    <input type="hidden" name="extreme-debug-setting" value="1">
                                    <div class="profile-setting-item">
                                        <span class="material-symbols-rounded">bug_report</span>
                                        <div>
                                            <p>Extrém hibakereső mód</p>
                                            <p>Kiír minden felmerülő hibát és figyelmeztetést.</p>
                                        </div>
                                        <input onchange="document.getElementById('debugger').submit()" type="checkbox" id="extreme_debug_mode" name="extreme_debug_mode" <?php if (isset($_SESSION["extreme_debug_mode"])) echo "checked" ?>>
                                    </div>
                                </form>
                                <div class="profile-setting-item">
                                    <span class="material-symbols-rounded">fingerprint</span>
                                    <div>
                                        <p>Munkamenet-azonosító</p>
                                        <p><?php echo $_COOKIE["PHPSESSID"] ?></p>
                                    </div>
                                </div>
                                <?php if (file_exists("data/last_update.txt")) { ?>
                                    <div class="profile-setting-item">
                                        <span class="material-symbols-rounded">construction</span>
                                        <div>
                                            <p>Build-szám</p>
                                            <p><?php echo file_get_contents("data/last_update.txt") ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="profile-setting-item">
                                    <span class="material-symbols-rounded">imagesmode</span>
                                    <div>
                                        <p>GD könyvtár</p>
                                        <p><?php echo (isGDAvailable())?"Telepítve":"Nincs telepítve" ?></p>
                                    </div>
                                </div>
                                <div class="profile-setting-item">
                                    <span class="material-symbols-rounded">package_2</span>
                                    <div>
                                        <p>PHP verziója</p>
                                        <p><?php echo phpversion() ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </section>
            </div>
        </main>
    </body>
</html>