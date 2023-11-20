<?php
session_start();

// Handling the form submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    // Prepare an insert statement
    $stmt = $mysqli->prepare("INSERT INTO Properties (Location, Price, Type, Status, AgentID) VALUES (?, ?, ?, ?, ?)");

    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("sdssi", $location, $price, $propertyType, $propertyStatus, $agentID);

    // Set values
    $location = $_POST['Location'];
    $price = $_POST['Price'];
    $propertyType = $_POST['Type'];
    $propertyStatus = $_POST['Status'];
    $agentID = $_SESSION['userID'];  // AgentID is taken from the session

    // Attempt to execute the prepared statement
    if($stmt->execute()){
        // Redirect to agent welcome page
        header("location: agentWelcome.php");
    } else{
        echo "Something went wrong. Please try again later.";
    }

    // Close statement
    $stmt->close();

    // Close connection
    $mysqli->close();
}
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="mycss.css">
</head>

<head>
    <title>Add a new property</title>
</head>
<body>
<h1>Add a new property</h1>

<form method="POST" action="propertyAdd.php">
    <label for="Location">Location:</label><br>
    <input type="text" id="Location" name="Location" required><br>

    <label for="Price">Price:</label><br>
    <input type="number" step="0.01" id="Price" name="Price" required><br>

    <label for="Type">Property Type:</label><br>
    <input type="text" id="Type" name="Type" required><br>

    <label for="Status">Property Status:</label><br>
    <input type="text" id="Status" name="Status" required><br>

    <input type="submit" value="Add Property">
</form>

<a href="agentWelcome.php">Back to Dashboard</a>
</body>
</html>
