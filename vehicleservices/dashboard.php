<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
echo "<p style='color: green; font-size: 24px; text-align : center'>WELCOME, " . $_SESSION['username'] . "</p>";
?>
<br><br>

<html>
<head>
    <style>
        h1 {
            color: blue;
            text-align: center;
            margin-top: 80px;
        }

        .button-container {
            text-align: center;
            margin-top: 60px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            font-size: 18px;
            background-color: rgb(21, 126, 218);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: rgb(21, 126, 256);
        }
    </style>
</head>
<body>
    <h1>DASHBOARD</h1>

    <div class="button-container">
        <a href="addvehicle.php" class="btn">Add Vehicle</a>
        <a href="addservice.php" class="btn">Book Service</a>
        <a href="logout.php" class="btn">Logout</a>
    </div>
</body>
</html>
