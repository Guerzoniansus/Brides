<?php
require ($_SERVER['DOCUMENT_ROOT']) . '/helpers.php';

if (isLoggedIn() == false && isLoggedInFake() == false) {
    if (isset($_COOKIE["sessionID"]) && isset($_GET["logout"]) == false) {
        $sessionID = $_COOKIE["sessionID"];
        $db = new DB();
        $user = $db->getUserFromSessionID($sessionID);

        if ($user != null) {
            setUserFake($user);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <? addHead("Happy Brides"); ?>
</head>
<body>

<!-- HEADER -->

<? showHeader() ?>

<div class="container-fluid">

    <div class="row">

        <!-- MIJN ACCOUNT -->
        <div class="col-sm-3 border-right text-center">
            <? showAccountArea(); ?>
        </div>
        <!-- MIJN ACCOUNT -->

        <div class="col-sm-6 text-center">

            <h1>Heb je een code gekregen?<br>Vul hem hier in!</h1>

            <form action="list.php" method="get" class="mt-3">
                <div class="form-group">
                    <input type="text" class="form-control form-control-lg w-50 text-center mx-auto"
                           placeholder="Voorbeeld: 2Ex9Ap" name="code">
                </div>
            </form>
            <br>
            <img class="img-fluid img-thumbnail" style="width:60%"
                 src="https://s.yimg.com/ny/api/res/1.2/yFEl28TgOO5Q4TC2zjs1Mw--~A/YXBwaWQ9aGlnaGxhbmRlcjtzbT0xO3c9ODAw/https://media-mbst-pub-ue1.s3.amazonaws.com/creatr-uploaded-images/2019-07/ec73e4a0-a87c-11e9-bed3-a40f6ba200d9">

        </div>

        <div class="col-sm-3 border-left">
            <h1>Wat is Happy Brides?</h1>
            <p>Op Happy Brides kunt u makkelijk een online wensenlijst maken voor uw bruiloft. U kunt een lijst maken van geschenken en
                deze cadeaus rangschikken op prioriteit. Uw bruiloftsgasten kunen deze lijst zien en aangeven welke specifieke cadeaus zij
                zullen kopen.</p>
            <h1>Hoe werkt het?</h1>
            <p><strong>Als bruidspaar:</strong></p>
            <p>Zodra u een account maakt krijgt u een unieke code en kunt u beginnen met het maken van een wensenlijst. Als u deze code aan
                bruiloftsgasten geeft kunnen zij uw wensenlijst zien en aangeven welk specifiek cadeau zij zullen kopen.
                <br>Klik <a href="register.php">hier</a> om je te registreren.</p>
            <p><strong>Als gast:</strong></p>
            <p>Vul de code die u gekregen hebt in op het midden van de pagina en u zal doorverwezen worden naar
                de wensenlijst van de bruiloft waartoe u uitgenodigd bent.
            </p>
        </div>

    </div>

</div>

</body>
</html>