<?php
require ($_SERVER['DOCUMENT_ROOT']) . '/helpers.php';

$email = $password = $emailErr = $passErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Some input validation to prevent pointless database lookups

    if (isLoggedIn()) {
        $emailErr = "Je bent al ingelogd!";
    }

    if (empty($_POST["email"])) {
        $emailErr = "Error: Je hebt geen email opgegeven.";
    }

    else {
        $email = test_input($_POST["email"]);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
            $emailErr = " Error: Geen geldige email";
        }
    }

    if (empty($_POST["password"])) {
        $passErr = "Error: Je hebt geen password opgegeven.";
    }

    else {
        $password = $_POST["password"];
        $password = filter_var($password, FILTER_SANITIZE_STRING);
    }

    // Log in

    if ($emailErr == "" && $passErr == "") {
        $db = new DB();
        $user = $db->getUser($email);

        if ($user == null) {
            $emailErr = "Een account met dit email bestaat niet.";
        }

        else {
            if (password_verify($password, $user->password) == false) {
                $passErr = "Error: Dit wachtwoord klopt niet.";
            }

            else {
                $sessionID = session_id();

                if ($db->updateUserSessionID($email, $sessionID) == false) {
                    $emailErr = "Error: Er was een database error, probeer het opnieuw.";
                }

                else {
                    $user->sessionID = $sessionID;
                    setUser($user);

                    // Set session ID cookie that expires after 30 days
                    setcookie("sessionID", $sessionID, time() + (86400 * 30), "/");

                    if (isset($_GET["list"])) {
                        $listToGoTo = test_input($_GET["list"]);
                        header('Location: mylist.php?list='. $listToGoTo);
                        die();
                    }

                    else {
                        header('Location: index.php');
                        die();
                    }
                }
            }
        }
    }

}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <? addHead("Login"); ?>
    <link rel="stylesheet" type="text/css" href="css/loginregister.css">
</head>
<body>

<!-- HEADER -->
<? showHeader(); ?>

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-sm-6 text-center">

            <div class="wrapper">
                <? if (isset($_GET["register"])): ?>
                <p class="text-success">Registratie succesvol, log alsjeblieft in.</p>
                <? endif; ?>

                <? if (isLoggedIn()): ?>
                    <p>Je bent al ingelogd!</p>

                <? else: ?>
                <div id="formContent" class="shadow">
                    <!-- Login Form -->
                    <form method="post">
                        <br/>
                        <input type="text" id="login" name="email" placeholder="Email" value="<?= $email ?>" required>
                        <p class="text-danger"> <?= $emailErr ?> </p>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <p class="text-danger"> <?= $passErr ?> </p>
                        <input type="submit" value="Log In">
                    </form>

                </div>
                <? endif; ?>

            </div>

        </div>

    </div>

</div>


</body>
</html>