<?php

    /*
    "ACL" php szkript
    Azon oldalakra kell beágyazni, amikhez felhasználói
    azonosítás kell. Ha nincs bejelentkezve, akkor
    átirányítjuk a bejelentkezésre.
    */

    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
    }