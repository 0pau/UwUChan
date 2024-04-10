<?php include_once "api/users.php"?>
<header>
    <div class="color-variants header-side-element desktop">
        <img alt="UwUChan-embléma" class="logo light-variant" src="img/logo.svg">
        <img alt="UwUChan-embléma" class="logo dark-variant" src="img/logo-dark.svg">
    </div>
    <img alt="UwUChan-embléma" src="img/logo-fill.svg" class="logo mobile">
    <div class="searchbar desktop">
        <span class="material-symbols-rounded">search</span>
        <input type="text" placeholder="Keresés a sok UwU-ság között">
    </div>
    <a href="index.html" class="flat button icon right mobile"><span class="material-symbols-rounded">search</span></a>
    <?php if (isset($_SESSION["user"])) { ?>
    <div class="header-side-element user-profile-button">
        <div>
            <p><?php echo $_SESSION["user"] ?></p>
            <p><?php echo getUserField("uwuness")?> pont</p>
        </div>
        <img src="<?php echo getUserProfilePicture($_SESSION["user"]); ?>" alt="Profilkép">
        <div class="session-options">
            <a href="profile.php" class="session-option button flat"><span class="material-symbols-rounded">settings</span>Profilbeállítások</a>
            <a href="api/logout.php" class="session-option button flat"><span class="material-symbols-rounded">logout</span>Kijelentkezés</a>
        </div>
    </div>
    <?php } else {?>
        <div class="header-side-element">
            <a href="login.php" class="button cta right">Bejelentkezés</a>
        </div>
    <?php }?>
</header>