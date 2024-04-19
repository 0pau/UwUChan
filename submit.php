<?php
session_start(); include "api/users.php"; include "api/acl.php"; include "api/boards.php"; include "api/posts.php";
$target_board = "";
if (isset($_GET["board"])) {
    $target_board = $_GET["board"];
    if (!boardExists($target_board)) {
        $target_board = "";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        ob_get_contents();
        ob_end_clean();
        uploadPost();
    } catch (Error $err) {
        $error = $err->getMessage();
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
        <script>
            function refreshBoardList() {
                let datalist = document.getElementById("boardList");
                let q = document.getElementById("boardInput").value;
                clearBoardList();
                if (q.length < 3) {
                    return;
                }
                document.getElementById("suggested-boards-input").classList.add("suggestionListShown");
                let xhr = new XMLHttpRequest();
                xhr.open("GET", "api/query_board.php?q="+q);
                xhr.onload = function (){
                    let resultObject = JSON.parse(xhr.response);
                    if (resultObject.result == "success") {
                        let items = resultObject.results;
                        items.forEach((i)=>{
                            datalist.innerHTML = datalist.innerHTML + "<span onclick=\"selectBoard('"+i+"', event)\" class='button flat'>"+i+"</span>";
                        });
                    }
                    if (datalist.innerHTML == "") {
                        clearBoardList();
                    }
                }
                xhr.send();
            }

            function selectBoard(boardName, event) {
                document.getElementById("boardInput").value = boardName;
                event.stopPropagation();
                clearBoardList();
            }

            function clearBoardList() {
                document.getElementById("suggested-boards-input").classList.remove("suggestionListShown");
                let datalist = document.getElementById("boardList");
                datalist.innerHTML = "";
            }
        </script>
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
                    <?php if($error != "") { ?>
                        <p class="error"><span class="material-symbols-rounded">error</span><?php echo $error ?></p>
                    <?php } ?>
                    <form id="new-post-form" method="POST" enctype="multipart/form-data">
                        <label class="card-header">Üzenőfal kiválasztása (Kötelező)</label>
                        <div id="suggested-boards-input" onauxclick="clearBoardList()">
                            <input id="boardInput" onkeyup="refreshBoardList()" name="board-name" list="boardlist" required type="text" placeholder="Kezdd el egy üzenőfal nevét gépelni..." value="<?php echo $target_board ?>">
                            <div id="boardList">

                            </div>
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
                        <p class="card-header">A következőre figyelj!</p>
                        <p class="card-description">A poszt összmérete nem haladhatja meg a 8 megabájtot, valamint egy kép legfeljebb csak 2 megabájtos lehet.</p>
                        <button class="cta">Poszt feltöltése</button>
                    </form>
                </section>
            </div>
        </main>
    </body>
</html>