<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$errors = [];
$inputs = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
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
            echo "<p style='color: red; text-align : center'> Database error: " . $conn->error . "</p>";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $vehicle_id = $_POST['vehicle_id'];
    $vehicle_name = $_POST['vehicle_name'];
    $vehicle_number = $_POST['vehicle_number'];
    $vehicle_type = $_POST['vehicle_type'];

    if (!$vehicle_name || !$vehicle_number || !$vehicle_type) {
        $errors['vehicle'] = "All fields are required.";
    }

    if (empty($errors)) {
      
        $sql = "UPDATE vehicles SET vehicle_name = ?, vehicle_number = ?, vehicle_type = ? WHERE vehicle_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $vehicle_name, $vehicle_number, $vehicle_type, $vehicle_id);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Vehicle updated successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
        }
    }
}

if (isset($_GET['delete'])) {
    $vehicle_id = $_GET['delete'];

    $sql = "DELETE FROM service_bookings WHERE vehicle_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();

    $sql = "DELETE FROM vehicles WHERE vehicle_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vehicle_id);
    if ($stmt->execute()) {
        echo "<p style='color: green; text-align : center'>Vehicle and related bookings deleted successfully!</p>";
    } else {
        echo "<p style='color: red; text-align : center'>Error: " . $stmt->error . "</p>";
    }
}

if (isset($_GET['edit'])) {
    $vehicle_id = $_GET['edit'];
    $sql = "SELECT * FROM vehicles WHERE vehicle_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Service</title>
    <style>
        h1 {
            color: blue;
            text-align: center;
            margin-top: 120px;
        }
        form {
            text-align: center;
            margin-top: 60px;
            font-size: 30px;
        }
        select, input {
            font-size: 20px;
            padding: 8px;
            margin: 10px;
        }
        button {
            font-size: 20px;
            margin-top: 40px;
            background-color: rgb(21, 126, 218);
            color: white;
            border-radius: 8px;
            padding: 10px;
        }
        button:hover {
            background-color: rgb(21, 126, 256);
        }
    </style>
</head>
<body>
    <h1><?= isset($vehicle) ? "Edit Vehicle" : "Add Vehicle" ?></h1>

    <form method="POST">
        <?php if (isset($vehicle)): ?>
            <input type="hidden" name="vehicle_id" value="<?= htmlspecialchars($vehicle['vehicle_id']) ?>">
        <?php endif; ?>

        <input type="text" name="vehicle_name" placeholder="Vehicle Name" value="<?= htmlspecialchars($vehicle['vehicle_name'] ?? '') ?>" required><br>
        <input type="text" name="vehicle_number" placeholder="Vehicle Number(digits only)" value="<?= htmlspecialchars($vehicle['vehicle_number'] ?? '') ?>" required><br>

        <select name="vehicle_type" required>
            <option value="2-wheeler" <?= (isset($vehicle) && $vehicle['vehicle_type'] == '2-wheeler') ? 'selected' : '' ?>>2-Wheeler</option>
            <option value="3-wheeler" <?= (isset($vehicle) && $vehicle['vehicle_type'] == '3-wheeler') ? 'selected' : '' ?>>3-Wheeler</option>
            <option value="4-wheeler" <?= (isset($vehicle) && $vehicle['vehicle_type'] == '4-wheeler') ? 'selected' : '' ?>>4-Wheeler</option>
        </select><br>

        <button type="submit" name="<?= isset($vehicle) ? 'update' : 'add' ?>"><?= isset($vehicle) ? 'Update Vehicle' : 'Add Vehicle' ?></button>
        <a href="dashboard.php"><button type="button">Dashboard</button></a>
    </form>

    <?php if (isset($errors['vehicle'])): ?>
        <p style="color: red; text-align: center"><?= $errors['vehicle'] ?></p>
    <?php endif; ?>

    <h2>Manage Vehicles</h2>
    <table border="1" cellpadding="5" cellspacing="0" style="width: 80%; margin: 0 auto;">
        <thead>
            <tr>
                <th>Vehicle ID</th>
                <th>Vehicle Name</th>
                <th>Vehicle Number</th>
                <th>Vehicle Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM vehicles WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['vehicle_id']) ?></td>
                    <td><?= htmlspecialchars($row['vehicle_name']) ?></td>
                    <td><?= htmlspecialchars($row['vehicle_number']) ?></td>
                    <td><?= htmlspecialchars($row['vehicle_type']) ?></td>
                    <td>
                        <a href="addvehicle.php?edit=<?= $row['vehicle_id'] ?>">Edit</a> |
                        <a href="addvehicle.php?delete=<?= $row['vehicle_id'] ?>" onclick="return confirm('Are you sure you want to delete this vehicle?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
