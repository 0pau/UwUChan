<?php

    error_reporting(0);

    if (isset($_SESSION["user"]) && getUserField("isUsingDarkMode")) {
        echo "dark";
    } else {
        echo "light";
    }

    if (isset($_SESSION["user"]) && getUserField("isUsingCuteCursor")) {
        echo " cute";
    }