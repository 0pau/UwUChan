<?php
function saveImage($ext, $tmpFileName, $root = ".") : String {

    if (!is_dir($root."/data/images")) {
        mkdir($root."/data/images");
    }
    $filename = uniqid().".".$ext;

    if (move_uploaded_file($tmpFileName, $root."/data/images/".$filename)) {
        return $filename;
    }
    return "";
}

function saveImageWithThumbnail($ext, $tmpFileName, $root = ".") {
    $original = saveImage($ext, $tmpFileName, $root);

    $hasGD = get_extension_funcs("gd");

    if ($hasGD) {
        $size = getimagesize($root . "/data/images/" . $original);
        $frac = $size[0] / 200;
        $w = (int)$size[0] / $frac;
        $h = (int)$size[1] / $frac;
        $tn = imagecreatetruecolor($w, $h);
        $image = "";

        if ($ext == "png") {
            $image = imagecreatefrompng($root . "/data/images/" . $original);
        } else if ($ext == "jpg" || $ext == "jpeg") {
            $image = imagecreatefromjpeg($root . "/data/images/" . $original);
        } else if ($ext == "webp") {
            $image = imagecreatefromwebp($root . "/data/images/" . $original);
        }
        imagecopyresampled($tn, $image, 0, 0, 0, 0, $w, $h, $size[0], $size[1]);
        imagepng($tn, $root . "/data/images/th_$original.png", 9);
        return [$original, "th_$original.png"];
    }

    return [$original, $original];
}

function checkEmail($address): bool|string {
    $address = trim($address);
    if (preg_match("/^[a-z0-9_.-]+@[a-z0-9_.-]+\.[a-z]{2,5}$/", $_POST["email"]) != 0) {
        return $address;
    }
    return false;
}

function checkBirthday($birthday) : bool|string {
    $today = strtotime(date("Y-m-d"));
    $birthday = strtotime($birthday);

    if ($today-$birthday >= (13*365*24*60*60)) {
        return gmdate("Y-m-d", $birthday);
    }

    return false;
}

function checkUsername($username) : bool|string {
    $username = trim($username);
    if (preg_match("/[A-Za-z0-9_.-]{3,32}/", $username) != 0) {
        return $username;
    }
    return false;
}

function checkPassword($password) : bool|string {
    $password = trim($password);
    if (preg_match("/^[A-Za-z0-9#&@.,:?\"+_-]{8,64}$/", $password) != 0) {
        return $password;
    }
    return false;
}

function isGDAvailable() : bool {
    if (get_extension_funcs("gd")) {
        return true;
    }
    return false;
}

