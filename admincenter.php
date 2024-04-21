<?php session_start();
include "api/users.php";
include "api/acl.php";
include "api/acl.php";
include "api/boards.php";
include "api/posts.php";
?>

<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Admin Központ - UwUChan</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/mobile.css">
        <link rel="stylesheet" href="css/admin.css">
        <link rel="stylesheet" href="css/thread.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php include "views/sidebar.php"?>
                <section>

                    <div class="section-head">
                        <h1>Admin központ</h1>
                    </div>

                    <?php if (getUserField("privilege") == 1) { ?>

                        <?php
                        $metadataPath = "data/reports/reportsmetadata.json";
                        $metadata = json_decode(file_get_contents($metadataPath), true);
                        ?>

                        <?php
                        $root = ".";
                        function updateReportMetadata($root) {
                            $reportsDirectory = "$root/data/reports";
                            $metadataPath = "$root/data/reports/reportsmetadata.json";
                            $metadata = json_decode(file_get_contents($metadataPath), true);

                            $totalModerated = 0;
                            $totalUnmoderated = 0;

                            $boards = scandir($reportsDirectory);
                            foreach ($boards as $board) {
                                if ($board === '.' || $board === '..') continue;

                                $boardDir = $reportsDirectory . '/' . $board;
                                $posts = scandir($boardDir);
                                foreach ($posts as $postID) {
                                    if ($postID === '.' || $postID === '..') continue;

                                    $postReportsDir = $boardDir . '/' . $postID;
                                    $reports = scandir($postReportsDir);
                                    foreach ($reports as $reportNumber) {
                                        if ($reportNumber === '.' || $reportNumber === '..') continue;

                                        $reportFile = $postReportsDir . '/' . $reportNumber . '/report.json';
                                        if (file_exists($reportFile)) {
                                            $reportData = json_decode(file_get_contents($reportFile), true);
                                            if (isset($reportData['moderate']) && $reportData['moderate']) {
                                                $totalModerated++;
                                            } else {
                                                $totalUnmoderated++;
                                            }
                                        }
                                    }
                                }
                            }
                            $metadata['done'] = $totalModerated;
                            $metadata['queue'] = $totalUnmoderated;
                            file_put_contents($metadataPath, json_encode($metadata, JSON_UNESCAPED_UNICODE));
                        }
                        updateReportMetadata($root);
                        $metadataPath = "$root/data/reports/reportsmetadata.json";
                        $metadata = json_decode(file_get_contents($metadataPath), true);
                        ?>

                        <div class="admin-dashboard">
                            <div class="dashboard-item">
                                <p>Összes felhasználó</p>
                                <p><?php echo getUserCount(); ?></p>
                            </div>
                            <div class="dashboard-item blue">
                                <p>Bírálatra váró tartalmak</p>
                                <p><?php echo $metadata['queue']; ?></p>
                            </div>
                            <div class="dashboard-item green">
                                <p>Moderált tartalom</p>
                                <p><?php echo $metadata['done']; ?></p>
                            </div>
                        </div>
                        <div class="tab-bar">
                            <a href="admincenter.php" class="button <?php echo (!isset($_GET['page']) ? 'active' : ''); ?>">Bírálatra vár</a>
                            <a href="admincenter.php?page=moderalt" class="button <?php echo (isset($_GET['page']) && $_GET['page'] == 'moderalt' ? 'active' : ''); ?>">Moderált elemek</a>
                        </div>
                        <div class="section-content">

                            <?php

                            error_reporting(E_ALL);

                            function getReportsByModeration($moderationStatus, $root = ".") {
                                $moderatedReports = [];
                                $unmoderatedReports = [];

                                $reportsDirectory = "$root/data/reports";

                                $boards = scandir($reportsDirectory);
                                foreach ($boards as $board) {

                                    if (str_starts_with("$board", ".") || is_file("$reportsDirectory/$board")) continue;

                                    $boardDir = $reportsDirectory . '/' . $board;
                                    $posts = scandir($boardDir);
                                    foreach ($posts as $postID) {
                                        if (str_starts_with("$postID", ".") || is_file("$reportsDirectory/$board")) continue;

                                        $postReportsDir = $boardDir . '/' . $postID;
                                        $reports = scandir($postReportsDir);
                                        foreach ($reports as $reportNumber) {
                                            if (str_starts_with("$postID", ".") || is_file("$reportsDirectory/$board")) continue;

                                            $reportFile = $postReportsDir . '/' . $reportNumber . '/report.json';
                                            if (file_exists($reportFile)) {
                                                $reportData = json_decode(file_get_contents($reportFile), true);
                                                if (isset($reportData['moderate']) && $reportData['moderate']) {
                                                    $moderatedReports[] = ["board" => $board, "postID" => $postID, "reportNumber" => $reportNumber, "moderate" => $reportData["moderate"]];
                                                } else {
                                                    $unmoderatedReports[] = ["board" => $board, "postID" => $postID, "reportNumber" => $reportNumber, "moderate" => $reportData["moderate"]];
                                                }
                                            }
                                        }
                                    }
                                }
                                return $moderationStatus ? $moderatedReports : $unmoderatedReports;
                            }

                            function displayReports($reports, $root) {
                                foreach ($reports as $report) {
                                    getPostCard("{$report['board']}/{$report['postID']}", "moderate.php?board={$report['board']}&postID={$report['postID']}&reportNumber={$report['reportNumber']}", $root);
                                }
                            }

                            if (!isset($_GET['page'])) {
                                $unmoderatedReports = getReportsByModeration(false, ".");
                                displayReports($unmoderatedReports, '.');
                            } elseif ($_GET['page'] == 'moderalt') {
                                $moderatedReports = getReportsByModeration(true, ".");
                                foreach ($moderatedReports as &$report) {
                                    $report['moderate'] = true;
                                }
                                displayReports($moderatedReports, '.');
                            } elseif ($_GET['page'] == 'uzenofal') {
                            }
                            ?>

                        </div>
                    <?php } else { ?>
                        <p>Az oldal megtekintéséhez rendszergazdai jogosultság szükséges.</p>
                    <?php } ?>
                </section>
            </div>
        </main>
    </body>
</html>