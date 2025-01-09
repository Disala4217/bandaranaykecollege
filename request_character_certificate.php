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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect POST data
    $full_name = $_POST['full_name'];
    $index_number = $_POST['index_number'];
    $studying_from = $_POST['studying_from'];
    $studying_to = $_POST['studying_to'];
    $year_of_ol = $_POST['year_of_ol'];
    $year_of_al = $_POST['year_of_al'];
    $al_stream = $_POST['al_stream'];
    $medium = $_POST['medium'];

    // Prepare and execute the query to insert into character_certificate_requests
    $stmt = $conn->prepare("INSERT INTO character_certificate_requests (full_name, index_number, studying_from, studying_to, year_of_ol, year_of_al, al_stream, medium) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiiss", $full_name, $index_number, $studying_from, $studying_to, $year_of_ol, $year_of_al, $al_stream, $medium);

    if ($stmt->execute()) {
        $request_id = $stmt->insert_id;

        // Handle extracurricular activities
        $activities = $_POST['activity'];
        $time_periods = $_POST['time_period'];
        $evidence_files = $_FILES['evidence'];

        for ($i = 0; $i < count($activities); $i++) {
            if ($activities[$i] && $time_periods[$i] && $evidence_files['error'][$i] == UPLOAD_ERR_OK) {
                $fileTmpPath = $evidence_files['tmp_name'][$i];
                $fileData = file_get_contents($fileTmpPath);

                $stmt = $conn->prepare("INSERT INTO extracurricular_activities (request_id, activity, time_period, evidence_file) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $request_id, $activities[$i], $time_periods[$i], $fileData);
                $stmt->execute();
            }
        }

        // Handle approval documents
        $approval_files = $_FILES['approval_documents'];

        if ($approval_files['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $approval_files['tmp_name'];
            $fileData = file_get_contents($fileTmpPath);

            $stmt = $conn->prepare("INSERT INTO approval_documents (request_id, approval_file) VALUES (?, ?)");
            $stmt->bind_param("is", $request_id, $fileData);

            if ($stmt->execute()) {
                $message = "Request submitted successfully!";
                $message_type = "success";
            } else {
                $message = "Error adding approval document: " . $conn->error;
                $message_type = "failure";
            }
        } else {
            $message = "Error uploading approval document.";
            $message_type = "failure";
        }

        $stmt->close();
    } else {
        $message = "Error submitting request: " ;
        $message_type = "failure";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Character Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('des1.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            width: 80%;
            max-width: 800px;
            overflow-y: auto; /* Enable scrolling for overflow */
            max-height: 80vh; /* Limit the height of the container */
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-container input[type="file"] {
            margin: 10px 0;
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
        .activities-table {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
        }
        .activities-table th, .activities-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .activities-table th {
            background-color: #f2f2f2;
        }
        .remove-activity {
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .remove-activity:hover {
            background-color: #d32f2f;
        }
        .add-activity {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .add-activity:hover {
            background-color: #45a049;
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
    </style>
    <script>
        function addActivityRow() {
            const table = document.getElementById('activities-table');
            const rowCount = table.rows.length;
            const row = table.insertRow(rowCount);

            row.innerHTML = 
                `<td><input type="text" name="activity[]" placeholder="Activity" required></td>
                <td><input type="text" name="time_period[]" placeholder="Time Period" required></td>
                <td><input type="file" name="evidence[]" accept=".JPG" required></td>
                <td><button type="button" class="remove-activity" onclick="removeActivityRow(this)">Remove</button></td>`;
        }

        function removeActivityRow(button) {
            const row = button.closest('tr');
            row.remove();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const messageElement = document.querySelector('.message');
            if (messageElement) {
                setTimeout(function() {
                    messageElement.style.display = 'none';
                }, 5000);
            }
        });
    </script>
</head>
<body>
    <div class="navbar">
        <div class="logo">Character Certificate Request</div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="form-container">
        <h2>Request Character Certificate</h2>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="text" name="index_number" placeholder="Index Number" required>
            <input type="number" name="studying_from" placeholder="Studying From (Year)" required>
            <input type="number" name="studying_to" placeholder="Studying To (Year)" required>
            <input type="number" name="year_of_ol" placeholder="Year of O/L" required>
            <input type="number" name="year_of_al" placeholder="Year of A/L" required>
            <input type="text" name="al_stream" placeholder="A/L Stream" required>
            <select name="medium" required>
                <option value="" disabled selected>Select Medium</option>
                <option value="English">English</option>
                <option value="Sinhala">Sinhala</option>
            </select>
<fieldset>
    <legend>Extracurricular Activities</legend>
            <table id="activities-table" class="activities-table">
                <thead>
                    <tr>
                        <th>Extracurricular Activity</th>
                        <th>Time Period</th>
                        <th>Evidence (JPG)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="activity[]" placeholder="Activity" required></td>
                        <td><input type="text" name="time_period[]" placeholder="Time Period" required></td>
                        <td><input type="file" name="evidence[]" accept=".JPG" required></td>
                        <td><button type="button" class="remove-activity" onclick="removeActivityRow(this)">Remove</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="add-activity" onclick="addActivityRow()">Add Activity</button>
            <br></fieldset><br>
            <fieldset>
    <legend>Upload Approval Document(JPG)</legend>
    <input type="file" name="approval_documents" accept=".jpg" required placeholder="Upload Approval Document" >
</fieldset><br>
            <input type="submit" value="Submit Request">
        </form>
    </div>
</body>
</html>
