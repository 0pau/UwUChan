<?php

    error_reporting(0);

    if (isset($_SESSION["user"]) && getUserField("isUsingDarkMode")) {
        echo "dark";
    } else {
        echo "light";
    }