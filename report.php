<?php session_start(); include "api/acl.php"; include "api/boards.php"; include "api/users.php"?>
<!doctype html>
<html lang="hu">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bejelentés - UwUChan</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/mobile.css">
    <link rel="stylesheet" href="css/submit.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
  </head>
  <body class="<?php include "api/theme.php"?>">
    <main>
        <?php include "views/header.php" ?>
      <div class="main-flex">
          <?php include "views/sidebar.php"?>
      <section>
        <div class="section-head">
          <h1>Bejelentés</h1>
        </div>
          <form action="report.php" method="post" id="new-post-form">
              <label class="card-header">Bejelenteni kívánt tartalom</label>

              <?php
              $metadataPath = "data/reports/reportsmetadata.json";
              $metadata = json_decode(file_get_contents($metadataPath), true);
              ?>

              <?php
              $message = "";
              $reportSuccess = false;
              ?>


              <?php
              if (isset($_GET["w"])) {
                  list($boardName, $postId) = explode('/', $_GET['w'], 2);
                  include "api/posts.php";
                  getPostCard($_GET["w"], "post.php?n=".$_GET["w"],true);
                  echo '<input type="hidden" name="board_name" value="' . htmlspecialchars($boardName) . '">';
                  echo '<input type="hidden" name="post_id" value="' . htmlspecialchars($postId) . '">';
              }
              ?>


              <?php
              if ($_SERVER["REQUEST_METHOD"] == "POST") {
                  $boardName = $_POST['board_name'];
                  $postID = $_POST['post_id'];
                  $username = $_SESSION['user'];
                  $violation = $_POST['violation'];
                  $comment = $_POST['comment'];
                  $postedAt = date("Y-m-d H:i:s");

                  $reportDirBase = "data/reports/{$boardName}/{$postID}";
                  $nextReportNumber = 1;
                  while (is_dir("{$reportDirBase}/{$nextReportNumber}")) {
                      $nextReportNumber++;
                  }

                  $newReportDir = "{$reportDirBase}/{$nextReportNumber}";
                  if (!is_dir($newReportDir)) {
                      mkdir($newReportDir, 0777, true);
                  }

                  $reportFilePath = "{$newReportDir}/report.json";
                  $report = [
                      'username' => $username,
                      'posted_at' => $postedAt,
                      'type' => 0,
                      'moderate' => false,
                      'content' => [
                          'boardName' => $boardName,
                          'postID' => $postID,
                          'violation' => $violation,
                          'comment' => $comment
                      ]
                  ];

                  $postFilePath = "data/boards/{$boardName}/{$postID}.json";
                  if (file_exists($postFilePath)) {
                      $postContent = json_decode(file_get_contents($postFilePath), true);
                      $postContent['hidden'] = true;
                      file_put_contents($postFilePath, json_encode($postContent, JSON_PRETTY_PRINT));
                  }

                  $metadataPath = "data/reports/metadata.json";
                  $metadata = file_exists($metadataPath) ? json_decode(file_get_contents($metadataPath), true) : ['queue' => 0];
                  $metadata['queue']++;
                  if (file_put_contents($reportFilePath, json_encode($report, JSON_PRETTY_PRINT))) {
                      echo "A bejelentést sikeresen továbbítottad a moderátoroknak!";
                      file_put_contents($metadataPath, json_encode($metadata, JSON_PRETTY_PRINT));
                  } else {
                      echo "Error saving report.";
                  }
              }
              ?>



              <?php if (!$reportSuccess): ?>
              <label class="card-header">Szabályszegés kiválasztása</label>
              <select name="violation" id="violation">
                  <option value="0">Kendőzetlen erőszak</option>
                  <option value="1">Személyeskedés</option>
                  <option value="2">Személyiségi jogok sértése</option>
                  <option value="3">A tartalom nem illik az üzenőfalra</option>
                  <option value="4">Gyermekpornográfia</option>
                  <option value="5">Kábítószerfogyasztás, -előállítás</option>
                  <option value="6">Államhatalom puccsal történő átvétele</option>
              </select>
              <label class="card-header">Megjegyzés</label>
              <fieldset id="post-body-editor">
                  <textarea required name="comment" id="post-text" placeholder="Ide írhatod a megjegyzésed..."></textarea>
              </fieldset>
              <button type="submit" class="cta">Elküldés</button>
              <?php else: ?>
                  <p><?php echo $message; ?></p>
              <?php endif; ?>
          </form>
      </section>
    </div>
    </main>
  </body>
</html>