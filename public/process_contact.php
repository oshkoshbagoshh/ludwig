<?php
header('Content-Type: application/json');

// Database configuration
$host = "127.0.0.1";
$port = "8889";
$user = "root";
$password = "";
$dbname = "tfn_music"; // Make sure this database exists

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Validate required fields
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$service_type = $_POST['service_type'] ?? '';
$message = trim($_POST['message'] ?? '');
$budget = $_POST['budget'] ?? '';

if (empty($name) || empty($email) || empty($service_type)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

try {
    // Connect to database
    $con = new mysqli($host, $user, $password, $dbname, $port);

    if ($con->connect_error) {
        throw new Exception('Database connection failed: ' . $con->connect_error);
    }

    // Create table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS contact_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        service_type VARCHAR(100) NOT NULL,
        message TEXT,
        budget VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if (!$con->query($createTable)) {
        throw new Exception('Error creating table: ' . $con->error);
    }

    // Insert contact request
    $stmt = $con->prepare("INSERT INTO contact_requests (name, email, service_type, message, budget) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $service_type, $message, $budget);

    if ($stmt->execute()) {
        // Optional: Send email notification here
        mail('hello@tfnms.co', 'New Contact Request', $message, 'From: ' . $email);

        echo json_encode([
            'success' => true,
            'message' => 'Thank you, ' . htmlspecialchars($name) . '! Your request has been submitted. We\'ll get back to you soon.'
        ]);
    } else {
        throw new Exception('Error saving request: ' . $stmt->error);
    }

    $stmt->close();
    $con->close();

} catch (Exception $e) {
    error_log($e->getMessage()); // Log error for debugging
    echo json_encode(['success' => false, 'message' => 'Sorry, there was a problem submitting your request. Please try again.']);
}
?>
