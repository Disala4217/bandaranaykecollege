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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = [];

    $NIC = $_POST['NIC'];
    $password = $_POST['password'];

    // Prepare and execute the query to check the NIC and password
    $stmt = $conn->prepare("SELECT NIC, full_name, privileged FROM users WHERE NIC = ? AND password = ?");
    $stmt->bind_param("ss", $NIC, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Successful login
        $stmt->bind_result($dbNIC, $full_name, $privileged);
        $stmt->fetch();

        // Store user information in session
        $_SESSION['NIC'] = $dbNIC;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['privileged'] = $privileged;

        $response['message'] = "Login successful!";
        $response['message_type'] = "success";

        // Redirect based on privilege level
        if ($privileged === 'admin') {
            $response['redirect'] = "admin_dashboard.php";
        } else {
            $response['redirect'] = "clerk_dashboard.php";
        }
    } else {
        // Unsuccessful login
        $response['message'] = "Invalid NIC or password";
        $response['message_type'] = "failure";
    }

    $stmt->close();
    $conn->close();

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        .login-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .login-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            box-sizing: border-box;
        }
        .login-container input[type="submit"]:hover {
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
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <div id="message" class="message" style="display: none;"></div>

        <form id="loginForm" method="post">
            <input type="text" name="NIC" placeholder="NIC" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Get form data
            const formData = new FormData(this);

            // Create an XMLHttpRequest object
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // Post to the same page

            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Parse the JSON response
                    const response = JSON.parse(xhr.responseText);

                    // Display the message
                    const messageDiv = document.getElementById('message');
                    messageDiv.innerHTML = response.message;
                    messageDiv.className = 'message ' + response.message_type;
                    messageDiv.style.display = 'block';

                    // Redirect if login is successful
                    if (response.message_type === 'success' && response.redirect) {
                        setTimeout(() => {
                            window.location.href = response.redirect;
                        }, 2000); // Redirect after 2 seconds
                    }
                } else {
                    // Display error message
                    const messageDiv = document.getElementById('message');
                    messageDiv.innerHTML = 'An error occurred. Please try again.';
                    messageDiv.className = 'message failure';
                    messageDiv.style.display = 'block';
                }
            };

            xhr.send(formData);
        });
    </script>
</body>
</html>