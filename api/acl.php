<?php

    /**
    <h1>"ACL" php szkript</h1>
    Azon oldalakra kell beágyazni, amikhez felhasználói
    azonosítás kell. Ha nincs bejelentkezve, akkor
    átirányítjuk a bejelentkezésre.
    */

    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
    }