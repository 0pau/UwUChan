<?php
    include_once "boards.php";
    include_once "users.php";
    include_once "util.php";

    function getPostCard($which, $isPreview = false, $root = ".") : bool {

        $file = $root."/data/boards/$which.json";
        if (!file_exists($file)) {
            return false;
        }

        $board = explode("/", $which);
        $board_name = $board[0];

        $post = file_get_contents($file);
        $post = json_decode($post, false);

        echo "
        <div class=\"post-card\">
            <div class=\"card-head\">
                <a href=\"profile-other.php?n=$post->author\">
                    <img class=\"user-profile-blog-avatar\" src=\"".getUserProfilePicture($post->author)."\" alt=\"$post->author profilképe\">
                    <span>$post->author</span>
                </a>
                <span class=\"material-symbols-rounded\">arrow_right</span>
                <a href=\"board.php?n=$board_name\">
                    <img class=\"user-profile-blog-avatar\" src=\"".getBoardIcon($board_name)."\" alt=\"$board_name\">
                    <span>$board_name</span>
                </a>
                <span class='posted-at-text'>".formatDateRelative($post->posted_at, true)."</span>";
        if (!$isPreview) {
            echo "<a class=\"right button icon flat\" href=\"$root/report.php?w=$which\"><span class=\"material-symbols-rounded\">emoji_flags</span></a>";
        }
        if (isset($_SESSION["extreme_debug_mode"])) {
            echo "<span>$which</span>";
        };
            echo "</div>
            <div class=\"post-content\">";
            if (count($post->images)) {
                if (count($post->images) == 1) {
                    $th = "$root/data/images/".$post->images[0]->thumbnail;
                    $title = $post->images[0]->title;
                    echo "<a class=\"post-images\" href=\"index.php\">
                    <img src=\"$th\" alt=\"$title\">
                    <p>$title</p>
                </a>";
                } else {
                    echo "<div class=\"post-images\"><div class=\"post-image-stack\">";

                    foreach ($post->images as $image) {
                        $th = "$root/data/images/".$image->thumbnail;
                        $title = $image->title;
                        echo "<a href=\"data/images/$image->original\"><img src=\"$th\" alt=\"$title\"></a>";
                    }
                    $count = count($post->images);

                    echo "</div><p>$count kép</p></div>";
                }
            }
            $l = "accentFg";
            $d = $l;
            if (isset($_SESSION["user"])) {
                if (isPostLiked($which, $root) == -1) {
                    $l = "";
                }
                if (isPostDisliked($which, $root) == -1) {
                    $d = "";
                }
            } else {
                $l = "";
                $d = "";
            }

            echo "<div class=\"post-fragment\">
                            <div class=\"post-body\">
                                <a href=\"post.php?n=$which\" class=\"post-title\">$post->title</a>
                                <p class=\"post-text\">$post->body</p>
                            </div>";
            if (!$isPreview) {
                echo "<div class=\"reaction-bar\">
                                <a class=\"button flat disabled\" href=\"post.php\"><span class=\"material-symbols-rounded\">forum</span>" . count($post->comments) . "</a>
                                <form class=\"right\" action=\"api/post_actions.php\" method=\"POST\">
                                    <input type=\"hidden\" name=\"action\" value=\"like\">
                                    <input type=\"hidden\" name=\"data\" value=\"$which\">
                                    <button class=\"flat $l\"><span class=\"material-symbols-rounded\">thumb_up</span>$post->likes</button>
                                </form>
                                <form action=\"api/post_actions.php\" method=\"POST\">
                                    <input type=\"hidden\" name=\"action\" value=\"dislike\">
                                    <input type=\"hidden\" name=\"data\" value=\"$which\">
                                    <button class=\"flat $d\"><span class=\"material-symbols-rounded\" >thumb_down</span>$post->dislikes</button>
                                </form>
                            </div>";
            }
            echo "</div></div></div>";
        return true;
    }

    function uploadPost($root = ".") {

        if ($_SERVER["CONTENT_LENGTH"] > 8388608) {
            throw new Error("A poszt összmérete meghaladja a 8 megabájtot.");
        }

        if (!isset($_SESSION["user"])) {
            throw new Error("Nincs hozzáférésed ehhez a végponthoz.");
        }

        if (!isset($_POST["board-name"]) || !isset($_POST["post-title"]) || !isset($_POST["post-body"])) {
            throw new Error("Hiba történt, fordulj a fejlesztőkhöz!");
        }

        $board = $_POST["board-name"];
        $title = validateText($_POST["post-title"]);
        $body = validateText($_POST["post-body"]);

        if (!$title || !$body) {
            throw new Error("A poszt címe és tartalma nem lehet üres és nem tartalmazhat HTML kódot.");
        }

        if (!boardExists($board, $root)) {
            throw new Error("A megadott üzenőfal nem létezik.");
        }

        $words = explode(" ", $body);

        $body = "";
        foreach ($words as $word) {
            if (str_starts_with($word, "http://")||str_starts_with($word, "https://")||str_starts_with($word, "ftp://")) {
                $word = "<a href=\"$word\">$word</a>";
            }
            $body = $body." ".$word;
        }

        $post = new stdClass();
        $post->title = $title;
        $post->body = $body;
        $post->author = $_SESSION["user"];
        $post->likes = 0;
        $post->dislikes = 0;
        $post->posted_at = time();

        $images = [];

        if (count($_FILES["images"]["name"]) != 0) {
            for ($i = 0; $i < count($_FILES["images"]["name"]); $i++) {

                if ($_FILES["images"]["name"][$i] == "") {
                    continue;
                }

                if ($_FILES["images"]["size"][$i] == 0) {
                    throw new Error("A(z) ".$_FILES["images"]["name"][$i]." kép mérete meghaladja a 2 megabájtot.");
                }

                $img = new stdClass();
                $img->title = $_FILES["images"]["name"][$i];
                $fileSeparated = explode(".", $img->title);
                $ext = end($fileSeparated);

                $filenames = saveImageWithThumbnail($ext, $_FILES["images"]["tmp_name"][$i], $root);
                $img->original = $filenames[0];
                $img->thumbnail = $filenames[1];

                $images[] = $img;
            }
        }

        $post->images = $images;
        $post->comments = [];

        $data = json_encode($post, JSON_UNESCAPED_UNICODE);

        $boardInfo = getBoardInfo($board, $root);
        $number = $boardInfo->post_count + 1;
        $boardInfo->post_count = $number;

        saveBoardInfo($board, $boardInfo, $root);
        savePostToUser($_SESSION["user"], "$board/$number", $root);

        changeUserField("uwuness", getUserField("uwuness", $root)+1, $root);

        file_put_contents("$root/data/boards/$board/$number.json", $data);

        if (!file_exists("$root/data/post_activity.dat")) {
            file_put_contents("$root/data/post_activity.dat", "");
        }
        $f = file_get_contents("$root/data/post_activity.dat");
        $f = "$board/$number\n".$f;
        file_put_contents("$root/data/post_activity.dat", $f);

        header("Location: $root/post.php?n=$board/$number");

    }
