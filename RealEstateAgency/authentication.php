<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    $stmt = $mysqli->prepare("SELECT Users.*, Agents.isActive FROM Users LEFT JOIN Agents ON Users.UserID = Agents.UserID WHERE Users.Username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['PasswordHash'])){
            if($user['UserRole'] === 'Agent' && $user['isActive'] != 1){
                echo "<p>Your account needs administrator validation, please contact him on email mihu.ni.denis@student.utcluj.ro</p>";
                header("refresh:5;url=welcome.php");
                exit();
            }

            $_SESSION['userID'] = $user['UserID'];
            $_SESSION['UserRole'] = $user['UserRole'];

            if($user['UserRole'] === 'Buyer'){
                header("Location: buyerWelcome.php");
            } else if($user['UserRole'] === 'Agent'){
                header("Location: agentWelcome.php");
            } else if($user['UserRole'] === 'Administrator'){
                header("Location: dashboard.php");
            }

            exit();
        }
    }

    $stmt->close();
    $mysqli->close();

    echo "<p>User not found. Try again.</p>";
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

    <input type="submit" value="Login">
</form>

<a href="userRegistration.php">Sign Up</a>
<button onclick="alert('Please send an email to technical support at mihu.ni.denis@student.utcluj.ro')">Forgot Password?</button>
</body>
</html>
