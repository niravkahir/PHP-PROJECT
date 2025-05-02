<?php
session_start();
include("config.php");

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['password'] == $password) {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<p style='color: red; text-align: center;'>Wrong password.</p>";
        echo "<p style='text-align: center;'><a href='index.php'>Try again</a></p>";
    }
} else {
    echo "<p style='color: red; text-align: center;'>User does not exist. Please register.</p>";
    echo "<p style='text-align: center;'><a href='register.php'>Go to Register</a></p>";
}
?>
