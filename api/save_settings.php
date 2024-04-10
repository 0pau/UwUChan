<?php

    $ref = $_SERVER["HTTP_REFERER"];
    header("Location: $ref");