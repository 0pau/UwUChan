<?php
/**
 * Közzétesz egy hozzászólást az adott poszt és (opcionálisan) komment alá. 2 féle eset lehetséges:
 * 1. A hozzászólás a poszt alá kerül, tehát a comments tömbbe kerül. Ekkor a replyTo értéke null.
 * 2. A hozzászólás egy másik hozzászólás alá kerül, a comments tömben egy másik hozzászólás replies tömbjébe kerül, amit a findComment függvény keres meg.
 * @param $where: A poszt azonosítója üzenőfal/poszt_száma formátumban
 * @param $text: A komment
 * @param $replyTo: A komment, amire válaszol, ha null, akkor a poszt alá kerül az új hozzásszólás
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return bool
 */
function postComment($where, $text, $replyTo, $root = ".") {

    $file = "$root/data/boards/$where.json";

    if (!file_exists($file)) {
        return false;
    }

    $data = file_get_contents($file);
    $data = json_decode($data, false);

    $newComment = new stdClass();
    $newComment->id = uniqid();
    $newComment->username = $_SESSION["user"];
    $newComment->text = $text;
    $newComment->posted_at = time();
    $newComment->replies = [];

    if ($replyTo == null) {
        $data->comments[] = $newComment;
    } else {
        for ($i = 0; $i < count($data->comments); $i++) {
            if (findComment($replyTo, $data->comments[$i], $newComment)) break;
        }
    }

    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    file_put_contents("$root/data/boards/$where.json", $data);

    saveCommentToUser($where, $newComment->id, $root);

    return true;
}

/**
 * Rekurzív függvény, amely végigjárja a kommenteket, és ha megtalálja a megfelelőt, akkor hozzáfűzi a választ, emiatt
 * a paraméterek referenciaként vannak átadva.
 * <br><b>FONTOS: Hiányzik a replies-hoz egy null check, így ha nem létezik a replies tömb, akkor hibára fut!</b>
 * @param $id: A komment azonosítója (uniqid)
 * @param $comment: Az adott komment objektum, amiben keressük azt a kommentet, amire válaszolni akarunk
 * @param $newComment: A komment szövege
 * @return bool
 */
function findComment(&$id, &$comment, &$newComment) : bool {
    if ($id == $comment->id) {
        $comment->replies[] = $newComment;
        return true;
    }
    for ($i = 0; $i < count($comment->replies); $i++) {
        if (findComment($id, $comment->replies[$i], $newComment)) return true;
    }
    return false;
}

/**
 * Lekéri egy poszt összes hozzászólását. (Valójában a poszt json-fájljában található comments tömböt)
 * @param $where: A poszt azonosítója üzenőfal/poszt_száma formátumban
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return false
 */
function getComments($where, $root = ".") {

    $file = "$root/data/boards/$where.json";
    if (!file_exists($file)) {
        return false;
    }
    $data = file_get_contents($file);
    $data = json_decode($data, false);

    return $data->comments;
}
/**
 * A hozzászólásokat tartalmazó tömböt menti az adott poszt json-fájljába.
 * @param $where: A poszt azonosítója üzenőfal/poszt_száma formátumban
 * @param $object: A kommenteket tartalmazó tömb
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return void
 */
function saveComments($where, $object, $root = ".") {
    $file = "$root/data/boards/$where.json";
    $data = file_get_contents($file);
    $data = json_decode($data, false);
    $data->comments = $object;
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $data);
}

/**
 * Egy addot komment objektumot HTML-formában kirenderel. A válaszokat rekurzívan hívja meg.
 * @param $comment: Az adott komment objektuma
 * @return void
 */
function printComment($comment) {

    $l = "href=\"profile-other.php?n=$comment->username\"";
    if ($comment->username == "") {
        $l = "";
        $comment->username = "[törölt]";
    }

    echo "<div class=\"post-comment-card\" id=\"".$comment->id."\">
            <div class=\"card-head\"><a $l>
                    <img class=\"post-profile-messages-avatar\" src=\"".getUserProfilePicture($comment->username)."\" alt=\"Profilkép\">
                    <span>$comment->username</span>
                    <span class=\"post-profile-message-sent-time\">".date("Y. m. d. H:i", $comment->posted_at)."</span>
                </a>
                <button onclick=\"showReplyUI('".$comment->id."', '".$comment->username."')\" class=\"flat right icon\"><span class=\"material-symbols-rounded\">reply</span></button>
            </div>
            <p>$comment->text</p><div class='comment-thread'>";
        foreach ($comment->replies as $c) {
            printComment($c);
        }
        echo "</div></div>";
}