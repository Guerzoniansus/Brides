<?php
$emailErr = $passErr = $pass2Err = $submitErr = "";
$ERROR_EMPTY = "Error: Veld mag niet leeg zijn.";

$email = $password = $password2 = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate email

    if (empty($_POST["email"])) {
        $emailErr = $ERROR_EMPTY;
    }

    else {
        $email = test_input($_POST["email"]);

        // Remove illegal chars from email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        // Validate e-mail
        if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
            $emailErr = "Error: Geen geldig email adress.";
        }

        else if (strlen($email) > 320) {
            $emailErr = "Error: Email mag niet meer dan 320 karakters zijn.";
        }
    }

    // Validate passwords, intentionally not using test_input() because passwords
    // won't be echo'd into HTML and because then they might not match login input

    if (empty($_POST["password2"])) {
        $pass2Err = $ERROR_EMPTY;
    }

    else {
        $password2 = $_POST["password2"];
        $password2 = filter_var($password2, FILTER_SANITIZE_STRING);
    }

    if (empty($_POST["password"])) {
        $passErr = $ERROR_EMPTY;
    }

    else {
        $password = $_POST["password"];
        $password = filter_var($password, FILTER_SANITIZE_STRING);
    }

    if ($password != $password2) {
        $passErr = "Error: Passwords zijn niet gelijk aan elkaar.";
    }

    else if (strlen($password) > 30) {
        $passErr = "Error: Password mag niet langer zijn dan 30 characters.";
    }

    // Register
    if ($emailErr == "" && $passErr == "" && $pass2Err == "" && $submitErr == "") {

        $db = new DB();

        if ($db->getUser($email) != null) {
            $emailErr = "Error: Dit email is al in gebruik.";
        }

        else {
            $pass_hash = password_hash($password, PASSWORD_BCRYPT);

            if ($db->addUser($email, $pass_hash) == false) {
                $submitErr = "Error: Database error, please try again";
            }

            else {
                header('Location: login.php?register=ok');
                die();
            }
        }

    }

}