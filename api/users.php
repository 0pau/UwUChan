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

function user_exists($name, $root = "."): bool {
    return (file_exists("$root/data/users/".$name));
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

    if (user_exists($n, $root)) {
        $json = file_get_contents("$root/data/users/$n/metadata.json");
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

    if ($name != "" && user_exists($name)) {
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
    $data = json_decode($data, false);
    return $data;
}

function savePostToUser($name, $post_id, $root = ".") {
    $file = "$root/data/users/$name/posts.json";

    $data = getUserPosts($name, $root);
    $data[] = $post_id;

    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $data);
}

function saveCommentToUser($where, $comment_id, $root = ".") {
    $file = "$root/data/users/".$_SESSION["user"]."/comments.json";

    $data = getUserComments($_SESSION["user"], $root);
    $data[] = "$where@$comment_id";

    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $data);
}

function isPostLiked($which, $root = ".") {
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

function isPostDisliked($which, $root = ".") {
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

function interactWithPost($where, $action, $root = ".") {
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

function changeUserName($newName, $root = ".") {

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

