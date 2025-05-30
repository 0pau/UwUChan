<?php

/**
<p><b>API végpont: </b><i>HTML formból, vagy JavaScriptből hívható</i></p>
<h1>Refresh activity</h1>
A post_activity.dat fájl tartalmazza az UwUChan összes posztját időrendben (legújabbtól a legrégebbiig). Ez az API-végpont
e fájl frissítésére szolgál.
<p>Működés: Végigmegy az összes üzenőfal összes posztján, majd ezeket időrendben rendezi (compare_posts) és elmenti a
post_activity.dat fájlba.</p>
 */

    session_start();
    include "users.php";
    include "boards.php";

    try {
        if (!isset($_SESSION["user"]) || getUserField("privilege", "..") < 1) {
            header("Location: ../403.html");
            exit;
        }

        $file = fopen("../data/post_activity.dat", "w");

        $posts = [];

        $boards = getRandomBoards("..");
        foreach ($boards as $board) {
            $dir = new DirectoryIterator("../data/boards/$board");
            foreach ($dir as $info) {
                if (!$info->isDot() && $info->getBasename() != "metadata.json") {
                    $posts[] = $board . "/" . str_replace(".json", "", $info->getBasename());
                }
            }
        }

        usort($posts, "compare_posts");

        foreach ($posts as $post) {
            fprintf($file, "%s\n", $post);
        }

        fclose($file);

        echo "A frissítés befejeződött.";

        $referer = $_SERVER["HTTP_REFERER"];
        header("Location: $referer");

    } catch (Error $e) {
        echo $e;
    }

    function compare_posts($a, $b) {
        $file1 = "../data/boards/$a.json";
        $file2 = "../data/boards/$b.json";

        $file1 = json_decode(file_get_contents($file1));
        $file2 = json_decode(file_get_contents($file2));

        $a = $file1->posted_at;
        $b = $file2->posted_at;

        return $a < $b;
    }