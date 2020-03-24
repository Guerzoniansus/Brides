<?php

// TO IMPORT: require ($_SERVER['DOCUMENT_ROOT']) . '/helpers.php';

class User {
    public $email;
    // $password = hash as taken from database, not actual password
    public $password;
    public $sessionID;
    public $listID;
}

class GiftList {
    public $listID;
    public $name1;
    public $name2;
    public $date;
    public $description;
    public $items;
}

class Item {
    public $itemID;
    public $itemName;
    public $itemIndex;
    public $itemOwner;
    public $listID;
}


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// This NEEDS to be after classes so it can deserialize objects
function getUser() : User {
    return unserialize($_SESSION["user"]);
}

function setUser(User $user) {
    $_SESSION["user"] = serialize($user);
    $_SESSION["fakelogin"] = "false";
}

/**
 * Sets a "fake login". They can see their own list ID so they can share it, but they will
 * still need to manually log in if they actually want to edit their list
 * @param string $user
 */
function setUserFake(User $user) {
    $_SESSION["user"] = serialize($user);
    $_SESSION["fakelogin"] = "true";
}

/**
 * Return true if it's a REAL login, through manually entering email and pasword
 * @return bool
 */
function isLoggedIn() {
    if (isset($_SESSION["user"])) {
        if (isset($_SESSION["fakelogin"])) {
            if ($_SESSION["fakelogin"] == "false") {
                return true;
            }
        }
    }

    return false;
}

/**
 * Return true if "logged in" via cookie and has NOT entered email and password yet
 * @return bool
 */
function isLoggedInFake() {
    if (isset($_SESSION["user"])) {
        if (isset($_SESSION["fakelogin"])) {
            if ($_SESSION["fakelogin"] == "true") {
                return true;
            }
        }
    }

    return false;
}

// Check if user pressed log out button
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["logout"])) {
        if ($_GET["logout"] == "true") {
            session_destroy();
            // Remove cookie
            setcookie("sessionID", "", -100);
            session_start();
        }
    }
}

function addHead($title) {
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> <?= $title ?> </title>

    <!-- Import CSS -->

    <link rel="stylesheet" type="text/css" href="css/style.css">

    <!-- Import Bootstrap and JQuery -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <!-- Import Font Awesome -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">

    <?php
}

function showHeader() {
    ?>
    <a href="index.php" id="header-link" class="text-dark text-center">
        <div class="jumbotron">
            <h1 class="display-3">Happy Brides</h1>
            <p>Makkelijk online wensenlijsten maken voor je bruiloft</p>
        </div>
    </a>
<?php
}

function showAccountArea() {
    require ($_SERVER['DOCUMENT_ROOT']) . '/accountarea.php';
}

class DB
{

    public $host = "localhost";
    public $databaseName = "BrideDB";
    public $username = "student";
    public $password = "student";

    // Generate random 7 character long listID
    private function generateListID() {
        $amountofchars = 7;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $amountofchars; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    /**
     * @return string
     */
    private function getConnectionString()
    {
        $dns = "mysql:host=$this->host;dbname=$this->databaseName";
        return $dns;
    }

    /**
     * @return PDO
     */
    private function createConnection()
    {
        $conn = new PDO($this->getConnectionString(), $this->username, $this->password);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    }

    /**
     * Return array of all items within a certain list
     * @param $listID
     * @return array
     */
    public function getItems($listID) : array
    {
        $sql = "SELECT * FROM Item WHERE listID = :listID ORDER BY itemIndex ASC";

        $conn = $this->createConnection();
        $stmtSelect = $conn->prepare($sql);
        $stmtSelect->bindValue(":listID", $listID, PDO::PARAM_STR);
        $stmtSelect->execute();

        $rows = $stmtSelect->fetchAll(PDO::FETCH_CLASS, "Item");

        $conn = null;
        return $rows;
    }


    /**
     * @param string $email
     * @return User|null
     */
    public function getUser(string $email) : ?User {
        $sqlEdit = "SELECT * FROM User WHERE email = :email";

        $conn = $this->createConnection();
        $stmtEdit = $conn->prepare($sqlEdit);
        $stmtEdit->bindValue(":email", $email, PDO::PARAM_STR);

        if ($stmtEdit->execute()) {
            $result = $stmtEdit->fetchObject("User");
            if($result === false)
            {
                $result = null;
            }
        } else {
            $result = null;
        }

        $conn = null;
        return $result;
    }

    /**
     * @param string $sessionID
     * @return User|null
     */
    public function getUserFromSessionID(string $sessionID) : ?User {
        // Limit to 1 just for the 1 in billion chance that 2 people have same sessionID
        $sqlEdit = "SELECT * FROM User WHERE sessionID = :sessionID LIMIT 1";

        $conn = $this->createConnection();
        $stmtEdit = $conn->prepare($sqlEdit);
        $stmtEdit->bindValue(":sessionID", $sessionID, PDO::PARAM_STR);

        if ($stmtEdit->execute()) {
            $result = $stmtEdit->fetchObject("User");
            if($result === false)
            {
                $result = null;
            }
        } else {
            $result = null;
        }

        $conn = null;
        return $result;
    }

    /**
     * @param string $listID
     * @return GiftList|null
     */
    public function getList(string $listID) : ?GiftList {
        $sqlEdit = "SELECT * FROM GiftList WHERE listID = :listID";

        $conn = $this->createConnection();
        $stmtEdit = $conn->prepare($sqlEdit);
        $stmtEdit->bindValue(":listID", $listID, PDO::PARAM_STR);

        if ($stmtEdit->execute()) {
            $result = $stmtEdit->fetchObject("GiftList");
            if($result === false)
            {
                $result = null;
            }
        } else {
            $result = null;
        }
        $conn = null;

        // Retrieve the items
        if ($result != null) {
            $result->items = $this->getItems($result->listID);
        }

        return $result;
    }

    /** Delete ALL items from a specific list
     * @param $listID
     * @return bool if it went succesful
     */
    public function deleteItems(string $listID) : bool
    {
        $sqlDelete = "DELETE FROM Item WHERE listID = :listID";

        $conn = $this->createConnection();
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bindValue(":listID", $listID, PDO::PARAM_STR);

        $result = false;
        if ($stmtDelete->execute()) {
            $result = true;
        }

        $conn = null;
        return $result;
    }

    /**
     * Returns true if successful, false if something went wrong
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function addUser(string $email, string $password)
    {
        // Create a new list for the user and check if it went succesfully
        $listID = $this->createList();

        if ($listID == "error") {
            return false;
        }

        $sqlInsert = "INSERT INTO User (email, password, listID) VALUES (:email, :password, :listID)";

        $conn = $this->createConnection();
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bindValue(":email", $email, PDO::PARAM_STR);
        $stmtInsert->bindValue(":password", $password, PDO::PARAM_STR);
        $stmtInsert->bindValue(":listID", $listID, PDO::PARAM_STR);

        $result = false;
        if ($stmtInsert->execute() && $stmtInsert->rowCount() == 1) {
            $result = true;
        }

        $conn = null;
        return $result;
    }

    /**
     * Adds all items from array of items to database, returns if it was succesfull
     * @param array $items
     * @param PDO $conn
     * @return bool
     */
    public function addItems(array $items) : bool
    {
        $itemName = $itemIndex = $itemOwner = $listID = "";

        $sqlInsert = "INSERT INTO Item (itemName, itemIndex, itemOwner, listID) VALUES (:itemName, :itemIndex, :itemOwner, :listID)";

        $conn = $this->createConnection();
        $stmtInsert = $conn->prepare($sqlInsert);

        $result = false;

        // This is NOT efficient. I tried a more efficient way but I couldn't fix the bugs
        foreach ($items as $item) {

            $stmtInsert->bindValue(":itemName", $item->itemName, PDO::PARAM_STR);
            $stmtInsert->bindValue(":itemIndex", $item->itemIndex, PDO::PARAM_INT);
            $stmtInsert->bindValue(":itemOwner", $item->itemOwner, PDO::PARAM_STR);
            $stmtInsert->bindValue(":listID", $item->listID, PDO::PARAM_STR);

            if ($stmtInsert->execute()) $result = true;
        }

        $conn = null;

        return $result;
    }

    /**
     * Returns listID of newly created list, or "error" if something went wrong
     * @return string
     */
    public function createList() : string
    {

        // Generate random list ID
        $listID = $this->generateListID();

        // Keep generating until the ID is unique
        while ($this->getList($listID) != null) {
            $listID = $this->generateListID();
        }

        $sqlInsert = "INSERT INTO GiftList (listID) VALUES (:listID)";

        $conn = $this->createConnection();
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bindValue(":listID", $listID, PDO::PARAM_STR);

        $result = "error";
        if ($stmtInsert->execute() && $stmtInsert->rowCount() == 1) {
            //$result = $conn->lastInsertId(); DIT GEEFT ERRORS!!!!!
            $result = $listID;
        }

        $conn = null;
        return $result;
    }

    /**
     * Updates list, both info and items, and returns if "success", or where it went wrong
     * Update list info -> Delete all old items -> Add all new items
     * @param GiftList $list
     * @return string "success" if no problems, otherwise where it went wrong
     */
    public function updateList(GiftList $list) : string
    {
        $this->deleteItems($list->listID);

        $conn = $this->createConnection();

        $sqlInsert = "UPDATE GiftList SET name1 = :name1, name2 = :name2, date = :newDate, description = :description WHERE listID = :listID";

        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bindValue(":listID", $list->listID, PDO::PARAM_STR);
        $stmtInsert->bindValue(":name1", $list->name1, PDO::PARAM_STR);
        $stmtInsert->bindValue(":name2", $list->name2, PDO::PARAM_STR);
        $stmtInsert->bindValue(":newDate", $list->date, PDO::PARAM_STR);
        $stmtInsert->bindValue(":description", $list->description, PDO::PARAM_STR);

        $result = "list";
        if ($stmtInsert->execute()) {
            //$result = $conn->lastInsertId(); DIT GEEFT ERRORS!!!!!
            $result = "success";
        }

        $conn = null;

        // Add all items
        if ($result == "success") {
            if ($this->addItems($list->items) == false) {
                $result = "items";
            }
        }


        return $result;
    }


    /**
     * Return true if worked, return false if database error
     * @param $email
     * @param $sessionID
     * @return bool
     */
    public function updateUserSessionID($email, $sessionID) {
        $sqlEdit = "UPDATE User SET sessionID = :sessionID WHERE email = :email";

        $stmtEdit = $conn = $this->createConnection()->prepare($sqlEdit);

        $stmtEdit->bindValue(":sessionID", $sessionID, PDO::PARAM_STR);
        $stmtEdit->bindValue(":email", $email, PDO::PARAM_STR);

        $result = false;
        if ($stmtEdit->execute()) {
            $result = true;
        }

        $conn = null;
        return $result;
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>