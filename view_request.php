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

// Initialize variables for messages
$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if update status button was clicked
    if (isset($_POST['update_status'])) {
        $request_id = $_POST['request_id'];
        $status = $_POST['update_status'];
        $issueInfo = $_POST['issueInfo'] ?? '';

        // Update status and issueInfo in the database
        $stmt = $conn->prepare("UPDATE character_certificate_requests SET status = ?, issueInfo = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $issueInfo, $request_id);

        if ($stmt->execute()) {
            $message = "Status updated successfully!";
            $message_type = "success";
        } else {
            $message = "Error updating status: " . $conn->error;
            $message_type = "failure";
        }
        $stmt->close();
    }
    // Check if mark as collected button was clicked
    elseif (isset($_POST['mark_collected'])) {
        $request_id = $_POST['request_id'];

        // Update status to collected in the database
        $stmt = $conn->prepare("UPDATE character_certificate_requests SET status = 'collected' WHERE id = ?");
        $stmt->bind_param("i", $request_id);

        if ($stmt->execute()) {
            $message = "Certificate marked as collected!";
            $message_type = "success";
        } else {
            $message = "Error marking certificate as collected: " . $conn->error;
            $message_type = "failure";
        }
        $stmt->close();
    }
    elseif (isset($_POST['update_issueInfo'])) {
        $request_id = $_POST['request_id'];

        // Update status to collected in the database
        $stmt = $conn->prepare("UPDATE character_certificate_requests SET issueInfo = '$_POST[issueInfo]' WHERE id = ?");
        $stmt->bind_param("i", $request_id);

        if ($stmt->execute()) {
            $message = "Information added!";
            $message_type = "success";
        } else {
            $message = "Error marking certificate as collected: " . $conn->error;
            $message_type = "failure";
        }
        $stmt->close();
    }
}

// Fetch the request details from the database
$request_id = $_GET['id'];
$sql = "SELECT * FROM character_certificate_requests WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

// Fetch extracurricular activities related to the request
$sql = "SELECT * FROM extracurricular_activities WHERE request_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$activities = $stmt->get_result();

// Fetch approval documents related to the request
$sql = "SELECT * FROM approval_documents WHERE request_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$approvals = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Request Details</title>
    <style>
        /* Basic styles for the page */
        body {
            font-family: Arial, sans-serif;
            background: url('des1.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
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
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            top: 80px; /* Adjusted to avoid navbar overlap */
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
        .request-details p, .activities p, .approvals p {
            margin: 5px 0;
        }
        .status-update textarea {
            width: 100%;
            max-width: 500px; /* Reduced width */
            height: 60px; /* Adjusted height */
            margin-top: 10px;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            resize: vertical;
        }
        .status-update button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            margin-top: 10px;
            margin-right: 10px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .status-update button:hover {
            transform: scale(1.05);
        }
        .status-update .checked {
            background-color: #17a2b8;
        }
        .status-update .rejected {
            background-color: #dc3545;
        }
        .status-update .done {
            background-color: #28a745;
        }
        .status-update .collected {
            background-color: #6c757d;
        }
        .update-info-button {
            background-color: #007bff;
        }
        .activities a, .approvals a {
            display: inline-block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .activities a:hover, .approvals a:hover {
            text-decoration: underline;
        }

        /* Styles for the popup */
        .popup-overlay {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        .popup-content {
            background: #fff;
            border-radius: 8px;
            max-width: 90%;
            max-height: 90%;
            overflow: auto;
            position: relative;
            padding: 20px;
        }
        .popup-content img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .popup-close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 1.5rem;
            color: #333;
        }
        .popup-close:hover {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="character_requests.php"><b>Character Certificate Requests</b></a>
        <a href="logout.php">Logout</a>
        <a href="javascript:history.back()">Back</a>
    </div>

    <div class="container">
        <h2>Request Details</h2>
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Displaying request details -->
        <div class="request-details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($request['full_name']); ?></p>
            <p><strong>Index Number:</strong> <?php echo htmlspecialchars($request['index_number']); ?></p>
            <p><strong>Studying From:</strong> <?php echo htmlspecialchars($request['studying_from']); ?></p>
            <p><strong>Studying To:</strong> <?php echo htmlspecialchars($request['studying_to']); ?></p>
            <p><strong>Year of O/L:</strong> <?php echo htmlspecialchars($request['year_of_ol']); ?></p>
            <p><strong>Year of A/L:</strong> <?php echo htmlspecialchars($request['year_of_al']); ?></p>
            <p><strong>Stream:</strong> <?php echo htmlspecialchars($request['al_stream']); ?></p>
            <p><strong>Medium:</strong> <?php echo htmlspecialchars($request['medium']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($request['status']); ?></p>
        </div>

        <!-- Displaying extracurricular activities -->
        <h3>Extracurricular Activities</h3>
        <?php while ($activity = $activities->fetch_assoc()): ?>
            <p>
                <a href="#" onclick="showPopup('serve_image.php?type=activity&file_id=<?php echo $activity['id']; ?>')">View Evidence Image</a>
            </p>
        <?php endwhile; ?>

        <!-- Displaying approval documents -->
        <h3>Approval Documents</h3>
        <?php while ($approval = $approvals->fetch_assoc()): ?>
            <p>
                <a href="#" onclick="showPopup('serve_image.php?type=approval&file_id=<?php echo $approval['id']; ?>')">View Approval Document</a>
            </p>
        <?php endwhile; ?>
        
        <!-- Popup for displaying the image -->
        <div id="popup-overlay" class="popup-overlay">
            <div class="popup-content">
                <span class="popup-close" onclick="closePopup()">&times;</span>
                <img id="popup-image" src="" alt="Document Image">
            </div>
        </div>

        <!-- Form to update status and issue information -->
        <?php if ($request['status'] === 'done'): ?>
            <form method="POST" class="status-update">
                <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
                <textarea name="issueInfo" placeholder="Enter issue information (if any)"><?php echo htmlspecialchars($request['issueInfo']); ?></textarea>
                <button type="submit" name="update_issueInfo" value="done" class="done">Update Status</button>
                <button type="submit" name="mark_collected" class="collected">Mark as Collected</button>
            </form>
        <?php elseif ($request['status'] === 'rejected'): ?>
            <form method="POST" class="status-update">
                <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
                <textarea name="issueInfo" placeholder="Enter issue information (if any)"><?php echo htmlspecialchars($request['issueInfo']); ?></textarea>
                <button type="submit" name="update_issueInfo" class="done">Reason for Rejection</button>
            </form>
        <?php elseif ($request['status'] === 'checked'): ?>
            <form method="POST" class="status-update">
                <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
                <button type="submit" name="update_status" value="rejected" class="rejected">Reject</button>
                <button type="submit" name="update_status" value="done" class="done">Mark as Done</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        function showPopup(src) {
            var imgElement = document.getElementById('popup-image');
            imgElement.src = src;
            document.getElementById('popup-overlay').style.display = 'flex';
        }

        function closePopup() {
            document.getElementById('popup-overlay').style.display = 'none';
        }
    </script>
</body>
</html>