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
$search_query = '';
$search_results = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    
    // Prepare the search SQL statement
    $sql = "SELECT * FROM character_certificate_requests WHERE full_name LIKE ? OR index_number LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = '%' . $search_query . '%';
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    
    // Get the result
    $search_results = $stmt->get_result();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Student Requests</title>
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
        .container {
            padding: 20px;
            margin: 20px auto;
            max-width: 800px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            box-sizing: border-box;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .search-form {
            display: flex;
            flex-direction: column; /* Stack elements vertically */
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 10px; /* Add space below input */
        }
        .search-form button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .search-form button:hover {
            background-color: #0056b3;
        }
        .results-container {
            margin-top: 20px;
        }
        .result-card {
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            background-color: #f8f9fa;
        }
        .result-card h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }
        .result-card p {
            margin: 5px 0;
        }
        .no-results {
            color: #ff0000;
            text-align: center;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 600px) {
            .search-form {
                flex-direction: column;
            }

            .result-card h3 {
                font-size: 16px; /* Reduce heading size on smaller screens */
            }

            .result-card p {
                font-size: 14px; /* Reduce paragraph size on smaller screens */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Student Requests</h2>
        
        <form class="search-form" method="post">
            <input type="text" name="search_query" placeholder="Enter student name or index number" value="<?php echo htmlspecialchars($search_query); ?>" required>
            <button type="submit" name="search">Search</button>
        </form>
        
        <div class="results-container">
            <?php if ($search_results && $search_results->num_rows > 0): ?>
                <?php while ($row = $search_results->fetch_assoc()): ?>
                    <div class="result-card">
                        <h3><?php echo htmlspecialchars($row['full_name']); ?> (<?php echo htmlspecialchars($row['index_number']); ?>)</h3>
                        <p><strong>Studying From:</strong> <?php echo htmlspecialchars($row['studying_from']); ?></p>
                        <p><strong>Studying To:</strong> <?php echo htmlspecialchars($row['studying_to']); ?></p>
                        <p><strong>Year of O/L:</strong> <?php echo htmlspecialchars($row['year_of_ol']); ?></p>
                        <p><strong>Year of A/L:</strong> <?php echo htmlspecialchars($row['year_of_al']); ?></p>
                        <p><strong>A/L Stream:</strong> <?php echo htmlspecialchars($row['al_stream']); ?></p>
                        <p><strong>Medium:</strong> <?php echo htmlspecialchars($row['medium']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                        <p><strong>Issue Info:</strong> <?php echo htmlspecialchars($row['issueInfo']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <p class="no-results">No results found for "<?php echo htmlspecialchars($search_query); ?>".</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
