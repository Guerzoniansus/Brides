<html>
<head>
    <title>Producten toevoegen</title>

    <link rel="stylesheet" type="text/css" href="/mystyle.css">
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT']).'home_button.php'; ?>

<h1>Week 1 - Producten toevoegen</h1>

<?php

$name = $description = $price = $email = $category = "";
$soldout = false;
$nameErr = $descriptionErr = $priceErr = $emailErr = $categoryErr = $soldoutErr = "";
$ERROR_EMPTY = "Veld mag niet leeg zijn";
$output = "";

// Set to true if any input was wrong so nothing gets processed and printed
$err = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["name"])) {
        $nameErr = $ERROR_EMPTY;
        $err = true;
    } else {
        $name = test_input($_POST["name"]);

        if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $nameErr = "Alleen letters en spaties zijn toegestaan";
            $err = true;
        }

        else if (strlen($name) < 3 || strlen($name) > 50) {
            $nameErr = "Naam moet tussen 3 en 50 letters zijn";
            $err = true;
        }
    }

    if (!empty($_POST["description"])) {
        $description = test_input($_POST["description"]);

        if (strlen($description) > 2048) {
            $descriptionErr = "Beschrijving mag niet meer dan 2048 tekens zijn";
            $err = true;
        }
    }

    if (empty($_POST["price"])) {
        $priceErr = $ERROR_EMPTY;
        $err = true;
    } else {
        $price = test_input($_POST["price"]);
        $priceFloat = (float)$price;

        if (!filter_var($price, FILTER_VALIDATE_FLOAT)) {
            $priceErr = "Dit is geen getal";
            $err = true;
        }

        else if (strpos($price, ".") === false) {
            $priceErr = "Dit is geen komma getal. Gebruik een punt als komma.";
        }

        else if (strlen(explode(".", $price)[1]) != 2) {
            $priceErr = "Je moet 2 komma getallen hebben";
            $err = true;
        }
    }

    if (empty($_POST["category"])) {
        $categoryErr = "Hoe heb je dit ooit leeg gekregen????";
        $err = true;
    }

    else {
        $category = test_input($_POST["category"]);

        if ($category != "games" && $category!= "kitchen"
            && $category != "books" && $category != "clothes") {
            $categoryErr = "Hey, houd eens op de site te hacken!";
            $err = true;
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = $ERROR_EMPTY;
        $err = true;
    } else {
        $email = test_input($_POST["email"]);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Geen geldig email formaat";
            $err = true;
        }
    }

    if (!empty($_POST["soldout"])) {
        $soldout = true;
    }

    if ($err == false) {
        $output = "Naam: $name <br>Beschrijving: $description <br> Prijs: $price <br>Categorie: $category
        <br>Email: $email <br> Uitverkocht: " . ($soldout == true ? 'true' : 'false');

        $name = $description = $price = $email = $category = "";
        $soldout = false;
        $nameErr = $descriptionErr = $priceErr = $emailErr = $categoryErr = $soldoutErr = "";
    }

    else {
        $output = "";
    }

}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>

<form action=""  method="post">

    Naam: <input type="text" name="name" value="<?php echo $name;?>">
    <span class="error">* <?php echo $nameErr;?></span>
    <br>
    Beschrijving: <br> <textarea name="description" rows="5" cols="50"><?php echo $description;?></textarea>
    <span class="error"><?php echo $descriptionErr;?></span>
    <br><br>
    Prijs: <input type="text" name="price" value="<?php echo $price;?>">
    <span class="error">* <?php echo $priceErr;?></span>
    <br>
    Categorie: <select name="category"">
        <option name="games" value="games" <?php echo($category == "games" ? 'selected=\"selected\"' : '')?> >Games</option>
        <option name="books" value="books" <?php echo($category == "books" ? 'selected=\"selected\"' : '')?> >Boeken</option>
        <option name="kitchen" value="kitchen" <?php echo($category == "kitchen" ? 'selected=\"selected\"' : '')?> >Keuken</option>
        <option name="clothes" value="clothes" <?php echo($category == "clothes" ? 'selected=\"selected\"' : '')?> >Kleren</option>
    </select>
    <span class="error">* <?php echo $categoryErr;?></span>
    <br>
    E-mail: <input type="text" name="email" value="<?php echo $email;?>">
    <span class="error">* <?php echo $emailErr;?></span>
    <br>
    Uitverkocht: <input type="checkbox" name="soldout" <?php echo($soldout == true ? 'checked=\"checked\"' : '');?>>
    <span class="error">* <?php echo $soldoutErr;?></span>
    <br>
    <input type="submit" name="submit" value="Submit"/>
</form>
<p><span class="error">Velden met * zijn verplicht</span></p>
<h2>Output</h2>
<p><?php echo $output ?></p>

</body>
</html>