<?php

    $allBoards = [];

    $dir = new DirectoryIterator("../data/boards");
    foreach ($dir as $info) {
        if (!$info->isDot() && $info->isDir()) {
            $allBoards[] = $info->getBasename();
        }
    }
    $rand_keys = array_rand($allBoards, 3);

    echo json_encode(array($allBoards[$rand_keys[0]], $allBoards[$rand_keys[1]], $allBoards[$rand_keys[2]]));