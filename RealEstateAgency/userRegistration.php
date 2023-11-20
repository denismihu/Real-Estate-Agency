<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    $stmt = $mysqli->prepare("INSERT INTO Users (Username, PasswordHash, UserRole) VALUES (?, ?, ?)");
    $PasswordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param('sss', $username, $PasswordHash, $role);

    $stmt->execute();
    $userID = $mysqli->insert_id; // this is the auto-generated UserID

    $stmt->close();
    $mysqli->close();

    // Pass the userID to the next page
    session_start();
    $_SESSION['userID'] = $userID;

    if ($role == 'agent') {
        header("Location: agentRegistration.php");
    } else if ($role == 'buyer') {
        header("Location: buyerRegistration.php");
    }
}
?>


<html>
<head>
    <link rel="stylesheet" type="text/css" href="mycss.css">
</head>

<body>
<form method="post" action="">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username">

    <label for="password">Password:</label>
    <input type="password" id="password" name="password">

    <label for="role">Role:</label>
    <select id="role" name="role">
        <option value="agent">Agent</option>
        <option value="buyer">Buyer</option>
    </select>

    <input type="submit" value="Register">
</form>
</body>
</html>
