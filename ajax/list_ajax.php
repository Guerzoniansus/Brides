<?php

require ($_SERVER['DOCUMENT_ROOT']) . '/helpers.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        $action = $_POST["action"];

        switch ($action) {
            case "validateOwner":
                validateOwner();
                break;
            case "submitOwner":
                submitOwner();
                break;
        }
    }
}


function validateOwner() {
    $listID = $_POST["listID"];

    $returnMessage = "valid";

    if (empty($_POST["ownerName"])) {
        $returnMessage = "Naam mag niet leeg zijn";
        echo $returnMessage;
        return;
    }

    $ownerName = $_POST["ownerName"];

    if ($ownerName > 30) {
        $returnMessage = "Naam mag niet langer zijn dan 30 letters";
        echo $returnMessage;
        return;
    }

    $db = new DB();
    $list = $db->getList($listID);

    foreach ($list->items as $item) {
        if (strtolower($item->itemOwner) == strtolower($ownerName)) {
            $returnMessage = "Iemand heeft deze naam al gebruikt";
            echo $returnMessage;
            return;
        }
    }



    echo $returnMessage;
}

function submitOwner() {
    $listID = $_POST["listID"];
    $ownerName = $_POST["ownerName"];
    $itemNames = $_POST["itemNames"];
    $db = new DB();

    // Filter and sanitize the names
    $ownerName = filter_var($ownerName, FILTER_SANITIZE_STRING);
    $ownerName = test_input($ownerName);

    $list = $db->getList($listID);
    $allItemNames = [];

    foreach ($list->items as $item) {
        array_push($allItemNames, $item->itemName);
    }

    for ($i = 0; $i < count($allItemNames); $i++) {
        $currItem = $allItemNames[$i];

        if (in_array($currItem, $itemNames)) {
            $list->items[$i]->itemOwner = $ownerName;
        }
    }


    echo $db->updateList($list);
}
