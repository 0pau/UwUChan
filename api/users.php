<?php

if (isset($_SESSION["extreme_debug_mode"])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ERROR);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}


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

function userExists($name, $root = "."): bool {

    if ($name == "SYSTEM") {
        return true;
    }

    return (file_exists("$root/data/users/".$name));
}

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

function changeUserField($field, $value, $root = "."): void {
    $file = $root."/data/users/".$_SESSION["user"]."/metadata.json";
    $userdata = file_get_contents($file);
    $userdata = json_decode($userdata, false);
    $userdata->$field = $value;
    $newdata = json_encode($userdata, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $newdata);
}

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

function rankcmp($a, $b): int {
    if ($a->visits < $b->visits) {
        return 1;
    } else if ($a->visits > $b->visits) {
        return -1;
    }
    return 0;
}

function getFollowedBoards($root = ".") {
    if (!isset($_SESSION["user"])) {
        return [];
    }
    $file = $root."/data/users/".$_SESSION["user"]."/followed_boards.json";
    $followed = file_get_contents($file);
    return json_decode($followed, false);
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

function getUserPosts($name, $root = ".") {
    $file = "$root/data/users/$name/posts.json";
    if (!file_exists($file)) {
        return [];
    }
    $data = file_get_contents($file);
    return json_decode($data, false);
}

function getUserComments($name, $root = ".") {
    $file = "$root/data/users/$name/comments.json";
    $data = file_get_contents($file);
    return json_decode($data, false);
}

function savePostToUser($name, $post_id, $root = "."): void {
    $file = "$root/data/users/$name/posts.json";
    $data = getUserPosts($name, $root);
    $data[] = $post_id;
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $data);
}

function saveCommentToUser($where, $comment_id, $root = "."): void {
    $file = "$root/data/users/".$_SESSION["user"]."/comments.json";
    $data = getUserComments($_SESSION["user"], $root);
    $data[] = "$where@$comment_id";
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $data);
}

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

function getPostAuthor($which, $root = ".") {
    $file = "$root/data/boards/$which.json";
    $data = file_get_contents($file);
    $data = json_decode($data, false);
    return $data->author;
}

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

function createThread($root = ".") : string {
    $id = uniqid();
    file_put_contents("$root/data/threads/$id.json", "[]");
    return $id;
}

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
