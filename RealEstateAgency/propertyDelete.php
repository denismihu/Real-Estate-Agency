<?php
session_start();

// Check if PropertyID is set in GET
if(!isset($_GET['PropertyID'])) {
    echo "No property specified.";
    exit();
}

$mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
}

$stmt = $mysqli->prepare("DELETE FROM Properties WHERE PropertyID = ? AND AgentID = ?");
$stmt->bind_param("ii", $_GET['PropertyID'], $_SESSION['userID']);
$stmt->execute();
$stmt->close();

header("Location: agentWelcome.php");
exit();
?>

