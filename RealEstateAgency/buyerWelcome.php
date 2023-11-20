<?php
session_start();
if(!isset($_SESSION['userID']) || $_SESSION['UserRole'] !== 'Buyer'){
    echo "You must be logged in as a buyer to access this page.";
    exit();
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

// Fetch the preferred location of the buyer
$stmt = $mysqli->prepare("SELECT PreferredLocation FROM Buyers WHERE UserID = ?");
$stmt->bind_param("i", $_SESSION['userID']);
$stmt->execute();
$result = $stmt->get_result();
$buyer = $result->fetch_assoc();
$preferredLocation = $buyer['PreferredLocation'];
$stmt->close();

// Building the query dynamically based on the filters
$query = "SELECT Properties.*, Agents.Name AS AgentName, Agents.Phone AS AgentPhone, Agents.Email AS AgentEmail FROM Properties JOIN Agents ON Properties.AgentID = Agents.AgentID WHERE 1=1";

if(!empty($_POST['minPrice'])) {
    $query .= " AND Price >= " . $_POST['minPrice'];
}
if(!empty($_POST['maxPrice'])) {
    $query .= " AND Price <= " . $_POST['maxPrice'];
}
if(!empty($_POST['type'])) {
    $query .= " AND Type = '" . $_POST['type'] . "'";
}
if(isset($_POST['preferredLocation']) && $_POST['preferredLocation'] == 'on') {
    $query .= " AND Location = '" . $preferredLocation . "'";
}

$result = $mysqli->query($query);
$properties = $result->fetch_all(MYSQLI_ASSOC);
$mysqli->close();
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="mycss.css">
</head>

<head>
    <title>Welcome, Buyer</title>
</head>
<body>
<h1>Welcome to the buyer's area</h1>

<form method="POST" action="buyerWelcome.php">
    <input type="hidden" name="action" value="logoff">
    <input type="submit" value="Log Off">
</form>

<form method="POST" action="buyerWelcome.php">
    <label for="minPrice">Minimum Price:</label>
    <input type="number" id="minPrice" name="minPrice" value="<?php echo isset($_POST['minPrice']) ? $_POST['minPrice'] : '' ?>">
    <label for="maxPrice">Maximum Price:</label>
    <input type="number" id="maxPrice" name="maxPrice" value="<?php echo isset($_POST['maxPrice']) ? $_POST['maxPrice'] : '' ?>">
    <label for="type">Property Type:</label>
    <input type="text" id="type" name="type" value="<?php echo isset($_POST['type']) ? $_POST['type'] : '' ?>">
    <input type="checkbox" id="preferredLocation" name="preferredLocation" <?php echo isset($_POST['preferredLocation']) ? 'checked' : '' ?>>
    <label for="preferredLocation">Only show properties in my preferred location</label>
    <input type="submit" value="Filter">
</form>
<form method="GET" action="buyerWelcome.php">
    <input type="submit" value="Reset Filters">
</form>
<?php if (empty($properties)): ?>
    <p>Sorry, there are no properties that match your filters. Please adjust your filters and try again.</p>
<?php else: ?>
    <p>Here are our properties:</p>

    <table>
        <tr>
            <th>Property ID</th>
            <th>Type</th>
            <th>Status</th>
            <th>Location</th>
            <th>Agent Name</th>
            <th>Price</th>
            <th>Contact</th>
            <th>Appointment</th>
        </tr>
        <?php foreach ($properties as $property): ?>
            <tr>
                <td><?php echo htmlspecialchars($property['PropertyID']); ?></td>
                <td><?php echo htmlspecialchars($property['Type']); ?></td>
                <td><?php echo htmlspecialchars($property['Status']); ?></td>
                <td><?php echo htmlspecialchars($property['Location']); ?></td>
                <td><?php echo htmlspecialchars($property['AgentName']); ?></td>
                <td><?php echo htmlspecialchars($property['Price']); ?></td>
                <td>
                    <button onclick="alert('Agent Contact: <?php echo $property['AgentPhone']; ?>, Email: <?php echo $property['AgentEmail']; ?>')">Contact Agent</button>
                </td>
                <td>
                    <a href="setAppointment.php?PropertyID=<?php echo $property['PropertyID']; ?>&AgentID=<?php echo $property['AgentID']; ?>"><button>Set Appointment</button></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>
