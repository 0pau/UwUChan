<?php
    include_once "boards.php";
    include_once "users.php";

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
                <span class='posted-at-text'>".gmdate("m. d. H:i", $post->posted_at)."</span>
                <a class=\"right button icon flat\" href=\"$root/report.php?w=$which\"><span class=\"material-symbols-rounded\">emoji_flags</span></a>
            </div>
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
                            <a href=\"post.php?n=$which\" class=\"post-body\">
                                <p class=\"post-title\">$post->title</p>
                                <p class=\"post-text\">$post->body</p>
                            </a>";
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