<?php
include ($_SERVER['DOCUMENT_ROOT']) . 'home_button.php';

/*
$ERROR_EMPTY = "Veld mag niet leeg zijn";
$err = false;
$nameErr = $passErr = "";
$username = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (empty($_GET["username"])) {
        $nameErr = $ERROR_EMPTY;
        $err = true;
    } else {
        $username = test_input($_GET["username"]);

        if (!preg_match("/^[a-zA-Z]*$/", $username)) {
            $nameErr = "Alleen letters zijn toegestaan, geen spaties";
            $err = true;
        }

    }

    if (empty($_GET["password"])) {
        $passErr = $ERROR_EMPTY;
        $err = true;
    } else {
        $password = test_input($_GET["password"]);

        if (!preg_match("/^[a-zA-Z]*$/", $password)) {
            $nameErr = "Alleen letters zijn toegestaan, geen spaties";
            $err = true;
        }

    }

    if ($err == false) {

        session_start();

        header('Location: addproduct.php?')

    }

}
*/

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>


<form action="addproduct.php" method="get">
    <input type="text" placeholder="username" name="username">
    <input type="password" placeholder="password" name="password">
    <button type="submit">Next</button>
</form>
