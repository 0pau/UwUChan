<?php

/**
 * <p><b>Speciális szkript: </b>Csak adott helyen érdemes PHP-kóból include-al meghívni!</p>
 * <h1>Theme</h1>
 * Ezt a szkriptet a body class attribútumába kell illeszteni, ahova beírja a megfelelő témabeállításokat
 * a felhasználó által konfigurált értékeknek megfelelően.
 */

    error_reporting(0);

    if (isset($_SESSION["user"]) && getUserField("isUsingDarkMode")) {
        echo "dark";
    } else {
        echo "light";
    }

    if (isset($_SESSION["user"]) && getUserField("isUsingCuteCursor")) {
        echo " cute";
    }