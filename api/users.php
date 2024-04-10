<?php
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

function getUserField($field) {
    if (isset($_SESSION["user"])) {
        $json = file_get_contents("data/users/".$_SESSION["user"]."/metadata.json");
        return json_decode($json, JSON_UNESCAPED_UNICODE)[$field];
    }
    return null;
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