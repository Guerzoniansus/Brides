<?php
require ($_SERVER['DOCUMENT_ROOT']) . '/helpers.php';

$error = "";
$listID = null;
$user = null;
$list = null;

// Check if URL contains list ID. Multiple checks because of weird bugs
if (isset($_GET["list"])) {
    if (!empty($_GET["list"])) {
        $listID = $_GET["list"];

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

// First check if user is logged in before doing pointless database lookup
if (isLoggedIn() == false) {
    $url = $listID == null ? "login.php" : "login.php?list=" . $listID;
    header('Location: ' . $url);
    die();
}

else {
    $user = getUser();
}

if ($error == "") {
    $db = new DB();

    // More validation
    if ($listID != null) {
        $list = $db->getList($listID);

        if ($list == null) {
            $error = "Deze lijst bestaat niet!";
        }

        else {
            if ($list->listID != $user->listID) {
                $error = "Dit is niet jouw lijst!";
            }
        }
    }
}

//$imagePath = '/bride_images/example.png';
$imagePath = "https://i.pinimg.com/originals/c5/f8/1b/c5f81bd159937ecfbd5b0104e43f3960.png";
$itemsAndOwners = [];

// Everything is cool
if ($error == "") {
    if (file_exists('/bride_images/' . $listID . '.png')) {
        $imagePath = '/bride_images/' . $listID . '.png';
    }

    foreach ($list->items as $item) {
        if ($item->itemOwner != null) {
            if (!empty($item->itemOwner)) {
                $itemsAndOwners[$item->itemName] = $item->itemOwner;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <? addHead("Mijn lijst"); ?>
    <script type="text/javascript" src="js/mylist.js"></script>
</head>
<body>

<!-- HEADER -->
<? showHeader(); ?>

<div class="container-fluid">

    <div class="row">

        <? if ($error != ""): ?>
        <h1 class="text-danger mx-auto"><?= $error ?></h1>

        <? else: ?>

        <div class="col-sm-3 border-right text-center">

            <h1>Beschrijving</h1>

            <!-- name1 and name2 -->
            <p>Bruiloft van
                <span class="border bruiloft-parameters" id="name1" contenteditable="true"><?= $list->name1 ?></span> en
                <span class="border bruiloft-parameters" id="name2" contenteditable="true"><?= $list->name2 ?></span>
            </p>
            <p id="nameErr" class="text-danger"></p>

            <!-- Datum -->
            <p><i><strong>Datum:</strong>
                    <span class="border bruiloft-parameters" id="date" contenteditable="true"><?= $list->date ?></span>
                    </i></p>
            <p id="dateErr" class="text-danger"></p>

            <!-- Image
            <img class="img-fluid w-75 rounded"
                 src="<?= $imagePath ?>">
            <br/>
            <i>Je kunt een foto uploaden van jullie als bruidspaar</i>
            <input type="file" name="couple-photo" class="form-control-file mx-auto w-50"
                   accept="image/*">
            <br/>
            -->

            <!-- Description -->
            <p class="w-75 mx-auto border bruiloft-parameters" id="description" contenteditable="true">
                <?= $list->description ?>
            </p>
            <p id="descriptionErr" class="text-danger"></p>

        </div>


        <div class="col-sm-6 text-center">

            <h1>Uw wensenlijst</h1>
            <h3>Code: <?= $list->listID ?></h3>
            <br>

            <!-- Save button -->
            <button class="btn btn-lg btn-primary" type="button" id="savebutton">Wijzigingen opslaan</button>
            <p id="saveErr" class="text-danger"></p>
            <p id="saveSuccess" class="text-success"></p>
            <br><br>

            <!-- De lijst zelf -->
            <div class="btn-group-vertical geschenken-buttons-owner w-75">

                <? foreach ($list->items as $item): ?>

                <div class="btn-group geschenken-buttons-owner-row" id="<?=  $item->itemName ?>">
                    <button class="btn btn-lg btn-outline-primary" type="button"><?= strtoupper($item->itemName) ?></button>
                    <div class="input-group-append">
                        <button class="btn btn-success up-button" type="button">▲</button>
                        <button class="btn btn-success down-button" type="button">▼</button>
                        <button class="btn btn-danger delete-button" data-toggle="modal" type="button" data-target="#myModal">×</button>
                    </div>
                </div>

                <? endforeach; ?>

            </div>

            <!-- Cadeau toevoegen -->
            <br/><br/>
            <input type="text" id="cadeau-naam" class="form-control w-25 mx-auto" placeholder="Cadeau naam">
            <button class="btn btn-lg btn-success mt-2" type="button" id="nieuw-cadeau">Voeg nieuw cadeau toe</button>
            <p id="giftNameErr" class="text-danger"></p>

            <!-- Item owners (cadeaus die al gekozen zijn) -->
            <? if(!empty($itemsAndOwners)): ?>
            <div id="item-owners">
                <p>Cadeaus die al gekozen zijn:</p>

                <?foreach($itemsAndOwners as $itemName => $itemOwner): ?>
                <p id="owner-<?= $itemName ?>"><strong class="itemOwner"><?= $itemName ?></strong> is gekozen door: <?= $itemOwner ?></p>
                <? endforeach; ?>
            </div>
            <? endif; ?>

            <!-- Modal for delete warning -->
            <div id="myModal" class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Cadeau verwijderen</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Weet je zeker dat je dit cadeau wilt verwijderen?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" id="verwijder-cadeau-button" data-dismiss="modal">Ja, verwijder hem</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleer</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-sm-3 border-left">

            <h1>Hoe werkt het?</h1>
            <p><strong>Beschrijving</strong></p>
            <p>Bij de beschrijving kunt u de naam invullen van jullie als bruidspaar, de datum typen waarop de bruiloft is,
                een foto van jullie uploaden zodat gasten jullie meteen herkennen, en een korte tekst schrijven.
                Een tip is om ieder geval een contactgegeven neer te zetten voor als iemand vragen heeft.
            </p>
            <p>U kunt de tekst links aanpassen door erop te klikken.</p>
            <p class="text-danger"><u>Wanneer u klaar bent met aanpassingen maken, druk op wijzigingen opslaan,
                    of je wijzigingen zullen verloren gaan.</u></p>

            <p><strong>Uw wensenlijst</strong></p>
            <p>In uw wensenlijst staat de lijst met cadeaus die uw wilt voor uw bruiloft. Gebruik de <span class="text-success">pijl knoppen</span> om ze
                op volgorde te zetten van hoog naar laag, met de cadeaus die u het graagst wilt bovenaan.
            </p>
            <p>Gebruik de <span class="text-danger">verwijder knop</span> om een cadeau te verwijderen.</p>
            <p>Vul onderin een naam in en klik op <span class="text-success">Voeg nieuw cadeau toe</span> om het cadeau toe te voegen.</p>
            <p>Als u klaar bent, kunt u uw code geven aan gasten zodat zij uw lijst kunnen zien.</p>
        </div>

        <? endif; ?>

    </div>

</div>

</body>
</html>