<?php

//repair_dirs();

function repair_dirs() {
    if (!is_dir("data")) {
        mkdir("data");
    }
    if (!is_dir("data/users")) {
        mkdir("data/users");
    }
    if (!is_dir("data/boards")) {
        mkdir("data/boards");
    }
    if (!is_dir("data/threads")) {
        mkdir("data/threads");
    }
    if (!is_dir("data/reports")) {
        mkdir("data/reports");
    }
    if (!is_dir("data/requests")) {
        mkdir("data/requests");
    }
    if (!is_dir("data/images")) {
        mkdir("data/images");
    }
}

function is_email_used($email): bool {

    if (!is_dir("data/users")) {
        return false;
    }

    $dir = new DirectoryIterator("data/users");
    foreach ($dir as $info) {
        if (!$info->isDot() && $info->isDir()) {
            $u = json_decode(file_get_contents("data/users/".$info->getBasename()."/metadata.json"));
            if ($u->email == $email) {
                return true;
            }
        }
    }
    return false;
}

function user_exists($name): bool {
    return (file_exists("data/users/".$name));
}

function create_user($user, $file): bool {
    if (!is_dir("data")) {
        mkdir("data");
    }
    if (!is_dir("data/users")) {
        mkdir("data/users");
    }
    $dir = "data/users/".$user->nickname;
    mkdir($dir);

    if ($file != null) {
        $fileSeparated = explode(".", $file["name"]);
        $ext = end($fileSeparated);
        $user->profilePictureFilename = saveImage($ext, $file["tmp_name"]);
    }

    $user->password = password_hash($user->password, PASSWORD_DEFAULT);
    $json_data = json_encode($user, JSON_UNESCAPED_UNICODE);
    file_put_contents($dir."/metadata.json", $json_data);
    file_put_contents($dir."/threads.json", "[]");
    file_put_contents($dir."/followed_boards.json", "[]");
    file_put_contents($dir."/owned_boards.json", "[]");
    file_put_contents($dir."/friends.json", "[]");
    file_put_contents($dir."/posts.json", "[]");
    return true;
}

function getUserField($field, $root = ".") {
    if (isset($_SESSION["user"])) {
        $json = file_get_contents($root."/data/users/".$_SESSION["user"]."/metadata.json");
        return json_decode($json, false)->$field;
    }
    return null;
}

function changeUserField($field, $value, $root = ".") {

    $file = $root."/data/users/".$_SESSION["user"]."/metadata.json";
    $userdata = file_get_contents($file);
    $userdata = json_decode($userdata, false);
    $userdata->$field = $value;

    $newdata = json_encode($userdata, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $newdata);
}

function getUserProfilePicture($name) {
    if (user_exists($name)) {
        if (getUserField("profilePictureFilename") != "") {
            return "data/images/" . getUserField("profilePictureFilename");
        }
    }
    return "img/default_user_avatar.png";
}

function getUserCount() : int {
    if (!is_dir("data/users")) {
        return 0;
    }

    $s = 0;
    $dir = new DirectoryIterator("data/users");
    foreach ($dir as $info) {
        if (!str_starts_with($info->getBasename(), ".")) {
            $s++;
        }
    }

    return $s;
}

function getFollowedCount($root = ".") : int {

    if (!isset($_SESSION["user"])) {
        return 0;
    }

    $file = $root."/data/users/".$_SESSION["user"]."/followed_boards.json";
    $followed = file_get_contents($file);
    $followed = json_decode($followed, false);

    return count($followed);
}

function getMostRankedBoards($root = ".") {

    if (!isset($_SESSION["user"])) {
        return [];
    }

    $file = $root."/data/users/".$_SESSION["user"]."/followed_boards.json";
    $followed = file_get_contents($file);
    $followed = json_decode($followed, false);
    usort($followed, "rankcmp");

    return $followed;

}

function rankcmp($a, $b) {
    if ($a->visits < $b->visits) {
        return 1;
    } else if ($a->visits > $b->visits) {
        return -1;
    }
    return 0;
}

function isBoardFollowed($name, $root = ".") : bool {
    if (!isset($_SESSION["user"])) {
        return false;
    }

    $file = $root."/data/users/".$_SESSION["user"]."/followed_boards.json";
    $followed = file_get_contents($file);
    $followed = json_decode($followed, false);
    foreach ($followed as $board) {
        if ($board->name == $name) {
            return true;
        }
    }

    return false;
}

function saveBoardVisit($name) {
    if (!isset($_SESSION["user"])) {
        return;
    }

    $file = "data/users/".$_SESSION["user"]."/followed_boards.json";
    $followed = file_get_contents($file);
    $followed = json_decode($followed, false);
    for ($i = 0; $i < count($followed); $i++) {
        if ($followed[$i]->name == $name) {
            $followed[$i]->visits = $followed[$i]->visits+1;
            break;
        }
    }
    $followed = json_encode($followed, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $followed);
}

function followBoard($name, $root = ".") {

    if (isBoardFollowed($name, $root)) {
        return;
    }

    $file = $root."/data/users/".$_SESSION["user"]."/followed_boards.json";
    $followed = file_get_contents($file);
    $followed = json_decode($followed, false);

    $newBoard = new stdClass();
    $newBoard->name = $name;
    $newBoard->visits = 0;
    $followed[] = $newBoard;

    $followed = json_encode($followed, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $followed);
}

function unfollowBoard($name, $root = ".") {
    $file = $root."/data/users/".$_SESSION["user"]."/followed_boards.json";
    $followed = file_get_contents($file);
    $followed = json_decode($followed, false);

    for ($i = 0; $i < count($followed); $i++) {
        if ($followed[$i]->name == $name) {
            array_splice($followed, $i, 1);
            break;
        }
    }

    $followed = json_encode($followed, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $followed);
}