<?php
/**
 * Lekér egy HTML-kártyát, ami tartalmazza az éppen aktuális rendszerüzenetet. Ha nincs rendszerüzenet, akkor
 * nem tesz semmit.
 * @return void
 */
function getSystemMessage() {
    if (file_exists("data/system_message.json")) {

        $sysmsg = file_get_contents("data/system_message.json");
        $sysmsg = json_decode($sysmsg, false);

        echo "
        <div><div class=\"post-card no-margin\">
            <div class=\"card-head\">
                <a href=\".\">
                    <img class=\"user-profile-blog-avatar\" src=\"img/system_message_avatar.png\" alt=\"Rendszerüzenet ikonja\">
                    <span>Rendszerüzenet</span>
                </a>
            </div>
            <div class=\"post-content\">
                <div class=\"post-fragment\">
                    <div class=\"post-body\">
                        <p class=\"post-title\">$sysmsg->title
                        <p class=\"post-text\">$sysmsg->body</p>
                    </div>
                </div>
            </div>
        </div></div><hr>   
        ";
    }
}