<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Assignment 4 - Part 2</title>
    </head>
    <body>
    <form method="POST">
        <p>Name: <input type="text" name="name"></p>
        <p>Category: <input type="text" name="category"></p>
        <p>Length: <input type="number" name="length" min ="0"></p>
        <p><input type="submit" value="Add Video" name="addVid"></p>
    </form>
<?php
    $validInput = false;

    //Connecting
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "wegnerma-db", "Ejey1gzLdNY5wWpw", "wegnerma-db");

    if (!$mysqli|| $mysqli->connect_errno) {
        echo "Failed to connect:" . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    if($_POST['addVid']) {
        $nameInput = $_POST["name"];
        $category = $_POST["category"];
        $lengthInput = $_POST["length"];

        $validInput = true;

        if (!$name) {
            echo "Name cannot be blank<br>";
            $validInput = false;
        }
        if (!$category) {
            echo "Category cannot be blank<br>";
            $validInput = false;
        }
        if (!$length) {
            echo "Length cannot be blank<br>";
            $validInput = false;
        }
        if (!$length < 0) {
            echo "Length must be a positive value<br>";
            $validInput = false;
        }
    }

    //Define prepare statement and test if failed
    if (!($statement = $mysqli->prepare("INSERT INTO video_store(name, category, length) VALUES (?, ?, ?)"))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    //Binds parameters and test result
    if (!($statement->bind_param('ssi', $nameInput, $category, $lengthInput))) {
        echo "Binding parameters failed: " . $statement->errno . " " . $statement->error;
    }

    //Execute and test result
    $statement->execute();
    //    echo "Execute failed: " . $statement->errno . " " . $statement->error;
    //}
?>
    </body>
</html>
