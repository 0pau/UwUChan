<?php

    $returnObject = new stdClass();
    $returnObject->results = [];
    if (!isset($_GET["q"])) {
        $returnObject->result = "fail";
    } else {
        $returnObject->result = "success";
        $query = strtolower($_GET["q"]);
        $dir = new DirectoryIterator("../data/boards");
        foreach ($dir as $info) {
            if (!$info->isDot() && $info->isDir()) {
                $name = strtolower($info->getBasename());
                if (str_contains($name, $query)) {
                    $returnObject->results[] = $name;
                }
            }
        }
    }
    echo json_encode($returnObject, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);