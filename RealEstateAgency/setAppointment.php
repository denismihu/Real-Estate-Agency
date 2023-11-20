<?php
session_start();

if(!isset($_SESSION['userID']) || $_SESSION['UserRole'] !== 'Buyer'){
    echo "You must be logged in as a buyer to access this page.";
    exit();
}

$mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
}

// Handling the form submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $mysqli->prepare("INSERT INTO PropertyViewings (BuyerID, PropertyID, AgentID, ViewingDate) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $_SESSION['userID'], $_GET['PropertyID'], $_GET['AgentID'], $_POST['ViewingDate']);
    $stmt->execute();
    $stmt->close();

    echo "<script type='text/javascript'>
            alert('You will receive a confirmation email in maximum 24h');
            setTimeout(function() {
                window.location.href = 'buyerWelcome.php';
            }, 2000);
        </script>";

    exit();
}
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="mycss.css">
</head>

<head>
    <title>Set Appointment</title>
</head>
<body>
<h1>Set Appointment</h1>

<form method="POST" action="setAppointment.php?PropertyID=<?php echo $_GET['PropertyID']; ?>&AgentID=<?php echo $_GET['AgentID']; ?>">
    <label for="ViewingDate">Choose a date for your viewing:</label><br>
    <input type="date" id="ViewingDate" name="ViewingDate"><br>
    <input type="submit" value="Set Appointment">
</form>

<a href="buyerWelcome.php">Back to Dashboard</a>
</body>
</html>
