<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $preferredLocation = $_POST['preferredLocation'];

    session_start();
    $userID = $_SESSION['userID']; // retrieve the userID saved in the session

    $mysqli = new mysqli("127.0.0.1", "root", "", "RealEstateAgency");

    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    $stmt = $mysqli->prepare("INSERT INTO Buyers (BuyerID, UserID, Name, Email, Phone, PreferredLocation) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iissss', $userID, $userID, $name, $email, $phone, $preferredLocation);

    $stmt->execute();
    $stmt->close();
    $mysqli->close();

    header("Location: authentication.php");
    exit();
}
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="mycss.css">
</head>

<body>
<form method="post" action="">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name">

    <label for="email">Email:</label>
    <input type="email" id="email" name="email">

    <label for="phone">Phone:</label>
    <input type="text" id="phone" name="phone">

    <label for="preferredLocation">Preferred Location:</label>
    <input type="text" id="preferredLocation" name="preferredLocation">

    <input type="submit" value="Complete Registration">
</form>
</body>
</html>
