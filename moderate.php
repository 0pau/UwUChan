<?php
session_start();
include "api/users.php";
include "api/boards.php";
include "api/posts.php";

$error = "";

$offset = 0;
if (isset($_GET["o"])) {
    $offset = intval($_GET["o"]);
}
if ($offset < 0) {
    $offset = 0;
}
?>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>UwUChan</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/mobile.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body class="<?php include "api/theme.php"?>">
<main>
    <?php include "views/header.php" ?>
    <div class="main-flex">
        <?php include "views/sidebar.php"?>
        <section>
            <div class="section-head">
                <h1>Bejelentés részletei</h1>
            </div>

            <?php
            $root = ".";
            $board = $_GET['board'] ?? 'defaultBoard';
            $postID = $_GET['postID'] ?? 'defaultPost';
            $reportNumber = $_GET['reportNumber'] ?? '1';

            $message = "";

            $reportFile = "$root/data/reports/$board/$postID/$reportNumber/report.json";
            $postPath = "$root/data/boards/$board/$postID.json";

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $content_status = $_POST['content_status'] ?? 'inappropriate';

                if ($content_status === 'appropriate') {
                    if (file_exists($postPath)) {
                        $post = json_decode(file_get_contents($postPath), true);
                        $post['hidden'] = false;
                        file_put_contents($postPath, json_encode($post));
                        $message = "<p class=\"success\"><span class=\"material-symbols-rounded\">check_circle</span>A tartalom megfelelt az UwUChan szabályainak, ezért vissza lett állítva.</p>";
                    }
                } elseif ($content_status === 'inappropriate') {
                    deletePost("$board/$postID", $root);
                    $message = "<p class=\"success\"><span class=\"material-symbols-rounded\">check_circle</span>A tartalom megsértette az UwUChan szabályait, a posztot töröltük.</p>";
                }

                if (file_exists($reportFile)) {
                    $reportData = json_decode(file_get_contents($reportFile), true);
                    $reportData['moderate'] = true;
                    file_put_contents($reportFile, json_encode($reportData));
                }

                if ($message) {
                    echo $message;
                }
                return;
            }

            if (file_exists($reportFile)) {
                $reportData = json_decode(file_get_contents($reportFile), true);
                $reportedUsername = $reportData['username'];
                if (file_exists($postPath)) {
                    echo "<p class='card-header'>Bejelentett tartalom</p>";
                    getPostCard("$board/$postID", true, $root);
                } else {
                    echo "<p>Poszt nem található.</p>";
                }

                ?>
                <div class='report-details'>
                    <p class='card-header'>Részletek</p>
                    <div class='report-container'>
                        <div class='report-header'>
                            <div class='report-row'><span class='report-label'>Bejelentő:</span> <span class='report-value'><?= htmlspecialchars($reportedUsername) ?></span></div>
                            <div class='report-row'><span class='report-label'>Bejelentés oka:</span> <span class='report-value'><?= getViolationReason($reportData['content']['violation']) ?></span></div>
                            <div class='comment-box'><p class='report-label'>Megjegyzés:</p><p><?= htmlspecialchars($reportData['content']['comment']) ?></p></div>
                        </div>
                    </div>
                    <p class='card-header'>Tartalom elbírálása</p>
                    <div class="report-container">
                        <form action='moderate.php?board=<?= urlencode($board) ?>&postID=<?= urlencode($postID) ?>&reportNumber=<?= urlencode($reportNumber) ?>' method='post' class='moderation-form'>
                            <div class='radio-group'>
                                <label class='radio-label'><input type='radio' name='content_status' value='appropriate'> A tartalom megfelel az UwUChan szabályainak (visszaállítás)</label>
                                <label class='radio-label'><input type='radio' name='content_status' value='inappropriate' checked> A bejelentés jogos</label>
                            </div>
                            <div class="button-box">
                                <button class='button cta right'>Moderálás végrehajtása</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
            } else {
                echo "<p>A keresett report nem található.</p>";
            }
            ?>



        </section>
    </div>
</main>
</body>
</html>