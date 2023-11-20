<?php
// start the session
session_start();

// check if the user is logged in as an administrator
if($_SESSION['UserRole'] != 'Administrator'){
    die("Access denied. Please log in as an Administrator.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'logoff') {
    session_destroy();
    header('Location: welcome.php');
    exit();
}
$mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
}

// Get the total number of properties
$result = $mysqli->query("SELECT COUNT(*) as TotalProperties FROM Properties");
$row = $result->fetch_assoc();
$totalProperties = $row['TotalProperties'];

// Get the number of properties sold this year
$result = $mysqli->query("SELECT COUNT(*) as PropertiesSoldThisYear FROM Transactions WHERE YEAR(Date) = YEAR(CURDATE())");
$row = $result->fetch_assoc();
$propertiesSoldThisYear = $row['PropertiesSoldThisYear'];

// Get the total price of properties sold this month
$result = $mysqli->query("SELECT SUM(SalePrice) as TotalSoldThisMonth FROM Transactions WHERE MONTH(Date) = MONTH(CURDATE()) AND YEAR(Date) = YEAR(CURDATE())");
$row = $result->fetch_assoc();
$totalSoldThisMonth = $row['TotalSoldThisMonth'];

// Get the top 3 agents
$result = $mysqli->query("SELECT Agents.Name, COUNT(*) as TotalSales FROM Transactions JOIN Agents ON Transactions.AgentID = Agents.AgentID GROUP BY Agents.Name ORDER BY TotalSales DESC LIMIT 3");
$topAgents = $result->fetch_all(MYSQLI_ASSOC);

// Get the top 3 properties
$result = $mysqli->query("SELECT Properties.PropertyID, COUNT(*) as TotalViewings FROM PropertyViewings JOIN Properties ON PropertyViewings.PropertyID = Properties.PropertyID GROUP BY Properties.PropertyID ORDER BY TotalViewings DESC LIMIT 3");
$topProperties = $result->fetch_all(MYSQLI_ASSOC);

?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="mycss.css">
</head>

<head>
    <title>Admin Dashboard</title>
</head>
<body>
<h1>Admin Dashboard</h1>
<form method="POST" action="dashboard.php">

<input type="hidden" name="action" value="logoff">
<input type="submit" value="Log Off">

</form>
<p>Total properties listed: <?php echo $totalProperties ? $totalProperties : "No properties listed."; ?></p>
<p>Number of properties sold this year: <?php echo $propertiesSoldThisYear ? $propertiesSoldThisYear : "No properties sold this year."; ?></p>
<p>Total price of properties sold this month: <?php echo $totalSoldThisMonth ? $totalSoldThisMonth : "No properties sold this month."; ?></p>

<h2>Top 3 Agents</h2>
<?php if (count($topAgents) > 0) { ?>
    <ul>
        <?php foreach($topAgents as $agent) { ?>
            <li><?php echo $agent['Name']; ?> - <?php echo $agent['TotalSales']; ?> sales</li>
        <?php } ?>
    </ul>
<?php } else { echo "No sales by agents this month/year."; } ?>

<h2>Top 3 Properties</h2>
<?php if (count($topProperties) > 0) { ?>
    <ul>
        <?php foreach($topProperties as $property) { ?>
            <li>Property ID <?php echo $property['PropertyID']; ?> - <?php echo $property['TotalViewings']; ?> viewings</li>
        <?php } ?>
    </ul>
<?php } else { echo "No property viewings this month/year."; } ?>
<p>
<a href="manageUsers.php"><button>Manage Users</button></a>
</p>
</body>
</html>

