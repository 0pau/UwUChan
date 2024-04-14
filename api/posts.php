<?php

    function getPostCard($which, $root = ".") : bool {

        $file = $root."/data/boards/$which.json";
        if (!file_exists($file)) {
            return false;
        }

        $post = file_get_contents($file);
        $post = json_decode($post, false);

        echo "
        <div class=\"post-card\">
            <div class=\"card-head\">
                <a href=\"profile-other.php?n=$post->author\">
                    <img class=\"user-profile-blog-avatar\" src=\"img/default_user_avatar.png\" alt=\"Profilkép\">
                    <span>$post->author</span>
                </a>
                <span class=\"material-symbols-rounded\">arrow_right</span>
                <a href=\"board.html\">
                    <img class=\"user-profile-blog-avatar\" src=\"img/minta_macsek.jpg\" alt=\"macskak\">
                    <span>macskak</span>
                </a>
                <a class=\"right button icon flat\" href=\"$root/report.php?w=$which\"><span class=\"material-symbols-rounded\">emoji_flags</span></a>
            </div>
            <div class=\"post-content\">";
            if (count($post->images)) {
                echo "<a class=\"post-images\" href=\"index.php\">
                    <img src=\"./img/blog_macska.jpg\" alt=\"macska\">
                    <p>DSC_3829.jpg</p>
                </a>";
            }
            echo "<div class=\"post-fragment\">
                            <a href=\"post.php\" class=\"post-body\">
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