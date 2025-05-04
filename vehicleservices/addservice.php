<?php
session_start();
include("config.php"); 

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['service_id']) && !empty($_POST['vehicle_id']) && !empty($_POST['booking_date'])) {
        $service_id = $_POST['service_id'];
        $vehicle_id = $_POST['vehicle_id'];
        $booking_date = $_POST['booking_date'];

        $sql = "INSERT INTO service_bookings (user_id, vehicle_id, service_id, booking_date) 
                VALUES ('$user_id', '$vehicle_id', '$service_id', '$booking_date')";

        if ($conn->query($sql)) {
            echo "<p style='color: green; text-align: center;'>Service booked successfully!</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>Error: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: red; text-align: center;'>Please fill all fields.</p>";
    }
}
?>

<html>
<head>
    <style>
        h1 {
            color: blue;
            text-align: center;
            margin-top: 80px;
        }
        form {
            text-align: center;
            margin-top: 40px;
            font-size: 22px;
        }
        select, input[type="date"] {
            font-size: 20px;
            padding: 8px;
            margin: 10px;
        }
        button {
            background-color :rgb(21, 126, 218);
            color : white;
            font-size: 20px;
            margin-top: 30px;
            padding: 8px 20px;
            border-radius : 8px;
        }
        button:hover {
            background-color: rgb(21, 126, 256);
        }
    </style>
</head>
<body>
    <h1>BOOK SERVICE</h1>
    <form method="POST" action="addservice.php">
        <label for="service_id">Select Service:</label><br>
        <select name="service_id" required>
            <option value="">Select Service...</option>
            <option value="1">Vehicle Wash</option>
            <option value="2">Oil Change</option>
            <option value="3">Repair Vehicle</option>
            <option value="4">Tire Rotation</option>
        </select><br><br>

        <label for="vehicle_id">Select Vehicle:</label><br>
<select name="vehicle_id" required>
    <option value="">Select Vehicle...</option>
    <?php
    $vehicle_query = "SELECT DISTINCT vehicle_id, vehicle_name FROM vehicles WHERE user_id = '$user_id'";
    $vehicle_result = $conn->query($vehicle_query);
    $added_ids = [];

    if ($vehicle_result->num_rows > 0) {
        while ($row = $vehicle_result->fetch_assoc()) {
            if (!in_array($row['vehicle_id'], $added_ids)) {
                echo "<option value='" . $row['vehicle_id'] . "'>" . htmlspecialchars($row['vehicle_name']) . "</option>";
                $added_ids[] = $row['vehicle_id'];
            }
        }
    } else {
        echo "<option value=''>No vehicles added yet</option>";
    }
    ?>
</select><br><br>


        <label for="booking_date">Service Date:</label><br>
        <input type="date" name="booking_date" min="<?= date('Y-m-d'); ?>" required><br><br>

        <button type="submit">Add Service</button>
        <a href="dashboard.php"><button type="button">Dashboard</button></a>
    </form>
</body>
</html>
