<?php
session_start();

// Check if the user is logged in and has the 'admin' privilege
if (!isset($_SESSION['NIC']) || $_SESSION['privileged'] !== 'admin') {
    header('Location: index.php');
    exit();
}

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

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $NIC = $_POST['NIC'];
    $password = $_POST['password'];
    $full_name = $_POST['full_name'];

    // Check if NIC already exists
    $stmt = $conn->prepare("SELECT NIC FROM users WHERE NIC = ?");
    $stmt->bind_param("s", $NIC);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "NIC already exists!";
        $message_type = "failure";
    } else {
        // Insert new clerk into the database
        $stmt = $conn->prepare("INSERT INTO users (NIC, password, full_name, privileged) VALUES (?, ?, ?, 'clark')");
        $stmt->bind_param("sss", $NIC, $password, $full_name);

        if ($stmt->execute()) {
            $message = "New clerk added successfully!";
            $message_type = "success";
        } else {
            $message = "Error adding clerk: " . $conn->error;
            $message_type = "failure";
        }
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a New Clerk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('des1.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
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
            font-size: 1rem;
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
        .form-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            width: 90%;
            max-width: 400px; /* Increase max width for larger screens */
            margin-top: 70px; /* To prevent overlap with the navbar */
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .form-container input[type="text"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            box-sizing: border-box;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .failure {
            background-color: #f8d7da;
            color: #721c24;
        }
        @media (max-width: 600px) {
            .navbar a {
                font-size: 0.9rem; /* Decrease font size for smaller screens */
            }
            .navbar .logo {
                font-size: 1.2rem; /* Decrease logo size for smaller screens */
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">My Dashboard</div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="javascript:history.back()">Back</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="form-container">
        <h2>Add a New Clerk</h2>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="NIC" placeholder="NIC" required>
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Add Clerk">
        </form>
    </div>
</body>
</html>
