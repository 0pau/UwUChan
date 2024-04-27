<?php

/**
 * A hónapneveket magyarul rövidítve tartalmazó tömb.
 */
const MONTHS = ["jan.", "feb.", "márc.", "ápr.", "máj.", "jún.", "júl.", "aug.", "szept.", "okt.", "nov.", "dec."];

/**
 * Kép mentéséért felelős metódus. Ha sikeres volt a mentés, akkor a kép nevét adja vissza, egyébként üres stringet.
 * @param $ext: A kép kiterjesztése
 * @param $tmpFileName: Az ideiglenes fájl neve, ami a $_POST["file"]-ban található
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return String
 */
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

/**
 * Kép mentéséért felelős metódus, ami egy thumbnail-t is készít a képből.
 * Ha sikeres volt a mentés, akkor egy kételemű tömböt ad vissza,
 * amiben az első elem az eredeti kép neve, a második pedig a thumbnail neve. HA nem sikerült a thumbnail készítése,
 * akkor az eredeti kép nevét adja vissza kétszer.
 * @param $ext: A kép kiterjesztése
 * @param $tmpFileName: Az ideiglenes fájl neve, ami a $_POST["file"]-ban található
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return array|String[]
 */
function saveImageWithThumbnail($ext, $tmpFileName, $root = ".") {
    $original = saveImage($ext, $tmpFileName, $root);

    if (isGDAvailable()) {
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

/**
 * A paraméterben kapott e-mail címet ellenőrzi regex alapján. Ha az e-mail cím megfelelő, visszatér az e-mail címmel
 * egyébként false-ot ad vissza.
 * @param $address: Az e-mail cím
 * @return bool|string
 */
function checkEmail($address): bool|string {
    $address = validateText($address);
    if (!$address) {
        return false;
    }
    if (preg_match("/^[a-z0-9_.-]+@[a-z0-9_.-]+\.[a-z]{2,5}$/", $_POST["email"]) != 0) {
        return $address;
    }
    return false;
}

/**
 * Ellenőrzi, hogy a paraméterben kapott születésnap szerint a felhasználó 13 éves elmúlt -e.
 * Ha igen, akkor a születésnapot adja vissza, egyébként false-ot.
 * @param $birthday
 * @return bool|string
 */
function checkBirthday($birthday) : bool|string {
    $today = strtotime(date("Y-m-d"));
    $birthday = strtotime($birthday);

    if ($today-$birthday >= (13*365*24*60*60)) {
        return date("Y-m-d", $birthday);
    }

    return false;
}

/**
 * Ellenőrzi, hogy a paraméterben kapott felhasználónév megfelel-e a követelményeknek. Ha igen, akkor a felhasználónevet
 * adja vissza, egyébként false-ot.
 * @param $username: A felhasználónév
 * @return bool|string
 */
function checkUsername($username) : bool|string {
    $username = validateText($username);
    if (!$username) {
        return false;
    }
    if (preg_match("/[A-Za-z0-9_.-]{3,32}/", $username) != 0) {
        return $username;
    }
    return false;
}

/**
 * Ellenőrzi, hogy a paraméterben kapott jelszó megfelel-e a követelményeknek. Ha igen, akkor a jelszót adja vissza,
 * egyébként false-ot.
 * @param $password: A jelszó
 * @return bool|string
 */
function checkPassword($password) : bool|string {
    $password = trim($password);
    if (preg_match("/^[A-Za-z0-9#&@.,:?\"+_-]{8,64}$/", $password) != 0) {
        return $password;
    }
    return false;
}

/**
 * Ellenőrzi, hogy a paraméterben kapott szöveg nem -e üres, és nem tartalmaz-e HTML kódot.
 * Ha megfelelő, akkor kicseréli az egyes speciális karaktereket HTML kódokra, és a sortöréseket <br> tagokra.
 * @param $text: A szöveg
 * @return bool|string
 */
function validateText($text) : bool|string {

    $text = trim($text);

    if ($text == "" || preg_match("/^(?!.*<\/?.*>).*$/m", $text) == 0) {
        return false;
    }

    $text = str_replace("<", "&lt;", $text);
    $text = str_replace(">", "&gt;", $text);
    $text = str_replace("\n", "<br>", $text);

    return $text;

}

/**
 * Ellenőrzi, hogy a PHP GD könyvtára elérhető-e, ami a képek feldolgozásához szükséges.
 * @return bool
 */
function isGDAvailable() : bool {
    if (get_extension_funcs("gd")) {
        return true;
    }
    return false;
}

/**
 * A megadott unix időbélyeget formázza a pillanatnyi időhöz képesti relatív módon, tehát:
 * - Ha a timestamp a jövőben van, akkor "Valamikor a jövőben :D"
 * - Ha a timestamp ma van, akkor: "Épp most", "x perce", "x órája"
 * - Ha a timestamp tegnap volt, akkor: "Tegnap {óra perc}"
 * - Ha a timestamp az idén volt, akkor: "hónap nap {óra perc}"
 * - Egyébként: "év. hónap. nap. {óra perc}"
 * @param $timestamp: A timestamp
 * @param bool $showTime: Ha true, akkor a dátum után kiírja az időt is
 * @return bool
 */
function formatDateRelative($timestamp, $showTime = true) {

    $now = time();

    if ($timestamp > $now) {
        return "Valamikor a jövőben :D";
    }

    if (date("Y-m-d", $timestamp) == date("Y-m-d")) {
        if ($now - $timestamp < 60) {
            return "Épp most";
        } else if ($now - $timestamp < 3600) {
            return ((int)(($now-$timestamp)/60))." perce";
        } else {
            return ((int)(($now-$timestamp)/3600))." órája";
        }
    } else if (date("Y-m", $timestamp) == date("Y-m") && intval(date("d", $timestamp)) == intval(date("d"))-1) {
        return "Tegnap".($showTime ? " ".date("H:i", $timestamp) : "");
    } else if (date("Y", $timestamp) == date("Y")) {
        return MONTHS[intval(date("m", $timestamp))-1]." ".date("j.".($showTime ? " H:i" : ""), $timestamp);
    } else {
        return date("Y. m. d.".($showTime ? " H:i" : ""), $timestamp);
    }

}