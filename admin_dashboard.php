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
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .navbar {
            width: 100%;
            background: rgba(255, 255, 255, 0.5);
            padding: 10px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            z-index: 1000;
        }
        .navbar a {
            color: #333;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 1.2rem;
            transition: background-color 0.3s ease;
        }
        .navbar a:hover {
            background-color: #4CAF50;
            color: white;
        }
        .navbar .logo {
            font-size: 1.5rem;
            font-weight: bold;
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
            margin-top: 70px; /* To prevent overlap with the navbar */
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

        /* Responsive Styles */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px;
            }
            .navbar a {
                font-size: 1rem;
                padding: 10px;
            }
            .dashboard-container {
                padding: 20px;
                width: 90%;
            }
            h1 {
                font-size: 1.5rem;
            }
            .button {
                padding: 10px 20px;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .navbar .logo {
                font-size: 1.2rem;
            }
            .dashboard-container {
                padding: 15px;
            }
            h1 {
                font-size: 1.2rem;
            }
            .button {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">My Dashboard</div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <?php if ($privileged === 'admin'): ?>
                <a href="addClark.php">Add a new clerk</a>
            <?php endif; ?>
            <a href="javascript:history.back()">Back</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

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
