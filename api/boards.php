<?php
/**
 * Lekéri az összes hírfolyamot a randszerből és visszaadja összekeverve.
 * Használatos: az onboarding felületen, ahol a user kiválaszthatja az érdkelődési körét.
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return array: az összekevert hírfolyamok nevei.
 */

function getRandomBoards($root = ".") {
    $allBoards = [];
    $dir = new DirectoryIterator("$root/data/boards");
    foreach ($dir as $info) {
        if (!$info->isDot() && $info->isDir()) {
            $allBoards[] = $info->getBasename();
        }
    }
    shuffle($allBoards);

    return $allBoards;
}

/**
 * Lekéri egy adott üzenőfal metadata.json fájl-jában található adatokat és visszaadja stdClass formában
 * @param $name: a hírfolyam neve
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return stdClass|null: az objektum, vagy null, ha nem létezik a board fájlja.
 */
function getBoardInfo($name, $root = ".") : ?stdClass {

    if (!is_dir($root."/data/boards/".$name)) {
        return null;
    }
    if (!file_exists($root."/data/boards/".$name."/metadata.json")) {
        return null;
    }
    $meta = file_get_contents($root."/data/boards/".$name."/metadata.json");

    return json_decode($meta, false);
}

/**
 * Menti egy üzenőfal adatait annak a metadata.json fájljába.
 * @param $name: Az üzenőfal neve
 * @param $boardInfo: Az információk, ami a metadata.json-ba kerül - stdClass-nak kell lennie.
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return void|null
 */
function saveBoardInfo($name, $boardInfo, $root = ".") {

    if (!is_dir($root."/data/boards/".$name)) {
        return null;
    }
    if (!file_exists($root."/data/boards/".$name."/metadata.json")) {
        return null;
    }

    $data = json_encode($boardInfo, JSON_UNESCAPED_UNICODE);

    file_put_contents($root."/data/boards/".$name."/metadata.json", $data);
}

/**
 * Ellenőrzi és visszaadja, hogy létezik -e egy üzenőfal.
 * @param $name: A keresett üzenőfal neve
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return bool: true, ha létezik, false, ha nem.
 */
function boardExists($name, $root = ".") : bool {
    return is_dir($root."/data/boards/".$name);
}

/**
 * Lekéri egy üzenőfal ikonjának a fájlnevét.
 * A visszaadott érték 3 féle lehet:
 * 1. Ha létezik az üzenőfal és van ikonja, akkor az ikon fájlneve a data/images könyvtárral együtt.
 * 2. Ha létezik az üzenőfal, de nincs ikonja, akkor az alapértelmezett user avatár fájlneve.
 * 3. Ha nem létezik az üzenőfal, akkor az ismeretlen ikon fájlneve.
 * @param $name: Az üzenőfal neve
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return string
 */
function getBoardIcon($name, $root = ".") : string {

    $file = getBoardInfo($name, $root);
    if ($file != null) {
        $file = $file->icon_filename;
    } else {
        return "img/unknown.png";
    }

    if ($file != "") {
        return "data/images/".$file;
    }

    return "img/default_user_avatar.png";
}