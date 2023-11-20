<?php
session_start();

$mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
}

// Handling the form submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $mysqli->prepare("UPDATE Properties SET Location = ?, Price = ?, Type = ?, Status = ? WHERE PropertyID = ? AND AgentID = ?");
    $stmt->bind_param("sdssii", $_POST['Location'], $_POST['Price'], $_POST['Type'], $_POST['Status'], $_POST['PropertyID'], $_SESSION['userID']);
    $stmt->execute();
    $stmt->close();

    header("Location: agentWelcome.php");
    exit();
}

// Fetching the existing property details
if(!isset($_GET['PropertyID'])) {
    echo "No property specified.";
    exit();
}

$stmt = $mysqli->prepare("SELECT * FROM Properties WHERE PropertyID = ? AND AgentID = ?");
$stmt->bind_param("ii", $_GET['PropertyID'], $_SESSION['userID']);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

if(empty($property)) {
    echo "No such property found.";
    exit();
}

$stmt->close();
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="mycss.css">
</head>

<head>
    <title>Edit Property</title>
</head>
<body>
<h1>Edit Property #<?php echo $property['PropertyID']; ?></h1>

<form method="POST" action="propertyEdit.php">
    <input type="hidden" id="PropertyID" name="PropertyID" value="<?php echo $property['PropertyID']; ?>">

    <label for="Location">Property Location:</label><br>
    <input type="text" id="Location" name="Location" value="<?php echo $property['Location']; ?>"><br>

    <label for="Price">Property Price:</label><br>
    <input type="number" step="100.00" id="Price" name="Price" value="<?php echo $property['Price']; ?>"><br>

    <label for="Type">Property Type:</label><br>
    <input type="text" id="Type" name="Type" value="<?php echo $property['Type']; ?>"><br>

    <label for="Status">Property Status:</label><br>
    <input type="text" id="Status" name="Status" value="<?php echo $property['Status']; ?>"><br>

    <input type="submit" value="Update Property">
</form>

<a href="agentWelcome.php">Back to Dashboard</a>
</body>
</html>
