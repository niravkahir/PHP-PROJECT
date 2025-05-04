<?php
include("config.php");
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if username already exists
    $check_sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        $error = "Username already taken.";
    } else {
        $insert = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($conn->query($insert)) {
            $success = "Registration successful. You can now log in.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        h1 { text-align: center; color: blue; ; margin-top: 80px; }
        form { text-align: center; font-size: 20px; }
        input, button { margin: 10px; padding: 8px; font-size: 18px; }
        p { text-align: center; color: red; }
        button{
            font-size : 20px;
            margin-top : 40px;
            background-color: rgb(21, 126, 218);
            color: white;
            border-radius: 8px;
            padding : 10px;
        }
        button:hover{
            background-color: rgb(21, 126, 256);
        }
    </style>
</head>
<body>
    <h1>Register</h1>
    <?php if ($error) echo "<p>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

    <form method="POST" action="register.php">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Register</button>
    </form>
    <p><a href="index.php" style="text-decoration: none; color: blue;">Back to Login</a></p>
</body>
</html>
