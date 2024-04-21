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
        <form id="new-post-form">
          <label class="card-header">Bejelenteni kívánt tartalom</label>
            <?php include "api/posts.php";
                if (!isset($_GET["w"])) {
                    die("<p>Nincs megadva a bejelenteni kívánt tartalom.</p>");
                } else {
                    getPostCard($_GET["w"], "post.php?n=".$_GET["w"],true);
                }
            ?>
          <label class="card-header">Szabályszegés kiválasztása</label>
          <select name="reason" id="reason">
            <option>Kendőzetlen erőszak</option>
            <option>Személyeskedés</option>
            <option>Személyiségi jogok sértése</option>
            <option>A tartalom nem illik az üzenőfalra</option>
            <option>Gyermekpornográfia</option>
            <option>Kábítószerfogyasztás, -előállítás</option>
            <option>Államhatalom puccsal történő átvétele</option>
          </select>
          <label class="card-header">Megjegyzés</label>
          <fieldset id="post-body-editor">
            <textarea required name="post-text" id="post-text" placeholder="Ide írhatod a megjegyzésed..."></textarea>
          </fieldset>
          <button class="cta">Elküldés</button>
        </form>
      </section>
    </div>
    </main>
  </body>
</html>