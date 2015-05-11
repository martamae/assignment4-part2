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
    include "secret.php";
    $validInput = false;

    //Connecting
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu", "wegnerma-db", $password, "wegnerma-db");

    if (!$mysqli|| $mysqli->connect_errno) {
        echo "Failed to connect:" . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }

    if($_POST['addVid']) {
        $nameInput = $_POST["name"];
        $category = $_POST["category"];
        $lengthInput = $_POST["length"];

        $validInput = true;

        if (!$nameInput) {
            echo "Name cannot be blank<br>";
            $validInput = false;
        }
        if (!$length < 0) {
            echo "Length must be a positive value<br>";
            $validInput = false;
        }
    }

    if($validInput == true) {
        //Define prepare statement and test if failed
        if (!($statement = $mysqli->prepare("INSERT INTO video_store(name, category, length) VALUES (?, ?, ?)"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }

        //Binds parameters and test result
        if (!($statement->bind_param('ssi', $nameInput, $category, $lengthInput))) {
            echo "Binding parameters failed: " . $statement->errno . " " . $statement->error;
        }

        //Execute
        $statement->execute();
    }

    if($_POST["in"]) {
        $vidId = $_POST['in'];
        $mysqli->query("UPDATE video_store SET rented=0 WHERE id = '".$vidId."'");
    }

    if($_POST["out"]) {
        $vidId = $_POST["out"];
        $mysqli->query("UPDATE video_store SET rented=1 WHERE id = '".$vidId."'");
    }

    if($_POST["Delete"]) {
        $vidId = $_POST['Delete'];
        $mysqli->query("DELETE FROM video_store WHERE id = '".$vidId."'");
    }

    if($_POST["DeleteAll"]) {
        $mysqli->query("TRUNCATE TABLE video_store");
    }

    if($_GET["categChoice"]) {
        $categSelect = $_GET["categChoice"];
    }

    $categoryList = $mysqli->query("SELECT distinct category FROM video_store");
    echo '<form method="GET">';
    echo '<select name="categChoice">';
    echo '<option value="All">All</option>';
    while ($row = $categoryList->fetch_assoc()) {
        if ($row['category'] != NULL) {
            echo '<option value="' . $row['category'] . '">' . $row['category'] . '</option>';
        }
    }
    echo '</select> <input type="submit" value="Select Category"></form>';

   if ($categSelect != "All" && $categSelect != NULL) {
       //Select All data from table
        $vidTable = $mysqli->query("SELECT id, name, category, length, rented FROM video_store WHERE category ='".$categSelect."'");
   }
    else {
        //Query to select specific category data from table
        $vidTable = $mysqli->query("SELECT id, name, category, length, rented FROM video_store");
    }

    echo "<table>";
    echo "<tr> <td> Name <td>Category <td>Length <td>Checked Out/Available <td>Check-in/out <td> Delete";
    while ($row = $vidTable->fetch_assoc()) {
        echo "<tr> <td>" . $row['name'] . "<td>" . $row['category'] . "<td>" . $row['length'] . "<td>";
        if ($row['rented'] == 1) {
            echo "Checked Out";
            echo '<td> <form method="POST"><button type="submit" value="'.$row['id'].'" name="in">Check-in</button></form>';
        }
        else {
            echo "Available";
            echo '<td> <form method="POST"><button type="submit" value="'.$row['id'].'" name ="out">Check-out</button></form>';
        }
        echo '<td> <form method="POST"><button type="submit" value="'.$row['id'].'" name="Delete">Delete</button></form>';
    }
    echo "</table>";

    echo '<form method="POST"><input type="submit" value="Delete All Videos" name="DeleteAll"></form>';

    $mysqli->close();
?>
    </body>
</html>
