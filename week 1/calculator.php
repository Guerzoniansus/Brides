<html>
<head>
    <title>Week 1 - Calculator</title>

    <style>
        input[type=radio] {
            margin-right: 30px;
            color: blue;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="/mystyle.css">
</head>
<body>

<?php include($_SERVER['DOCUMENT_ROOT']).'home_button.php'; ?>

<h1>Week 1 - Calculator</h1>

<?php

$number1 = "";
$number2 = "";
$result = "";
$action = "plus";

$actionErr = "";
$number1Err = $number2Err = "";
$NUMBER_ERR_EMPTY= "Error: Mag niet leeg zijn!";
$NUMBER_ERR_NOT_NUMBER = "Error: Dat is geen getal!";

// Set to false if something is incorrect, input != numbers, input == empty, weird action etc
$readyForCalculation = true;

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    if (isset($_POST["reset"])) {
        $number1 = "";
        $number2 = "";
        $action = "plus";
        $result = "";
        $actionErr = "";
        $number1Err = "";
        $number2Err = "";
        $readyForCalculation = false;
    }

    else {

        // *-*-*-* INPUT CHECKS *-*-*-*-*-*-*-*-*-*

        // Is empty?
        if (!isset($_POST["number1"])) {
            $number1Err = $NUMBER_ERR_EMPTY;
            $readyForCalculation = false;
        }

        // Is number?
        else if (!is_numeric($_POST["number1"])) {
            $number1Err = $NUMBER_ERR_NOT_NUMBER;
            $readyForCalculation = false;
        }

        else {
            $number1 = test_input($_POST["number1"]);
        }

        // Is number?
        if (!isset($_POST["number2"])) {
            $number2Err = $NUMBER_ERR_EMPTY;
            $readyForCalculation = false;
        }

        // Is empty?
        else if (!is_numeric($_POST["number2"])) {
            $number2Err = $NUMBER_ERR_NOT_NUMBER;
            $readyForCalculation = false;
        }

        else {
            $number2 = test_input($_POST["number2"]);
        }

        if (empty($_POST["action"])) {
            $actionErr = "Error: Je moet iets kiezen!";
            $readyForCalculation = false;
        } else {
            $action = test_input($_POST["action"]);
        }

        // *-*-*-* END OF INPUT CHECKS *-*-*-*-*-*-*-*-*-*

        if ($action != "plus" && $action != "min" && $action != "keer" && $action != "delen") {
            $actionErr = "Error: De site niet hacken a.u.b.";
            $readyForCalculation = false;
        }

        if ($readyForCalculation) {
            $result = calculate($number1, $number2, $action);
            $number1 = $result;
            $number2 = "";
        }
    }

}


// $action = "plus", "min", "keer", or "delen"
// calculates number1 with number2 and returns result
function calculate($number1, $number2, $action) {
    $result = 0;

    switch ($action) {
        case "plus": $result = $number1 + $number2;
            break;
        case "min": $result = $number1 - $number2;
            break;
        case "keer": $result = $number1 * $number2;
            break;
        case "delen": $result = $number1 / $number2;
            break;
    }

    return $result;
}

function alert() {
    echo '<script>alert();</script>';
}


function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>



<form action="" method="post">
    Getal 1: <input type="text" name="number1" value="<?php echo $number1 ?>">
    <span class="error"><?php echo $number1Err?></span> <br>
    Getal 2: <input type="text" name="number2" value="<?php echo $number2 ?>">
    <span class="error"><?php echo $number2Err?></span> <br>
    +<input type="radio" name="action" value="plus" <?php echo($action == "plus" ? 'checked="checked"' : '') ?> >
    -<input type="radio" name="action" value="min" <?php echo($action == "min" ? 'checked="checked"' : '') ?> >
    *<input type="radio" name="action" value="keer" <?php echo($action == "keer" ? 'checked="checked"' : '') ?> >
    /<input type="radio" name="action" value="delen" <?php echo($action == "delen" ? 'checked="checked"' : '') ?> >
    <span class="error"><?php echo $actionErr?></span><br>
    <input type="submit" name="submit" value="Submit">
    <input type="submit" name="reset" value="Reset">
    <!-- DIT IS KAPOT <button name="reset" type="button" onclick="<?php alert() ?>">Reset button</button> -->
</form>

<p>Result: <?php echo $result?></p>



</body>
</html>