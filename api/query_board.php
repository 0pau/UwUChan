<?php

/**
<p><b>API végpont: </b><i>HTML formból, vagy JavaScriptből hívható</i></p>
<h1>Query board</h1>
API végpont, ami egy üzenőfal kereséséhez használatos. GET paramétere a "q", amiben a keresett szövegrészt kell
megadni. Válaszként egy JSON-objektummal tér vissza, aminek a "results" tömbje tartalmazza a találatokat.
 */

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