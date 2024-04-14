<?php
session_start(); include "api/users.php"; include "api/acl.php"; include "api/boards.php";
$target_board = "";
if (isset($_GET["board"])) {
    $target_board = $_GET["board"];
    if (!boardExists($target_board)) {
        $target_board = "";
    }
}
?>
<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Poszt írása - UwUChan</title>
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
                        <h1>Új poszt írása</h1>
                    </div>
                    <form id="new-post-form" method="POST" action="api/upload_post.php">
                        <label class="card-header">Üzenőfal kiválasztása (Kötelező)</label>
                        <div id="suggested-boards-input">
                            <input name="board-name" required type="text" placeholder="Kezdd el egy üzenőfal nevét gépelni..." value="<?php echo $target_board ?>">
                        </div>
                        <label class="card-header">A poszt tartalma (Kötelező)</label>
                        <fieldset id="post-body-editor">
                            <input required type="text" name="post-title" id="post-title" placeholder="Poszt címe">
                            <textarea required name="post-body" id="post-body" placeholder="Poszt szövege"></textarea>
                        </fieldset>
                        <label class="card-header">Mellékletek</label>
                        <fieldset class="horizontal">
                            <label>Képek</label>
                            <input type="file" name="images[]" id="image-uploader" accept="image/*" multiple>
                        </fieldset>
                        <button class="cta">Poszt feltöltése</button>
                    </form>
                </section>
            </div>
        </main>
    </body>
</html>