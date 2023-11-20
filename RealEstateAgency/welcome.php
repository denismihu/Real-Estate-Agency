<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
}

$stmt = $mysqli->prepare("SELECT PropertyID, Type, Status FROM Properties");
$stmt->execute();
$result = $stmt->get_result();

$properties = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="mycss.css">
</head>
<head>
    <title>Welcome to RealEstateAgency</title>
</head>
<body>
<h1>Welcome to RealEstateAgency</h1>
<a href="authentication.php">Login</a>
<?php if (empty($properties)): ?>
    <p>Sorry, for the moment there are no properties available. We recommend checking back every day at 10 am, that's when our database is refreshed.</p>
<?php else: ?>
    <p>Here are all our properties:</p>

    <table>
        <tr>
            <th>Property ID</th>
            <th>Type</th>
            <th>Status</th>
        </tr>
        <?php foreach ($properties as $property): ?>
            <tr>
                <td><?php echo htmlspecialchars($property['PropertyID']); ?></td>
                <td><?php echo htmlspecialchars($property['Type']); ?></td>
                <td><?php echo htmlspecialchars($property['Status']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>


</body>
</html>

