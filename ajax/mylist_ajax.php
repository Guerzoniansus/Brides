<?php

require ($_SERVER['DOCUMENT_ROOT']) . '/helpers.php';

// Receive AJAX requests

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["action"])) {
        $action = $_POST["action"];

        // Validate bruiloft parameters
        if ($action == "validateParameters") {
            $validateParametersData = validateParameters();
            echo json_encode($validateParametersData);
        }

        // Validate gift name
        else if ($action == "validateGiftName") {
            $validateGiftNameData = validateGiftName($_POST["giftName"]);
            echo json_encode($validateGiftNameData);
        }

        // Save list
        else if ($action == "saveList") {
            save();
        }


    }

}

// Define some functions for nice clean code :D

function createErrorMessageData(string $errorType, string $saveErr, bool $error) {
    $ajaxData["saveErr"] = $saveErr;
    $ajaxData["error"] = $error;
    $ajaxData["errorType"] = $errorType;
    return $ajaxData;
}

function save() {

    $saveErr = "";
    $error = false;

    $validateParametersData = validateParameters();

    // First check if there's nothing wrong with the parameters
    // If there's something wrong, return those errors
    if ($validateParametersData["error"] == true) {
        $saveErr = "Er zijn nog fouten aanwezig, opslaan mislukt.";
        $errorType = "parameters";

        $validateParametersData["saveErr"] = $saveErr;
        $validateParametersData["errorType"] = $errorType;
        echo json_encode($validateParametersData);
        die();
    }

    $itemNames = [];
    $hasItems = false;

    if (isset($_POST["itemNames"])) {
        $itemNames = $_POST["itemNames"];
        $hasItems = true;
    }

    // Check if there's anything wrong with the gift names
    // Though the only way for this to be possible is through inspect element
    // But never trust the client
    foreach ($itemNames as $itemName) {
        if (validateGiftName($itemName)["error"] == true) {
            $error = true;
            $errorType = "itemNames";
            $saveErr = "Er is een cadeau met een ongeldige naam. Zorg ervoor dat er geen cadeau is met een lege naam,
                of een naam met meer dan 40 letters, en probeer dan opnieuw op te slaan";

            $ajaxData = createErrorMessageData(errorType, saveErr, $error);
            echo json_encode($ajaxData);
            die();
        }
    }

    // Check if session didnt expire because user has been AFK for 30 minutes
    if (getUser() == null) {
        $ajaxData = createErrorMessageData("timeout", "Error: Sessie is verlopen, log opnieuw in", true);
        echo json_encode(($ajaxData));
        die();
    }

    // Assuming everything is correct now

    // Sanitize / filter all inputs
    $name1 = test_input($_POST["name1"]);
    $name1 = filter_var($name1, FILTER_SANITIZE_STRING);
    $name2 = test_input($_POST["name2"]);
    $name2 = filter_var($name2, FILTER_SANITIZE_STRING);
    $date = test_input($_POST["date"]);
    $date = filter_var($date, FILTER_SANITIZE_STRING);
    $description = test_input($_POST["description"]);
    $description = filter_var($description, FILTER_SANITIZE_STRING);

    for ($i = 0; $i < count($itemNames); $i++) {
        $itemName = $itemNames[$i];
        $itemName = test_input($itemNames[$i]);
        $itemName = filter_var($itemName, FILTER_SANITIZE_STRING);
        $itemNames[$i] = $itemName;
    }

    $listID = getUser()->listID;

    $db = new DB();
    $oldList = $db->getList($listID);

    // Check if they have a list (which should be true... but just in case
    if ($oldList == null) {
        $ajaxData = createErrorMessageData("database", "Error: Database fout", true);
        echo json_encode($ajaxData);
        die();
    }

    $list = new GiftList();
    $list->listID = $listID;
    $list->name1 = $name1;
    $list->name2 = $name2;
    $list->date = $date;
    $list->description = $description;

    $itemObjects = [];

    for ($i = 0; $i < count($itemNames); $i++) {
        $itemName = $itemNames[$i];
        $itemObject = new Item();
        $itemObject->itemName = $itemName;
        $itemObject->itemIndex = $i;
        $itemObject->listID = $listID;

        // Add item owners back
        foreach ($oldList->items as $oldItem) {
            if ($oldItem->itemName == $itemName) {
                if ($oldItem->itemOwner != null) {
                    if (!empty($oldItem->itemOwner)) {
                        $itemObject->itemOwner = test_input($oldItem->itemOwner);
                    }
                }
            }
        }

        $itemObjects[$i] = $itemObject;
    }

    $list->items = $itemObjects;

    $dbErrorMessage = $db->updateList($list);

    if ($dbErrorMessage != "success") {
        // It WILL return an error called "items" if the user hasnt added any items yet
        // We dont want to show an error for this though
        if (! ($hasItems == false && $dbErrorMessage == "items")) {
            $ajaxData = createErrorMessageData("database",
                "Er was een database error, probeer het alsjeblieft opnieuw", true);
            echo json_encode($ajaxData);
        }

        else {
            $ajaxData["error"] = false;
            echo json_encode($ajaxData);
        }
    }

    else {
        $ajaxData["error"] = false;
        echo json_encode($ajaxData);
    }


}

/**
 * Validate Gift name (string giftNAme)
 * @return array $ajaxData[] with error messages, including boolean "error" to check if ANYTHING went wrong
 */
function validateGiftName(string $giftName) {
    $giftNameErr = "";

    // *-*-* Validate name *-*-*
    if (empty($giftName)) {
        $giftNameErr = "Error: Veld mag niet leeg zijn";
    }

    else {
        if (strlen($giftName) > 40) {
            $giftNameErr = "Error: Naam mag niet langer zijn dan 40 letters";
        }
    }

    // *-*-* Return errors *-*-*
    $error = $giftNameErr == "" ? false : true;

    $ajaxData["giftNameErr"] = $giftNameErr;
    $ajaxData["error"] = $error;

    return $ajaxData;
}


/**
 * Validate bruiloft parameters
 * @return array $ajaxData[] with error messages, including boolean "error" to check if ANYTHING went wrong
 */
function validateParameters() {
    $nameErr = $dateErr = $descriptionErr = $submitErr = "";
    $error = true;
    $ERROR_EMPTY = "Error: Veld mag niet leeg zijn.";

    // *-*-* Name validation *-*-*

    if (empty($_POST["name1"]) || empty($_POST["name2"])) {
        $nameErr = $ERROR_EMPTY;
    }

    else {
        $name1 = $_POST["name1"];
        $name2 = $_POST["name2"];

        if (strlen($name1) > 30 || strlen($name2) > 30) {
            $nameErr = "Error: Naam mag niet meer dan 30 letters zijn";
        }
    }

    // *-*-* Date validation *-*-*

    if (empty($_POST["date"])) {
        $dateErr = $ERROR_EMPTY;
    }

    else {
        $date = $_POST["date"];

        if (strlen($date) > 30) {
            $dateErr = "Error: Datum mag niet meer dan 30 karakters zijn";
        }
    }

    // *-*-* Description validation *-*-*

    if (empty($_POST["description"])) {
        $descriptionErr = $ERROR_EMPTY;
    }

    else {
        $description = $_POST["description"];

        if (strlen($description) > 500) {
            $descriptionErr = "Error: Beschrijving mag niet meer dan 500 karakters zijn";
        }
    }

    if ($nameErr == "" && $dateErr == "" && $descriptionErr == "") {
        $error = false;
    }

    // *-*-* Return errors *-*-*

    $ajaxData["nameErr"] = $nameErr;
    $ajaxData["dateErr"] = $dateErr;
    $ajaxData["descriptionErr"] = $descriptionErr;
    $ajaxData["submitErr"] = $submitErr;
    $ajaxData["error"] = $error;

    return $ajaxData;
}

?>