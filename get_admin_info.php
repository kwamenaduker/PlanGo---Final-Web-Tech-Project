<?php
header('Content-Type: application/json');
session_start();
include 'config.php';

$response = ['success' => false];

// Check if user is logged in and is admin
if (!isset($_SESSION['UserID']) || !isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
    $response['error'] = 'Unauthorized access';
    echo json_encode($response);
    exit();
}

try {
    $stmt = $conn->prepare('SELECT FirstName, LastName, Email FROM Users WHERE UserID = ? AND Role = "Admin"');
    $stmt->bind_param('i', $_SESSION['UserID']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($admin = $result->fetch_assoc()) {
        $response['success'] = true;
        $response['admin'] = $admin;
    } else {
        $response['error'] = 'Admin not found';
    }
} catch (Exception $e) {
    $response['error'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>
