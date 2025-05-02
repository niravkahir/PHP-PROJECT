<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$errors = [];
$inputs = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_SESSION['user_id'];

    $name = filter_input(INPUT_POST, 'vehicle_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $inputs['vehicle_name'] = $name;
    if (!$name) {
        $errors['vehicle_name'] = "Vehicle name is required.";
    }

    $number = $_POST['vehicle_number'] ?? '';
    $inputs['vehicle_number'] = $number;
    if ($number) {
        if (!preg_match("/^\d{4}$/", $number)) {
            $errors['vehicle_number'] = "Vehicle number must be exactly 4 digits.";
        }
    } else {
        $errors['vehicle_number'] = "Vehicle number is required.";
    }
    

    $type = filter_input(INPUT_POST, 'vehicle_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $inputs['vehicle_type'] = $type;
    $allowed_types = ['2-wheeler', '3-wheeler', '4-wheeler'];
    if ($type) {
        if (!in_array($type, $allowed_types)) {
            $errors['vehicle_type'] = "Invalid vehicle type selected.";
        }
    } else {
        $errors['vehicle_type'] = "Vehicle type is required.";
    }
    if (empty($errors)) {
        $sql = "INSERT INTO vehicles (user_id, vehicle_name, vehicle_number, vehicle_type)
                VALUES ('$uid', '$name', '$number', '$type')";
        if ($conn->query($sql)) {
            echo "<p style='color: green; text-align : center'> Vehicle added successfully!</p>";
        } else {
            echo "<p style='color: red; text-align : center'>  Database error: " . $conn->error . "</p>";
        }
    }
}
?>

<html>
    <head>
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
            select, input{
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
    <h1>ADD VEHICLES</h1>

<form method="POST">
    <input type="text" name="vehicle_name" placeholder="Vehicle Name" value="<?= htmlspecialchars($inputs['vehicle_name'] ?? '') ?>" required><br>
    <?php if (isset($errors['vehicle_name'])) echo "<small style='color:red'>{$errors['vehicle_name']}</small><br>"; ?>

    <input type="text" name="vehicle_number" placeholder="Vehicle Number(digits only)" value="<?= htmlspecialchars($inputs['vehicle_number'] ?? '') ?>" required><br>
    <?php if (isset($errors['vehicle_number'])) echo "<small style='color:red'>{$errors['vehicle_number']}</small><br>"; ?>

    <select name="vehicle_type" required>
        <option value="" disabled <?= !isset($inputs['vehicle_type']) ? 'selected' : '' ?>>Select Vehicle Type</option>
        <option value="2-wheeler" <?= ($inputs['vehicle_type'] ?? '') === '2-wheeler' ? 'selected' : '' ?>>2 Wheeler</option>
        <option value="3-wheeler" <?= ($inputs['vehicle_type'] ?? '') === '3-wheeler' ? 'selected' : '' ?>>3 Wheeler</option>
        <option value="4-wheeler" <?= ($inputs['vehicle_type'] ?? '') === '4-wheeler' ? 'selected' : '' ?>>4 Wheeler</option>
    </select><br>
    <?php if (isset($errors['vehicle_type'])) echo "<small style='color:red'>{$errors['vehicle_type']}</small><br>"; ?>

    <br><button type="submit">Add Vehicle</button>
    <a href="dashboard.php"><button type="button">Dashboard</button></a>
</form>  
    </body>
</html> 