<?php

/**
 * Az "Extrém hibakereső mód" beállításait végzi el.
 * Ha be van állítva a hibakereső, akkor minden hiba (E_ALL) megjelenik, különben csak a súlyosabbak (E_ERROR).
 * <p><b>BUG</b><br>
 * Ha a hibakereső ki van kapcsolva, akkor a display_errors és a
 * display_startup_errors értékének 0-nak kéne lennie.
 * <ul><li>Verzió: v1.0-20240421</li></ul></p>
 */
if (isset($_SESSION["extreme_debug_mode"])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ERROR);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

/**
 * Ellenőrzi, hogy az adott e-mail címmel regisztrált -e már valaki.
 * <p><b>FONTOS: </b>A metódus feltételezi, hogy helyes e-mail címet kap.</p>
 * @param $email: Az e-mail cím
 * @return bool
 */
function isEmailUsed($email): bool {
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

/**
 * Ellenőrzi, hogy létezik -e a felhasználó.
 * @param $name: Felhasználó neve
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return bool
 */
function userExists($name, $root = "."): bool {

    if ($name == "SYSTEM") {
        return true;
    }

    return (file_exists("$root/data/users/".$name));
}

/**
 * Felhasználó adatait mentő metódus.
 * @param $user: Felhasználó adatait tartalmazó stdClass
 * @param $file: A $_FILES tömbből vett profilkép
 * @return bool: Ha sikeres volt akkor true
 */
function createUser($user, $file): bool {
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
    file_put_contents($dir."/comments.json", "[]");
    file_put_contents($dir."/followed_boards.json", "[]");
    file_put_contents($dir."/owned_boards.json", "[]");
    file_put_contents($dir."/friends.json", "[]");
    file_put_contents($dir."/posts.json", "[]");
    return true;
}

/**
 * Lekér egy mezőt a felhasználó metadata.json fájljából (pl. e-mail cím).
 * @param $field: Mező neve
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @param $name: Ha egy másik felhasználó adatát szeretnénk kérni, akkor annak a neve.
 * @return null
 */
function getUserField($field, $root = ".", $name = "\\") {
    $n = $name;
    if ($name == "\\") {
        $n = $_SESSION["user"];
    }
    if (userExists($n, $root)) {
        $json = file_get_contents("$root/data/users/$n/metadata.json");
        return json_decode($json, false)->$field;
    }
    return null;
}

/**
 * Megváltoztat egy mezőt a felhasználó metadata.json fájljában.
 * @param $field: Mező neve
 * @param $value: Mező új értéke
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return void
 */
function changeUserField($field, $value, $root = "."): void {
    $file = $root."/data/users/".$_SESSION["user"]."/metadata.json";
    $userdata = file_get_contents($file);
    $userdata = json_decode($userdata, false);
    $userdata->$field = $value;
    $newdata = json_encode($userdata, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $newdata);
}

/**
 * Lekéri egy felhasználónév profilképét beágyazható útvonalként. 4 viszatérési érték lehetséges:
 * 1. Ha a rendszerfelhasználó képét kérjük le, akkor egy fogaskereket ábrázoló kép útvonala
 * 2. Ha a felhasználó létezik és van képe, akkor az a kép.
 * 3. Ha a felhasználó létezik, de nincs képe, akkor az alap avatar.
 * 4. Ha a felhasználó nem létezik, akkor egy kérdőjelet ábrázoló kép útvonala
 * @param $name: A felhasználó neve
 * @return string
 */
function getUserProfilePicture($name): string {

    if ($name == "SYSTEM") {
        return "img/system.png";
    }

    if ($name != "" && userExists($name)) {
        if (getUserField("profilePictureFilename", ".", $name) != "") {
            return "data/images/" . getUserField("profilePictureFilename", ".", $name);
        }
    } else {
        return "img/unknown.png";
    }
    return "img/default_user_avatar.png";
}

/**
 * Visszaadja az összes regisztrált felhasználó számát
 * @return int
 */
function getUserCount() : int {
    if (!is_dir("data/users")) {
        return 0;
    }
    $users = 0;
    $dir = new DirectoryIterator("data/users");
    foreach ($dir as $info) {
        if (!str_starts_with($info->getBasename(), ".")) {
            $users++;
        }
    }
    return $users;
}

/**
 * Visszaadja azt, hogy az aktuális felhasználó mennyi üzenőfalat követ.
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return int
 */
function getFollowedCount($root = ".") : int {
    if (!isset($_SESSION["user"])) {
        return 0;
    }
    $file = $root."/data/users/".$_SESSION["user"]."/followed_boards.json";
    $followed = file_get_contents($file);
    $followed = json_decode($followed, false);
    return count($followed);
}

/**
 * Visszaad egy rendezett tömböt a felhasználó által követett üzenőfalak neveivel. A rendezési elv az, hogy a felhasználó
 * mennyiszer látogatta meg az adott üzenőfalat (visits mező értéke alapján)
 * @param $root
 * @return array|mixed
 */
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

/**
 * Két üzenőfal rangsorolásához szükséges metódus. A rendezési elv alapja a visits mező.
 * @param $a: Az egyik üzenőfal
 * @param $b: A másik üzenőfal
 * @return int
 */
function rankcmp($a, $b): int {
    if ($a->visits < $b->visits) {
        return 1;
    } else if ($a->visits > $b->visits) {
        return -1;
    }
    return 0;
}

/**
 * Visszaad egy tömböt a követett üzenőfalak stdObject-jeivel. Itt nincs rendezés.
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return array|mixed
 */
function getFollowedBoards($root = ".") {
    if (!isset($_SESSION["user"])) {
        return [];
    }
    $file = $root."/data/users/".$_SESSION["user"]."/followed_boards.json";
    $followed = file_get_contents($file);
    return json_decode($followed, false);
}

/**
 * Visszaadja, hogy az aktuális felhasználó követi -e az adott üzenőfalat
 * @param $name: Az üzenőfal neve
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return bool
 */
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

/**
 * Megnöveli eggyel az aktuális felhasználó followed_boards.json fájljában az adott üzenőfalhoz tartozó visits számlálót.
 * @param $name: Az üzenőfal neve
 * @return void
 */
function saveBoardVisit($name): void {
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

/**
 * Hozzáadja az adott nevű üzenőfalat a felhasználó folllowed_boards.json fájljához.
 * @param $name: Az üzenőfal neve
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return void
 */
function followBoard($name, $root = "."): void {
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

/**
 * Törli az adott nevű üzenőfalat a felhasználó folllowed_boards.json fájljából.
 * @param $name: Az üzenőfal neve
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return void
 */
function unfollowBoard($name, $root = "."): void {
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

/**
 * Lekéri egy felhasználó összes posztját üzenőfal/poszt_szám formában.
 * @param $name: A felhasználó neve
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozások hiánya miatt.
 * @return array
 */
function getUserPosts($name, $root = ".") {
    $file = "$root/data/users/$name/posts.json";
    if (!file_exists($file)) {
        return [];
    }
    $data = file_get_contents($file);
    return json_decode($data, false);
}

/**
 * Lekéri az összes kommentet, amit a felhasználó írt. A kommentek "üzenőfal/poszt_szám@komment_szám" formában vannak.
 * @param $name: A felhasználó neve
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return mixed
 */
function getUserComments($name, $root = ".") {
    $file = "$root/data/users/$name/comments.json";
    $data = file_get_contents($file);
    return json_decode($data, false);
}

/**
 * Amikor egy felhasználó posztot hoz létre, akkor azt el kell menteni a magához a felhasználóhoz is, hogy később az
 * adattörlésénél tudjuk, mely posztokat kell törölni.
 * @param $name: A felhasználó neve
 * @param $post_id: A poszt azonosítója üzenőfal/poszt_szám formában
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return void
 */
function savePostToUser($name, $post_id, $root = "."): void {
    $file = "$root/data/users/$name/posts.json";
    $data = getUserPosts($name, $root);
    $data[] = $post_id;
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $data);
}

/**
 * Amikor egy felhasználó kommentet hoz létre, akkor azt el kell menteni a magához a felhasználóhoz is, hogy később az
 * adattörlésénél tudjuk, mely kommenteket kell törölni.
 * @param $where: A poszt, ahol a komment van üzenőfal/poszt_szám formában
 * @param $comment_id: A komment azonosítója
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return void
 */
function saveCommentToUser($where, $comment_id, $root = "."): void {
    $file = "$root/data/users/".$_SESSION["user"]."/comments.json";
    $data = getUserComments($_SESSION["user"], $root);
    $data[] = "$where@$comment_id";
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $data);
}

/**
 * Ellenőrzi és visszaadja, hogy az adott poszt szerepel -e a felhasználó kedvelt posztjai között.
 * Ha igen, akkor a poszt indexét adja vissza, egyébként -1-et. Azért kell az index, mert a kedvelt posztokat egy tömbben
 * tároljuk, és így könnyen tudjuk törölni is, amikor a felhasználó már nem kedveli a posztot. (lásd. interactWithPost)
 * @param $which: A poszt azonosítója üzenőfal/poszt_szám formában
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return int|string
 */
function isPostLiked($which, $root = "."): int|string {
    $liked_posts = getUserField("liked_posts", $root);
    if ($liked_posts == null) {
        return -1;
    }
    $v = array_search($which, $liked_posts);
    if ($v === false) {
        return -1;
    } else {
        return $v;
    }
}

/**
 * Ellenőrzi és visszaadja, hogy az adott poszt szerepel -e a felhasználó nem kedvelt posztjai között.
 * Ha igen, akkor a poszt indexét adja vissza, egyébként -1-et. Azért kell az index, mert a nem kedvelt posztokat egy tömbben
 * tároljuk, és így könnyen tudjuk törölni is, amikor a felhasználó törli a dislike-ot. (lásd. interactWithPost)
 * @param $which: A poszt azonosítója üzenőfal/poszt_szám formában
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return int|string
 */
function isPostDisliked($which, $root = "."): int|string {
    $liked_posts = getUserField("disliked_posts", $root);
    if ($liked_posts == null) {
        return -1;
    }
    $v = array_search($which, $liked_posts);
    if ($v === false) {
        return -1;
    } else {
        return $v;
    }
}

/**
 * Egy posztra való reakció (like/dislike) esetén a felhasználó adatait és a poszt adatait is módosítani kell.
 * A felhasználó metaadatai között tároljuk, hogy mely posztokat kedveli, illetve melyeket nem.
 * Amikor egy posztot like-ol a felhasználó, de a poszt már dislike-olva van, akkor a dislike-ot törölni kell és
 * vice versa.
 * @param $where: A poszt azonosítója üzenőfal/poszt_szám formában
 * @param $action: A reakció típusa (like/dislike)
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return void
 */
function interactWithPost($where, $action, $root = "."): void {
    $post_list = getUserField($action."d_posts", $root);
    if ($post_list == null) {
        $post_list = [];
    }
    if ($action == "like") {
        $found = isPostLiked($where, $root);
        if (isPostDisliked($where, $root) != -1) {
            interactWithPost($where, "dislike", $root);
        }
    } else {
        $found = isPostDisliked($where, $root);
        if (isPostLiked($where, $root) != -1) {
            interactWithPost($where, "like", $root);
        }
    }
    $increment = 1;
    if ($found == -1) {
        $post_list[] = $where;
    } else {
        array_splice($post_list, $found, 1);
        $increment = -1;
    }
    changeUserField($action."d_posts", $post_list, $root);
    $file = "$root/data/boards/$where.json";
    $data = file_get_contents($file);
    $data = json_decode($data, false);
    if ($action == "like") {
        $data->likes += $increment;
    } else {
        $data->dislikes += $increment;
    }
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $data);
}

/**
 * Lehetőséget ad a felhasználónak, hogy megváltoztassa a felhasználónevét. A metódus átírja a felhasználó összes
 * posztjának és kommentjének a szerzőjét az új névre, valamint a felhasználó mappáját is átnevezi.
 * <p><b>BUG</b><br>
 * A metódus nem ellenőrzi, hogy az új név már létezik -e, így ha igen, akkor a másik felhasználó elveszíti az összes
 * posztját és kommentjét, valamint a felhasználó barátainál is eltűnik a kapcsolat.
 * <ul><li>Verzió: v1.0-20240421</li></ul></p>
 * @param $newName: Az új felhasználónév
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return void
 */
function changeUserName($newName, $root = "."): void {
    $currentName = $_SESSION["user"];
    $postList = getUserPosts($_SESSION["user"], $root);
    //Szerző átírása minden poszt metaadatában az új névre
    foreach ($postList as $post) {
        $file = "$root/data/boards/$post.json";
        $data = file_get_contents($file);
        $data = json_decode($data, false);
        $data->author = $newName;
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        file_put_contents($file, $data);
    }
    $commentList = getUserComments($_SESSION["user"], $root);
    //Szerző átírása minden egyes kommentben az új névre
    foreach ($commentList as $comment) {
        $comment = explode("@", $comment);
        $comment = $comment[0];
        $file = "$root/data/boards/$comment.json";
        $data = file_get_contents($file);
        $data = str_replace("\"username\":\"$currentName\"", "\"username\":\"$newName\"", $data);
        file_put_contents($file, $data);
    }
    changeUserField("nickname", $newName);
    rename("$root/data/users/$currentName", "$root/data/users/$newName");
    $_SESSION["user"] = $newName;
}

/**
 * Lekéri egy poszt szerzőjét.
 * @param $which: A poszt azonosítója üzenőfal/poszt_szám formában
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return mixed
 */
function getPostAuthor($which, $root = ".") {
    $file = "$root/data/boards/$which.json";
    $data = file_get_contents($file);
    $data = json_decode($data, false);
    return $data->author;
}

/**
 * Barátkérelmet küld egy felhasználónak. A kapcsolatokat a felhasználók friends.json fájljában tároljuk.
 * A kapcsolatokat a következőképpen kezeljük:
 * -1: Kiküldött barátkérelem
 * 0: Bejövő barátkérelem
 * 1: Elfogadott barátkérelem
 * 2: Blokkolt felhasználó
 * @param $to: A felhasználó, akinek küldjük a barátkérelmet
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return void
 */
function sendFriendRequest($to, $root = ".") {
    if (!userExists($to, $root)) {
        throw new Error("A felhasználó nem létezik.");
    }

    $my_friends = "$root/data/users/".$_SESSION["user"]."/friends.json";
    $myFriendsData = json_decode(file_get_contents($my_friends), false);
    $other_friends = "$root/data/users/$to/friends.json";
    $otherFriendsData = json_decode(file_get_contents($other_friends), false);

    $myEntry = new stdClass();
    $myEntry->username = $to;
    $myEntry->relationship = -1;
    $myEntry->thread = "";
    $myEntry->seenLastReaction = true;
    $myFriendsData[] = $myEntry;

    $otherEntry = new stdClass();
    $otherEntry->username = $_SESSION["user"];
    $otherEntry->relationship = 0;
    $otherEntry->thread = "";
    $otherEntry->seenLastReaction = false;
    $otherFriendsData[] = $otherEntry;

    file_put_contents($my_friends, json_encode($myFriendsData, JSON_UNESCAPED_UNICODE));
    file_put_contents($other_friends, json_encode($otherFriendsData, JSON_UNESCAPED_UNICODE));

}

/**
 * Törli a kapcsolatot a felhasználó friends.json fájljából és a másikéból is, így megszűnik a kapcsolat. Ezen
 * kívül törli a kapcsolathoz tartozó üzeneteket is.
 * @param $who: A felhasználó, akivel megszűnik a kapcsolat
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return void
 */
function removeFriend($who, $root = ".") {
    if (!userExists($who, $root)) {
        throw new Error("A felhasználó nem létezik.");
    }

    $my_friends = "$root/data/users/".$_SESSION["user"]."/friends.json";
    $myFriendsData = json_decode(file_get_contents($my_friends), false);
    $other_friends = "$root/data/users/$who/friends.json";
    $otherFriendsData = json_decode(file_get_contents($other_friends), false);

    for ($i = 0; $i < count($myFriendsData); $i++) {
        if ($myFriendsData[$i]->username == $who) {
            if ($myFriendsData[$i]->thread != "") {
                unlink("$root/data/threads/".$myFriendsData[$i]->thread.".json");
            }
            array_splice($myFriendsData, $i, 1);
            break;
        }
    }

    for ($i = 0; $i < count($otherFriendsData); $i++) {
        if ($otherFriendsData[$i]->username == $_SESSION["user"]) {
            array_splice($otherFriendsData, $i, 1);
            break;
        }
    }

    file_put_contents($my_friends, json_encode($myFriendsData, JSON_UNESCAPED_UNICODE));
    file_put_contents($other_friends, json_encode($otherFriendsData, JSON_UNESCAPED_UNICODE));

}

/**
 * Elfogadja a barátkérelmet és létrehoz egy új beszélgetést a felhasználóval.
 * @param $who: A felhasználó, akinek a barátkérelmét elfogadjuk
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return void
 */
function acceptFriendRequest($who, $root = ".") {

    if (!userExists($who, $root)) {
        throw new Error("A felhasználó nem létezik.");
    }

    $my_friends = "$root/data/users/".$_SESSION["user"]."/friends.json";
    $myFriendsData = json_decode(file_get_contents($my_friends), false);
    $other_friends = "$root/data/users/$who/friends.json";
    $otherFriendsData = json_decode(file_get_contents($other_friends), false);

    $threadId = createThread($root);

    for ($i = 0; $i < count($myFriendsData); $i++) {
        if ($myFriendsData[$i]->username == $who) {
            $myFriendsData[$i]->relationship = 1;
            $myFriendsData[$i]->thread = $threadId;
            $myFriendsData[$i]->seenLastInteraction = false;
            $myFriendsData[$i]->lastInteraction = time();
            break;
        }
    }
    for ($i = 0; $i < count($otherFriendsData); $i++) {
        if ($otherFriendsData[$i]->username == $_SESSION["user"]) {
            $otherFriendsData[$i]->relationship = 1;
            $otherFriendsData[$i]->thread = $threadId;
            $otherFriendsData[$i]->seenLastInteraction = false;
            $otherFriendsData[$i]->lastInteraction = time();
            break;
        }
    }

    file_put_contents($my_friends, json_encode($myFriendsData, JSON_UNESCAPED_UNICODE));
    file_put_contents($other_friends, json_encode($otherFriendsData, JSON_UNESCAPED_UNICODE));

}

/**
 * Blokkolja a felhasználót, így a két fél a tiltás feloldásáig nem tud egymással kommunikálni.
 * @param $who: A felhasználó, akit blokkolunk
 * @param $unblock: Ha igaz, akkor a felhasználó blokkolását feloldja
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return void
 */
function blockUser($who, $unblock = false, $root = ".") {
    if (!userExists($who, $root)) {
        throw new Error("A felhasználó nem létezik.");
    }

    $my_friends = "$root/data/users/".$_SESSION["user"]."/friends.json";
    $myFriendsData = json_decode(file_get_contents($my_friends), false);

    for ($i = 0; $i < count($myFriendsData); $i++) {
        if ($myFriendsData[$i]->username == $who) {
            $myFriendsData[$i]->relationship = ($unblock ? 1 : 2);
            break;
        }
    }

    file_put_contents($my_friends, json_encode($myFriendsData, JSON_UNESCAPED_UNICODE));

}

/**
 * Létrehoz egy véletlen uuid-nevű fájlt a threads mappában, amelyben a beszélgetés adatait tároljuk.
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return string: A beszélgetés azonosítója
 */
function createThread($root = ".") : string {
    $id = uniqid();
    file_put_contents("$root/data/threads/$id.json", "[]");
    return $id;
}

/**
 * Lekéri, hogy a felhasználó milyen kapcsolatban van a másik felhasználóval.
 * Ha a relative igaz, akkor csak azt adja vissza, hogy mi milyen kapcsolatban vagyunk a másikkal. Például, ha a másik
 * felhasználó letiltott minket, de mi őt nem, akkor 1-et ad vissza, mert a mi szemszögünkből ő a barátunk, viszont, ha
 * a relative hamis, akkor a másik felhasználó szemszögéből is vizsgálja a kapcsolatot.
 * @param $name: A felhasználó neve
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return mixed
 */
function getRelationship($with, $relative = false, $root = ".") : int {
    $my_friends = "$root/data/users/".$_SESSION["user"]."/friends.json";
    $myFriendsData = json_decode(file_get_contents($my_friends), false);

    $other_friends = "$root/data/users/$with/friends.json";
    $otherFriendsData = json_decode(file_get_contents($other_friends), false);

    $friend = false;
    for ($i = 0; $i < count($myFriendsData); $i++) {

        if ($relative && $myFriendsData[$i]->username == $with) {
            return $myFriendsData[$i]->relationship;
        }

        if ($myFriendsData[$i]->relationship == 1 && $myFriendsData[$i]->username == $with) {
            $friend = true;
        }
        if ($myFriendsData[$i]->relationship == 2 && $myFriendsData[$i]->username == $with) {
            return 2;
        }
    }
    if ($relative) {
        return -2;
    }
    for ($i = 0; $i < count($otherFriendsData); $i++) {
        if ($otherFriendsData[$i]->relationship == 2 && $otherFriendsData[$i]->username == $_SESSION["user"]) {
            return 2;
        }
        if ($otherFriendsData[$i]->relationship != 1 && $otherFriendsData[$i]->username == $_SESSION["user"]) {
            $friend = false;
        }
    }
    return $friend ? 1 : 0;
}

/**
 * Lekéri, hogy hány olvasatlan üzenete van a felhasználónak. Az olvasatlan üzenetekbe
 * beleértendők a barátkérelmek is.
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return int
 */
function getUnreadCount($root = ".") {
    $my_friends = "$root/data/users/".$_SESSION["user"]."/friends.json";
    $myFriendsData = json_decode(file_get_contents($my_friends), false);
    $unread = 0;
    for ($i = 0; $i < count($myFriendsData); $i++) {
        if ($myFriendsData[$i]->relationship == 0 || ($myFriendsData[$i]->relationship == 1 && !$myFriendsData[$i]->seenLastInteraction)) {
            $unread++;
        }
    }
    return $unread;
}

/**
 * Küld egy üzenetet a rendszer (SYSTEM) felhasználótól a megadott felhasználónak.
 * Ez a metódus akkor használatos, amikor a rendszer automatikusan küld üzenetet a felhasználónak, például
 * ha egy posztját bejelentették (nem lett implementálva), vagy éppen regisztrált a felhasználó.
 * A metódus lényegében létrehoz egy kapcsolatot SYSTEM és a felhasználó között, ha még nem létezik, majd
 * létrehoz egy új beszélgetést a felhasználóval, amelyben elküldi az üzenetet.
 * @param $to: A címzett felhasználó
 * @param $msg: Az üzenet szövege
 * @param $root: a gyökérkönyvtár, ahonnan el kell indulni az abszulút hivatkozás hiánya miatt.
 * @return void
 */
function sendSystemMessage($to, $msg, $root = ".") {

    error_reporting(E_ALL);

    $friends_file = "$root/data/users/$to/friends.json";

    if (!file_exists($friends_file)) {
        throw new Error("A fájl nem létezik!");
    }

    $friends_data = json_decode(file_get_contents($friends_file), false);

    $systemThread = "";
    $hasSystemAsFriend = false;
    foreach ($friends_data as $friend) {
        if ($friend->username == "SYSTEM") {
            $systemThread = $friend->thread;
            $hasSystemAsFriend = true;
            break;
        }
    }

    $friend_detail = new stdClass();
    $friend_detail->username = "SYSTEM";
    $friend_detail->relationship = 1;
    $friend_detail->lastInteraction = time();
    $friend_detail->seenLastInteraction = false;


    if ($systemThread == "") {
        $systemThread = createThread($root);
        $friend_detail->thread = $systemThread;
    } else {
        $friend_detail->thread = $systemThread;
    }

    if (!$hasSystemAsFriend) {
        $friends_data[] = $friend_detail;
    } else {
        for ($i = 0; $i < count($friends_data); $i++) {
            if ($friends_data[$i]->username == "SYSTEM") {
                $friends_data[$i] = $friend_detail;
                break;
            }
        }
    }

    file_put_contents($friends_file, json_encode($friends_data, JSON_UNESCAPED_UNICODE));

    $thread_data = json_decode(file_get_contents("$root/data/threads/$systemThread.json"), false);
    if ($thread_data == null) {
        $thread_data = [];
    }

    $message = new stdClass();
    $message->id = uniqid();
    $message->username = "SYSTEM";
    $message->text = $msg;
    $postedAtField = "posted-at";
    $message->$postedAtField = time();
    $message->unsent = false;

    $thread_data[] = $message;

    file_put_contents("$root/data/threads/$systemThread.json", json_encode($thread_data,JSON_UNESCAPED_UNICODE));

}
