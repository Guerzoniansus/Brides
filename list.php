<?php
require ($_SERVER['DOCUMENT_ROOT']) . '/helpers.php';

$error = "";
$guest = "";
$listID = null;
$user = null;
$list = null;
$canStillChoose = true;

// Check if URL contains list ID. Multiple checks because of weird bugs
if (isset($_GET["code"])) {
    if (!empty($_GET["code"])) {
        $listID = $_GET["code"];

        if ($listID == null) {
            $error = "Error: Er is geen lijst opgegeven";
        }
    }
}

else {
    $error = "Error: Er is geen lijst opgegeven";
}

if ($listID == null) {
    $error = "Error: Er is geen lijst opgegeven";
}

if ($error == "") {
    $db = new DB();

    // More validation
    if ($listID != null) {
        $list = $db->getList($listID);

        if ($list == null) {
            $error = "Deze lijst bestaat niet!";
        }

    }
}

if ($error == "") {
    if (isset($_COOKIE[$listID])) {
        if (!empty($_COOKIE[$listID])) {
            $guest = $_COOKIE[$listID];
            $canStillChoose = false;
        }
    }
}

function addGift($item) {
    $name = $item->itemName;
    $class = "";
    global $guest;

    // Make sure items chosen by other people wont be displayed
    if ($item->itemOwner != null && $item->itemOwner != "") {
        if ($item->itemOwner != $guest) {
            return;
        }
    }

    if ($item->itemOwner == $guest && $guest != "") $class = "btn btn-lg btn-primary";
    else $class = "btn btn-lg btn-outline-primary";

    ?>
    <button id="<?= $name ?>" class="geschenk-button-single <?= $class ?>" type="button">
        <?= strtoupper($name) ?>
    </button>
    <?php
}


?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <? addHead("Wensenlijst - Happy Brides"); ?>

    <!-- IMPORT JAVASCRIPT -->
    <script type="text/javascript" src="js/list.js"></script>
</head>
<body>

<? showHeader(); ?>

<div class="row">

    <? if ($error != ""): ?>
        <h1 class="text-danger mx-auto"><?= $error ?></h1>
    <? else: ?>

    <div class="col-sm-3 border-right text-center">
        <input id="canStillChoose" type="hidden" name="canStillChoose" value="<?= $canStillChoose ?>">
        <input id="listID" type="hidden" name="listID" value="<?= $listID ?>"?

        <!-- Parameters -->
        <h1>Bruiloft</h1>
        <p>Van <strong><?= $list->name1 ?></strong> en <strong><?= $list->name2 ?></strong></p>
        <p><i><strong>Datum:</strong> <?= $list->date ?></i></p>
        <!-- Image
        <img class="img-fluid w-75 rounded"
             src="https://i.pinimg.com/originals/c5/f8/1b/c5f81bd159937ecfbd5b0104e43f3960.png">
        <br/>
        -->
        <br/>
        <p class="w-75 mx-auto"><?= $list->description ?>
        </p>
    </div>

    <div class="col-sm-6 text-center">

        <h1>Wensenlijst</h1>
        <p><strong>Code: </strong> <?= $listID ?></p>

        <input type="text" class="form-control w-25 mx-auto mt-3" id="gast-naam" placeholder="Uw naam" value="<?= $guest ?>" required>
        <p class="text-danger" id="naam-vergeten-tekst">U moet uw naam nog invullen!</p>
        <p id="ownerNameErr" class="text-danger"></p>

        <? if ($canStillChoose == false): ?>
        <p id="confirmMessage" class="text-success">Je keuze is bevestigd.</p>
        <? endif; ?>

        <div class="btn-group-vertical mt-5 mb-4" id="geschenken-buttons-gasten">

            <? foreach ($list->items as $item): ?>
            <? addGift($item) ?>
            <? endforeach; ?>

        </div>
        <br/>

        <? if ($canStillChoose == true): ?>
        <button class="btn btn-lg btn-success" id="bevestiging-knop">Bevestigen</button>
        <? endif; ?>

        <p id="bevestiging-tekst">Keuze bevestigd!</p>

    </div>

    <div class="col-sm-3 border-left">

        <h1>Hoe werkt het?</h1>
        <p class="pr-3">Hier is een lijst met cadeaus die het bruidspaar graag wilt hebben.
            De cadeaus staan van boven naar beneden op volgorde van welke het bruidspaar het liefst wilt.
            Klik op het cadeau (of de cadeaus)
            die u wilt kopen. Vul bovenaan uw naam in. Alleen het echtpaar zal uw naam zien.
            <br/><br/>Als u klaar bent, drukt u op bevestigen.
            Cadeaus die u gekozen heeft zullen niet meer zichtbaar zijn voor andere gasten.
        </p>
        <p class="text-danger">Na het bevestigen kunt u uw naam en de cadeaus niet meer veranderen.</p>

    </div>

    <? endif; ?>

</div>

</body>
</html>
