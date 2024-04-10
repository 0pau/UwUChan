<?php
function saveImage($ext, $tmpFileName) : String {

    if (!is_dir("data/images")) {
        mkdir("data/images");
    }
    $filename = uniqid().".".$ext;
    move_uploaded_file($tmpFileName, "data/images/".$filename);

    return $filename;
}