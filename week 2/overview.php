<?php
include ($_SERVER['DOCUMENT_ROOT']) . 'home_button.php';

session_start();


?>

<style> p {font-size: 18px;} </style>

<h1>Besteloverzicht</h1>
<p><b>Gebruikersnaam</b></p>
<p><?= $_SESSION["username"] ?></p>
<p><b>Password</b></p>
<p><?= $_SESSION["password"] ?></p>
<p><b>Product</b></p>
<p><?= $_SESSION["product"] ?></p>
<p><b>Straat</b></p>
<p><?= $_SESSION["street"] ?></p>
<p><b>Nummer</b></p>
<p><?= $_SESSION["number"] ?></p>
<p><b>Plaats</b></p>
<p><?= $_SESSION["place"] ?></p>
<p><a href="ordercomplete.php?status=cancelled">Cancel</a>          <a href="ordercomplete.php?status=ok">Place order</a> </p>
