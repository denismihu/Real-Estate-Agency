<?php
session_start();
if(!isset($_SESSION['userID']) || $_SESSION['UserRole'] !== 'Agent'){
    echo "You must be logged in as an agent to access this page.";
    exit();
}

$mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
}

$stmt = $mysqli->prepare("SELECT Properties.*, Agents.Name AS AgentName FROM Properties JOIN Agents ON Properties.AgentID = Agents.AgentID");
$stmt->execute();
$result = $stmt->get_result();

$allProperties = $result->fetch_all(MYSQLI_ASSOC);

$stmt = $mysqli->prepare("SELECT * FROM Properties WHERE AgentID = ?");
$stmt->bind_param("i", $_SESSION['userID']); // assuming 'userID' is the AgentID
$stmt->execute();
$result = $stmt->get_result();

$agentProperties = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$mysqli->close();
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="mycss.css">
</head>

<head>
    <title>Welcome, Agent</title>
</head>
<body>
<h1>Welcome to the agent's area</h1>

<p>Here are all properties:</p>

<!-- display all properties -->
<table>
    <tr>
        <th>Property ID</th>
        <th>Location</th>
        <th>Price($)</th>
        <th>Type</th>
        <th>Status</th>
        <th>Agent Name</th>
    </tr>
    <?php foreach ($allProperties as $property): ?>
        <tr>
            <td><?php echo htmlspecialchars($property['PropertyID']); ?></td>
            <td><?php echo htmlspecialchars($property['Location']); ?></td>
            <td><?php echo htmlspecialchars($property['Price']); ?></td>
            <td><?php echo htmlspecialchars($property['Type']); ?></td>
            <td><?php echo htmlspecialchars($property['Status']); ?></td>
            <td><?php echo htmlspecialchars($property['AgentName']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<p>Here are your properties:</p>
<table>
    <tr>
        <th>Property ID</th>
        <th>Location</th>
        <th>Price($)</th>
        <th>Type</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($agentProperties as $property): ?>
        <tr>
            <td><?php echo htmlspecialchars($property['PropertyID']); ?></td>
            <td><?php echo htmlspecialchars($property['Location']); ?></td>
            <td><?php echo htmlspecialchars($property['Price']); ?></td>
            <td><?php echo htmlspecialchars($property['Type']); ?></td>
            <td><?php echo htmlspecialchars($property['Status']); ?></td>
            <td>
                <a href="propertyEdit.php?PropertyID=<?php echo $property['PropertyID']; ?>">Edit</a> |
                <a href="propertyDelete.php?PropertyID=<?php echo $property['PropertyID']; ?>" onclick="return confirm('Are you sure you want to delete this property?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<a href="propertyAdd.php">Add a new property</a> <br>
<a href="registerTransaction.php">Register a transaction</a> <br>
<a href="welcome.php">Log Off</a>
</body>
</html>
