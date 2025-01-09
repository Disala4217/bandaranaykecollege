<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
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
        .main-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            width: 80%;
            max-width: 800px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
            font-size: 2rem;
            animation: fadeIn 2s ease-out;
            text-align: center;
        }
        .content {
            display: flex;
            align-items: center;
            width: 100%;
            flex-wrap: wrap; /* Allow sections to wrap on smaller screens */
        }
        .section {
            width: 100%; /* Default to full width */
            padding: 20px;
            box-sizing: border-box;
            text-align: center; /* Center text for smaller screens */
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
            width: calc(100% - 20px); /* Ensure buttons fit within the container */
            box-sizing: border-box;
        }
        .button:hover {
            background-color: #45a049;
        }
        .separator {
            display: none; /* Hide separator on smaller screens */
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Styles */
        @media (min-width: 600px) {
            .section {
                width: 45%; /* Two sections side by side on larger screens */
            }
            .separator {
                display: block; /* Show separator on larger screens */
                width: 1px;
                background-color: #ccc;
                height: 200px; /* Adjust based on your content height */
                margin: 0 20px;
            }
        }

        @media (max-width: 599px) {
            h1 {
                font-size: 1.5rem; /* Adjust font size for smaller screens */
            }
            .button {
                font-size: 1rem; /* Adjust button font size for smaller screens */
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <h1>Welcome</h1>
        <div class="content">
            <div class="section">
                <a href="login.php" class="button">Login</a>
            </div>
            <div class="separator"></div>
            <div class="section">
                <a href="request_leaving_certificate.php" class="button">Request Leaving Certificate</a>
                <a href="sh_leaving_certificate.php" class="button">Leaving Certificate Status</a>
                <a href="request_character_certificate.php" class="button">Request Character Certificate</a>
                <a href="sh_character_certificate.php" class="button">Character Certificate Status</a>
            </div>
        </div>
    </div>
</body>
</html>
