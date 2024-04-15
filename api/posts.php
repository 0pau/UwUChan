<?php
    include_once "boards.php";

    function getPostCard($which, $root = ".") : bool {

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
                        echo "<img src=\"$th\" alt=\"$title\">";
                    }
                    $count = count($post->images);

                    echo "</div><p>$count kép</p></div>";
                }
            }
            echo "<div class=\"post-fragment\">
                            <a href=\"post.php?n=$which\" class=\"post-body\">
                                <p class=\"post-title\">$post->title</p>
                                <p class=\"post-text\">$post->body</p>
                            </a>
                            <div class=\"reaction-bar\">
                                <a class=\"button flat\" href=\"post.php\"><span class=\"material-symbols-rounded\">forum</span>".count($post->comments)."</a>
                                <button class=\"flat right\"><span class=\"material-symbols-rounded\">thumb_up</span>$post->likes</button>
                                <button class=\"flat\"><span class=\"material-symbols-rounded\" >thumb_down</span>$post->dislikes</button>
                            </div>
                        </div>
                    </div>
                </div>";
        return true;
    }