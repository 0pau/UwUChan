<?php
    session_start();
    include "api/users.php";
    include "api/boards.php";
?>
<!doctype html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>UwUChan tudakozó</title>
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/mobile.css">
        <link rel="stylesheet" href="css/help-anim.css">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    </head>
    <body class="<?php include "api/theme.php"?>">
        <main>
            <?php include "views/header.php" ?>
            <div class="main-flex">
                <?php include "views/sidebar.php" ?>
                <section class="help">
                    <h1 id="top">Tudakozó</h1>
                    <article class="contents">
                        <b>Tartalomjegyzék</b>
                        <p>Az UwUChan-ról</p>
                        <ul>
                            <li><a href="#about">Mi az az UwUChan?</a></li>
                            <li><a href="#boards">Mik azok az üzenőfalak?</a></li>
                            <li><a href="#newboard">Szeretnék üzenőfalat létrehozni. Mi a teendő?</a></li>
                            <li><a href="#cute">Hogyan tudom kikapcsolni a rózsaszín kurzort?</a></li>
                        </ul>
                        <p>Regisztrációval kapcsolatos kérdések</p>
                        <ul>
                            <li><a href="#birthday">Miért kell megadnom a születésnapomat?</a></li>
                            <li><a href="#password">Milyen a jó jelszó?</a></li>
                        </ul>
                        <p>Az oldalon megjelenő tartalom szabályozási módja</p>
                        <ul>
                            <li><a href="#post_rules">Mit és hogyan lehet az UwUChan-ra posztolni?</a></li>
                            <li><a href="#report">Mit tegyek, ha valami szabályellenes tartalmat találok?</a></li>
                            <li><a href="#moderation">Hogyan történik egy poszt, komment moderálása?</a></li>
                        </ul>
                    </article>
                    <hr>
                    <article id="about">
                        <h1>Mi az az UwUChan?</h1>
                        <p>Az UwUChan egy új, magyar fejlesztésű közösségi oldal, ami vegyíti a Reddit, a 4chan, és az Instagram legjobb tulajdonságait.</p>
                    </article>
                    <article id="boards">
                        <h1>Mik azok az üzenőfalak?</h1>
                        <p>Az üzenőfalak tematikus színterek, ahova lehet posztolni <b>az üzenőfal témájába vágó</b> tartalmakat. Például, a macskakedvelők cicás képeket posztolhatnak a "macskak" nevű üzenőfalra.</p>
                    </article>
                    <article id="newboard">
                        <h1>Szeretnék üzenőfalat létrehozni. Mi a teendő?</h1>
                        <p>Ha egy olyan témában szeretnél egy közösséget összehozni, ami még nem létezik az UwUChan-on, akkor az ötletláda oldalán beadhatsz egy üzenőfal-létrehozási kérelmet, melyben meg kell adnod az új üzenőfal nevét, valamint meg kell indokolnod, hogy miért szeretnéd létrehozni. Ha az adminok elfogadják a kérelmet, akkor automatikusan létrejön az új üzenőfal.</p>
                    </article>
                    <article id="cute">
                        <h1>Hogyan tudom kikapcsolni a rózsaszín kurzort?</h1>
                        <p>A számítógép alapértelmezett kurzorának használatához a profil menüben keresd meg a "Cuki kurzor" kapcsolót, és kapcsold ki.</p>
                    </article>
                    <hr>
                    <article id="birthday">
                        <h1>Miért kell megadnom a születésnapomat?</h1>
                        <p>A regisztráció során kötelező megadni a születésnapot is, mert:</p>
                        <ul>
                            <li>Az UwUChan-on a regisztrációs korhatár 13 év</li>
                            <li>Így meg tudjuk akadályozni, hogy a kiskorú felhasználók ne tudjanak elérni felnőtteknek szánt tartalmakat az oldalon</li>
                        </ul>
                    </article>
                    <article id="password">
                        <h1>Milyen a jó jelszó?</h1>
                        <p>Fontos, hogy minél biztonságosabb jelszót válasszunk a fiókunknak, megelőzve az illetéktelen belépést. A jelszó biztonságosságának fokmérője az, hogy mennyi idő alatt lehet feltörni.</p>
                        <p>A feltöréshez szükséges időt több tényező is befolyásolja:</p>
                        <ul>
                            <li>Hány karakterből áll a jelszó</li>
                            <li>Mennyire vegyes karakterekből áll a jelszó (kis-, nagybetűk, számok, speciális karakterek)</li>
                        </ul>
                        <p>Ahhoz, hogy biztonságos legyen a jelszó, a következőknek kell megfelelnie:</p>
                        <ul>
                            <li>Legalább 8 karakterből áll</li>
                            <li>Nem tartalmaz személyes adatot (pl. név, születési dátum)</li>
                            <li>Vegyesen tartalmaz kis- és nagybetűket, számokat és speciális karaktereket</li>
                        </ul>
                    </article>
                    <hr>
                    <article id="post_rules">
                        <h1>Mit és hogyan lehet az UwUChan-ra posztolni?</h1>
                        <p>Posztolni a főoldalon megjelenő "Új poszt írása" gomb segítségével lehetséges.</p>
                        <p>Ahhoz, hogy az UwUChan egy mindenkinek egyaránt biztonságos és otthonos legyen, be kell tartani néhány szabályt.</p>
                        <ol>
                            <li>A poszt témája kapcsolódjon az üzenőfal tematikájához! (például egy macskákról szóló üzenőfalra nem posztolhatsz kutyás képet)</li>
                            <li>
                                A poszt ne tartalmazzon törvényellenes tartalmat! Ilyen tartalomnak minősül:
                                <ul>
                                    <li>Kendőzetlen erőszak (például gore)</li>
                                    <li>Gyermekpornográfia</li>
                                    <li>Kábítószerfogyasztás, -előállítás</li>
                                    <li>Államhatalom puccsal történő átvétele</li>
                                </ul>
                            </li>
                            <li>A poszt ne sértse mások személyiségi jogait, és ne járasson le másokat! Kerüljük a személyeskedést!</li>
                        </ol>
                    </article>
                    <article id="report">
                        <h1>Mit tegyek, ha valami szabályellenes tartalmat találok?</h1>
                        <p>A poszt kártyáján, vagy a poszt oldalon megjelenő zászló ikon segítségével bejelentheted a posztot az adminoknak, akik a moderációs feladatokat végzik. A bejelentéskor meg kell adnod, hogy milyen szabálysértést követett el a poszt feltöltője (lásd. <a href="#post_rules">Posztolási szabályok</a>)</p>
                    </article>
                    <article id="moderation">
                        <h1>Hogyan történik egy poszt, komment moderálása?</h1>
                        <p>Amint egy poszt, vagy komment bejelentésre kerül, eltűnik az oldalról, viszont még nem kerül végleges törlésre, azaz nem kerül listázásra a publikum számára. Ez az állapot egészen addig áll fenn, amíg az adminok nem hoznak ítéletet a poszt, vagy komment sorsáról. Ha jogos volt a bejelentés, végleg töröljük a tartalmat a rendszerből, viszont, ha nem volt az, ismét listázásra fog kerülni.</p>
                        <p>A bejelentésről a poszt feltöltője értesítést kap, valamint a meghozott ítéletről is. Az értesítésre válaszolva a szerző fellebezhet, illetve bizonyíthatja, hogy nem követett el szabálysértést a bejelentést követő 5 napban.</p>
                    </article>
                </section>
                <a href="#top" class="button go-up cta"><span class="material-symbols-rounded">arrow_upward</span></a>
            </div>
        </main>
    </body>
</html>