<?php
include ($_SERVER['DOCUMENT_ROOT']) . 'home_button.php';

$username = $password = $product = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    session_start();

    if (isSamePageRequest()) {
        $_SESSION["street"] = $_POST["street"];
        $_SESSION["number"]  = $_POST["number"];
        $_SESSION["place"] = $_POST["place"];

        header('Location: overview.php');
        exit();
    }

    else {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $product = $_POST["product"];

        $_SESSION["username"] = $username;
        $_SESSION["password"]  = $password;
        $_SESSION["product"]  = $product;

    }

}

function isSamePageRequest() {
    $actual_link = (isset($_SEERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http")
        . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if ($actual_link == $_SERVER["HTTP_REFERER"]) {
        return true;
    } else {
        return false;
    }
}

/*
class Order {
    public $username, $password, $product, $street, $number, $place;

    function __construct()
    {
        $this->username = $this->password = $this->product = $this->street
            = $this->number = $this->place = "";
    }
}
*/

?>

<style> p {font-size: 18px;} </style>

<form method="post">
    <input type="text" placeholder="Straat" name="street">
    <br>
    <input type="text" placeholder="Nummer" name="number">
    <br>
    <input type="text" placeholder="Plaats" name="place">
    <br>
    <button type="submit">Next</button>
</form>

<p><b>Gebruikersnaam</b></p>
<p><?= $username ?></p>
<p><b>Password</b></p>
<p><?= $password ?></p>
<p><b>Product</b></p>
<p><?= $product ?></p>