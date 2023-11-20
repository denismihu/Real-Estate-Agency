<?php


session_start();


$mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];


    $column = ($type == 'Agents') ? 'AgentID' : 'BuyerID'; // Select column based on type

    $stmt = $mysqli->prepare("SELECT * FROM {$type} WHERE {$column} = ?");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    $id = $_POST['id'];

    if ($type == 'Agents') {
        $name = sanitizeInput($_POST['name']);
        $email = filter_var(sanitizeInput($_POST['email']), FILTER_VALIDATE_EMAIL);
        $phone = sanitizeInput($_POST['phone']);
        $commissionRate = filter_var(sanitizeInput($_POST['commissionRate']), FILTER_VALIDATE_FLOAT);
        $isActive = $_POST['isActive'] ? 1 : 0;

        $stmt = $mysqli->prepare("UPDATE Agents SET Name = ?, Email = ?, Phone = ?, CommissionRate = ?, isActive = ? WHERE AgentID = ?");
        $stmt->bind_param("sssidi", $name, $email, $phone, $commissionRate, $isActive, $id);
    } else {
        $name = sanitizeInput($_POST['name']);
        $email = filter_var(sanitizeInput($_POST['email']), FILTER_VALIDATE_EMAIL);
        $phone = sanitizeInput($_POST['phone']);
        $preferredLocation = sanitizeInput($_POST['preferredLocation']);

        $stmt = $mysqli->prepare("UPDATE Buyers SET Name = ?, Email = ?, Phone = ?, PreferredLocation = ? WHERE BuyerID = ?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $preferredLocation, $id);
    }

    $stmt->execute();

    $stmt->close();
    $mysqli->close();

    header("Location: manageUsers.php");
    exit();
}
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="mycss.css">
</head>

<head>
    <title>Edit User</title>
</head>
<body>
<h1>Edit User</h1>
<?php if (isset($user) && !empty($user)) { ?>
    <form method="POST" action="editUser.php">
        <input type="hidden" name="id" value="<?php echo $user[$column]; ?>">
        <input type="hidden" name="type" value="<?php echo $type; ?>">
        <label>Name: </label><input type="text" name="name" value="<?php echo $user['Name']; ?>"><br>
        <label>Email: </label><input type="text" name="email" value="<?php echo $user['Email']; ?>"><br>
        <label>Phone: </label><input type="text" name="phone" value="<?php echo $user['Phone']; ?>"><br>
        <?php if ($type == 'Agents') { ?>
            <label>Commission Rate: </label><input type="text" name="commissionRate" value="<?php echo $user['CommissionRate']; ?>"><br>
            <label>Active: </label><input type="checkbox" name="isActive" <?php echo $user['isActive'] ? 'checked' : ''; ?>><br>
        <?php } else { ?>
            <label>Preferred Location: </label><input type="text" name="preferredLocation" value="<?php echo $user['PreferredLocation']; ?>"><br>
        <?php } ?>
        <input type="submit" value="Save">
    </form>
<?php } else { echo "No user found."; } ?>
</body>
</html>
