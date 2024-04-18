<?php
    include_once "api/users.php";

    $page =$_SERVER["REQUEST_URI"];

    $page = explode("/", $page);
    $page = end($page);

?>
<header>
    <div class="color-variants header-side-element desktop">
        <img alt="UwUChan-embléma" class="logo light-variant" src="img/logo.svg">
        <img alt="UwUChan-embléma" class="logo dark-variant" src="img/logo-dark.svg">
    </div>
    <img alt="UwUChan-embléma" src="img/logo-fill.svg" class="logo mobile">
    <form class="searchbar" method="GET" action="search.php">
        <span class="material-symbols-rounded">search</span>
        <input name="q" type="text" placeholder="Keresés a sok UwU-ság között"
        <?php if (str_contains($page, "search.php")) echo "value=\"".$_GET["q"]."\"" ?>>
    </form>
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
        <a href="login.php" class="header-side-element user-profile-button">
            <div>
                <p>Jelentkezz be!</p>
            </div>
            <img src="img/unknown.png" alt="Profilkép">
        </a>
    <?php }?>
</header>