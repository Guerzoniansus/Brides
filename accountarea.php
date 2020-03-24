<?php

$listURL = "login.php";

if (isLoggedIn() == false && isLoggedInFake() == true) {
    $listURL = "login.php?list=". getUser()->listID;
}

else if (isLoggedIn()) {
    $listURL = "mylist.php?list=". getUser()->listID;
}

if (isLoggedIn() || isLoggedInFake()): ?>

<!-- Show account info -->
<h1>Mijn account</h1>
<p>Hallo <strong><?= getUser()->email ?></strong></p>
<h3>Je code: <strong><?= getUser()->listID ?></strong></h3>
<br> <br>
<a href="<?= $listURL ?>"><button type="button" class="btn btn-primary">Ga naar mijn lijst</button></a>
<br/>
<br/>
<br/>
<a href="index.php?logout=true"><button type="button" class="btn btn-sm btn-primary">Uitloggen</button></a>

<? else: ?>
<!-- Show login / register buttons -->
<h1>Mijn account</h1>
<a href="login.php"><button type="button" class="btn btn-primary">Inloggen</button></a>
<a href="register.php"><button type="button" class="btn btn-primary">Registreren</button></a>
<? endif ?>

