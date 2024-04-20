<?php
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

    function getComments($where, $root = ".") {

        $file = "$root/data/boards/$where.json";
        if (!file_exists($file)) {
            return false;
        }
        $data = file_get_contents($file);
        $data = json_decode($data, false);

        return $data->comments;
    }

    function saveComments($where, $object, $root = ".") {
        $file = "$root/data/boards/$where.json";
        $data = file_get_contents($file);
        $data = json_decode($data, false);
        $data->comments = $object;
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        file_put_contents($file, $data);
    }

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
                <button class=\"flat icon\"><span class=\"material-symbols-rounded\">flag</span></button>
                </div>
                <p>$comment->text</p><div class='comment-thread'>";
            foreach ($comment->replies as $c) {
                printComment($c);
            }
            echo "</div></div>";
    }