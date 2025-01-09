<?php
session_start();

// Database connection details
$servername = "sql103.infinityfree.com";
$username = "if0_37200806";
$password = "1KeZG2Rvo5dNXvh";
$dbname = "if0_37200806_students_db";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['NIC'])) {
    header('Location: index.php');
    exit();
}

$NIC = $_SESSION['NIC'];

// Prepare and execute the query to get user information
$stmt = $conn->prepare("SELECT full_name, privileged FROM users WHERE NIC = ?");
$stmt->bind_param("s", $NIC);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($full_name, $privileged);
    $stmt->fetch();
} else {
    // If no result, redirect or show an error
    echo "No user data found.";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('des1.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        .dashboard-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            width: 80%;
            max-width: 600px;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
            font-size: 2rem;
        }
        .button {
            display: inline-block;
            margin: 10px;
            padding: 15px 30px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            font-size: 1.2rem;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($full_name); ?></h1>
        <p>Privilege Level: <?php echo htmlspecialchars($privileged); ?></p>

        <?php if ($privileged === 'admin'): ?>
            <a href="addClark.php" class="button">Add a new clerk</a>
        <?php endif; ?>

        <?php if ($privileged === 'clark'): ?>
            <a href="request_leaving_certificate.php" class="button">Request Leaving Certificate</a>
            <a href="request_character_certificate.php" class="button">Request Character Certificate</a>
        <?php endif; ?>

        <a href="logout.php" class="button">Logout</a>
    </div>
</body>

</html>
