<?php
include ($_SERVER['DOCUMENT_ROOT']) . 'home_button.php';

$username = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $username = test_input($_GET["username"]);
    $password = test_input($_GET["password"]);
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>

<style> p {font-size: 18px;} </style>

<p>Selecteer een product</p>
<form action="addadress.php" method="post">
    <select name="product">
        <option name="ball" value="ball">Bal</option>
        <option name="book" value="book">Boek</option>
        <option name="car" value="car">Auto</option>
    </select>
    <input type="hidden" name="username" value="<?= $username ?>">
    <input type="hidden" name="password" value="<?= $password ?>">
    <input type="submit" name="submit" value="Next">
</form>

<p><b>Gebruikersnaam</b></p>
<p><?= $username ?></p>
<p><b>Password</b></p>
<p><?= $password ?></p>