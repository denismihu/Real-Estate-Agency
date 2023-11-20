<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
}

$agent_query = "SELECT Agents.*, Users.Username, Users.PasswordHash, Users.UserRole FROM Agents INNER JOIN Users ON Agents.UserID = Users.UserID";
$buyer_query = "SELECT Buyers.*, Users.Username, Users.PasswordHash, Users.UserRole FROM Buyers INNER JOIN Users ON Buyers.UserID = Users.UserID";

$agent_result = $mysqli->query($agent_query);
$buyer_result = $mysqli->query($buyer_query);
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="mycss.css">
</head>

<head>
    <title>Manage Users</title>
</head>
<body>
<h1>Manage Users</h1>
<a href="dashboard.php">back to Dashboard</a>
<h2>Agents</h2>
<table>
    <tr>
        <th>AgentID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>CommissionRate</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while($row = $agent_result->fetch_assoc()): ?>

        <tr>
            <td><?php echo $row['AgentID']; ?></td>
            <td><?php echo $row['Name']; ?></td>
            <td><?php echo $row['Email']; ?></td>
            <td><?php echo $row['Phone']; ?></td>
            <td><?php echo $row['CommissionRate']; ?></td>
            <td><?php echo ($row['isActive']) ? 'Active' : 'Inactive'; ?></td>
            <td>
                <a href="editUser.php?type=Agents&id=<?php echo $row['AgentID']; ?>">Edit</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<h2>Buyers</h2>
<table>
    <tr>
        <th>BuyerID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>PreferredLocation</th>
        <th>Action</th>
    </tr>
    <?php while($row = $buyer_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['BuyerID']; ?></td>
            <td><?php echo $row['Name']; ?></td>
            <td><?php echo $row['Email']; ?></td>
            <td><?php echo $row['Phone']; ?></td>
            <td><?php echo $row['PreferredLocation']; ?></td>
            <td>
                <a href="editUser.php?type=Buyers&id=<?php echo $row['BuyerID']; ?>">Edit</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

<?php
$mysqli->close();
?>
