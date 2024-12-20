<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

// Check if user is admin
if (!isset($_SESSION['UserID']) || !isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
    $response['error'] = "Unauthorized access";
    echo json_encode($response);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['firstname', 'lastname', 'email', 'password', 'role'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        $response['error'] = "Missing required field: " . $field;
        echo json_encode($response);
        exit();
    }
}

$firstname = trim($data['firstname']);
$lastname = trim($data['lastname']);
$email = trim($data['email']);
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$role = $data['role'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['error'] = "Invalid email format";
    echo json_encode($response);
    exit();
}

// Validate role
if ($role !== 'User' && $role !== 'Admin') {
    $response['error'] = "Invalid role";
    echo json_encode($response);
    exit();
}

try {
    // Check if email already exists
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        $response['error'] = "Email already exists";
        echo json_encode($response);
        exit();
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Email, Password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $firstname, $lastname, $email, $password);
    
    if ($stmt->execute()) {
        // Update the user's role in a separate query
        $userId = $conn->insert_id;
        $updateStmt = $conn->prepare("UPDATE Users SET Role = ? WHERE UserID = ?");
        $updateStmt->bind_param("si", $role, $userId);
        $updateStmt->execute();
        
        $response['success'] = true;
        $response['message'] = "User created successfully";
    } else {
        $response['error'] = "Failed to create user";
    }
} catch (Exception $e) {
    $response['error'] = "Database error: " . $e->getMessage();
}

echo json_encode($response);
?>
