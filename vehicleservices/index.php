<?php
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("config.php");

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        h1{
            color : blue;
            text-align : center;
            margin-top : 120px;
        }
        form{
            text-align : center;
            margin-top : 60px;
            font-size : 30px;
        }
        input{
            font-size: 20px;
            padding: 8px;
            margin: 10px;
        }
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
    <h1>LOGIN PAGE</h1>
    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
    <input type="text" name="username" placeholder="Username" required /><br>
    <input type="password" name="password" placeholder="Password" required /><br><br>
    <button type="submit">Login</button>
    <p style="text-align: center;"><a href="register.php" style="text-decoration: none; color: blue;">Don't have an account? Register here</a></p>

</form>

</body>
</html>
