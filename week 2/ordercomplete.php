<?php

session_start();
include ($_SERVER['DOCUMENT_ROOT']) . 'home_button.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $status = $_GET["status"];

    if ($status == "cancelled") {
        echo "Cancelled";
    }

    else if ($status == "ok") {
        echo '
        Bedankt voor de bestelling!
        <p><b>Gebruikersnaam</b></p>
        <p>'.$_SESSION["username"].'</p>
        <p><b>Password</b></p>
        <p>'.$_SESSION["password"].'</p>
        <p><b>Product</b></p>
        <p>'.$_SESSION["product"].'</p>
        <p><b>Straat</b></p>
        <p>'.$_SESSION["street"].'</p>
        <p><b>Nummer</b></p>
        <p>'.$_SESSION["number"].'</p>
        <p><b>Plaats</b></p>
        <p>'.$_SESSION["place"].'</p>
        ';
    }

    else {
        echo 'Probeer jij iets raars te doen?';
    }

    session_unset();
    session_destroy();
}


?>