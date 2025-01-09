<?php
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

// Initialize variables for search and results
$status_filter = isset($_POST['status_filter']) ? $_POST['status_filter'] : 'pending';

// Fetch requests based on selected status
$sql = "SELECT id, full_name, index_number, al_stream, status FROM character_certificate_requests WHERE status = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $status_filter);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Character Certificate Requests</title>
    <style>
        /* Existing styles ... */
        body {
            font-family: Arial, sans-serif;
            background: url('des1.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
            color: #fff;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
        }
        .navbar a:hover {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }
        .navbar .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 800px;
            animation: fadeIn 1s ease-in;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            max-height: calc(100vh - 60px); /* Adjust height based on navbar height */
            margin: 80px auto 20px auto; /* Center the container and provide space for navbar */
        }
        .container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .request-list {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 100%; /* Ensure the list items span the width of the container */
        }
        .request-list li {
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
            align-items: start;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s, transform 0.5s;
            animation: slideIn 0.5s forwards;
        }
        .request-list li a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        .request-list li a:hover {
            text-decoration: underline;
        }
        .request-list .info {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .request-list .info span {
            margin-bottom: 8px;
            font-size: 16px;
        }
        .request-list .info span strong {
            color: #333;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        /* Custom Scrollbar Styles */
        .container::-webkit-scrollbar {
            width: 8px;
        }
        .container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px;
            }
            .navbar a {
                padding: 8px 10px;
                font-size: 1rem;
            }
            .navbar .logo {
                font-size: 1.3rem;
            }
            .container {
                padding: 15px;
                margin: 70px auto 10px auto; /* Adjust margin for smaller screen */
            }
            .container h2 {
                font-size: 1.5rem;
            }
            .request-list li {
                padding: 10px;
                margin-bottom: 8px;
            }
            .request-list .info span {
                font-size: 14px;
            }
            .request-list .info span strong {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .navbar a {
                padding: 6px 8px;
                font-size: 0.9rem;
            }
            .navbar .logo {
                font-size: 1.2rem;
            }
            .container {
                padding: 10px;
                margin: 60px auto 5px auto; /* Adjust margin for very small screens */
            }
            .container h2 {
                font-size: 1.3rem;
            }
            .request-list li {
                padding: 8px;
                margin-bottom: 6px;
            }
            .request-list .info span {
                font-size: 12px;
            }
            .request-list .info span strong {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">Certificate Requests</div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="javascript:history.back()">Back</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>Character Certificate Requests</h2>

        <!-- Filter Form -->
        <form method="post">
            <div>
                <label><input type="radio" name="status_filter" value="pending" <?php if ($status_filter == 'pending') echo 'checked'; ?>> Pending</label>
                <label><input type="radio" name="status_filter" value="checked" <?php if ($status_filter == 'checked') echo 'checked'; ?>> Checked</label>
                <label><input type="radio" name="status_filter" value="rejected" <?php if ($status_filter == 'rejected') echo 'checked'; ?>> Rejected</label>
                <label><input type="radio" name="status_filter" value="done" <?php if ($status_filter == 'done') echo 'checked'; ?>> Done</label>
                <label><input type="radio" name="status_filter" value="collected" <?php if ($status_filter == 'collected') echo 'checked'; ?>> Collected</label>
                <button type="submit" style="
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            margin-left: 10px;">Filter</button>
            </div>
        </form>

        <ul class="request-list">
            <?php foreach ($requests as $request): ?>
                <li>
                    <div class="info">
                        <span><strong>Name:</strong> <?php echo htmlspecialchars($request['full_name']); ?></span>
                        <span><strong>Index Number:</strong> <?php echo htmlspecialchars($request['index_number']); ?></span>
                        <span><strong>AL Stream:</strong> <?php echo htmlspecialchars($request['al_stream']); ?></span>
                        <span><strong>Status:</strong> <?php echo htmlspecialchars($request['status']); ?></span>
                        <a href="view_request.php?id=<?php echo $request['id']; ?>">View Request</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
