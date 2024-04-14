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

    $size = getimagesize($root."/data/images/".$original);
    $frac = $size[0]/200;
    $w = (int)$size[0]/$frac;
    $h = (int)$size[1]/$frac;
    $tn = imagecreatetruecolor($w, $h);
    $image = "";

    if ($ext == "png") {
        $image = imagecreatefrompng($root."/data/images/".$original);
    } else if ($ext == "jpg" || $ext == "jpeg") {
        $image = imagecreatefromjpeg($root."/data/images/".$original);
    } else if ($ext == "webp") {
        $image = imagecreatefromwebp($root."/data/images/".$original);
    }
    imagecopyresampled($tn, $image, 0,0,0,0, $w, $h, $size[0], $size[1]);
    imagepng($tn, $root."/data/images/th_$original.png", 9);

    return [$original, "th_$original.png"];
}