<?php
require ($_SERVER['DOCUMENT_ROOT']) . '/helpers.php';

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <? addHead("Registreren"); ?>

    <link rel="stylesheet" type="text/css" href="css/loginregister.css">
    <script type="text/javascript" src="js/register.js"></script>
</head>
<body>

<!-- HEADER -->
<? showHeader(); ?>

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-sm-6 text-center">

            <div class="wrapper">
                <div id="formContent" class="shadow">

                    <? if (isLoggedIn()): ?>
                    <p>Je bent al ingelogd!</p>

                    <? else: ?>
                    <!-- Register Form -->
                    <form method="post">
                        <br/>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                        <p id="emailErr" class="text-danger"></p>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <p id="passErr" class="text-danger"></p>
                        <input type="password" id="password2" name="password2" placeholder="Password herhalen" required>
                        <p id="pass2Err" class="text-danger"></p>
                        <input type="submit" value="Registreren">
                        <p id="submitErr" class="text-danger"></p>
                    </form>
                    <? endif; ?>

                </div>
            </div>

        </div>

    </div>

</div>

</body>
</html>