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

$type = $_GET['type'];
$file_id = $_GET['file_id'];

if ($type === 'activity') {
    $sql = "SELECT evidence_file FROM extracurricular_activities WHERE id = ?";
} elseif ($type === 'approval') {
    $sql = "SELECT approval_file FROM approval_documents WHERE id = ?";
} else {
    die("Invalid type specified.");
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $file_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $fileData = $row['evidence_file'] ?? $row['approval_file'];
    
    header("Content-Type: image/jpeg"); // Adjust as needed for different image types
    echo $fileData;
} else {
    echo "No image found.";
}

$conn->close();
?>
