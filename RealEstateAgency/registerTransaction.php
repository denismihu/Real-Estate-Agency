<?php
session_start();

if(!isset($_SESSION['userID']) || $_SESSION['UserRole'] !== 'Agent'){
    echo "You must be logged in as an agent to access this page.";
    exit();
}

// Form handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buyerID = $_POST['buyerID'];
    $propertyID = $_POST['propertyID'];
    $salePrice = $_POST['salePrice'];
    $date = $_POST['date'];

    $mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    // Insert into database
    $stmt = $mysqli->prepare("INSERT INTO Transactions (BuyerID, PropertyID, AgentID, SalePrice, Date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiids", $buyerID, $propertyID, $_SESSION['userID'], $salePrice, $date);
    $stmt->execute();
    $stmt->close();

    $mysqli->close();

    // Redirect to agentWelcome.php
    header('Location: agentWelcome.php');
    exit();
}

?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="mycss.css">
</head>

<head>
    <title>Register Transaction</title>
</head>
<body>
<h1>Register Transaction</h1>

<form method="POST" action="registerTransaction.php">
    <label for="buyerID">Buyer ID:</label>
    <input type="number" id="buyerID" name="buyerID" required>
    <label for="propertyID">Property ID:</label>
    <input type="number" id="propertyID" name="propertyID" required>
    <label for="salePrice">Sale Price:</label>
    <input type="number" id="salePrice" name="salePrice" step="0.01" required>
    <label for="date">Date (YYYY-MM-DD):</label>
    <input type="date" id="date" name="date" required>
    <input type="submit" value="Register Transaction">
</form>

</body>
</html>

