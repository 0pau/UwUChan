<?php
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

function boardExists($name, $root = ".") : bool {
    return is_dir($root."/data/boards/".$name);
}

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