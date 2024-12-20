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
    // Get all users except the current admin
    $query = "SELECT UserID, FirstName, LastName, Email, Role, CreatedAt 
              FROM Users 
              WHERE UserID != ? 
              ORDER BY CreatedAt DESC";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Error preparing query: " . $conn->error);
    }
    
    $stmt->bind_param('i', $_SESSION['UserID']);
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $users = [];
    
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'UserID' => $row['UserID'],
            'FirstName' => $row['FirstName'],
            'LastName' => $row['LastName'],
            'Email' => $row['Email'],
            'Role' => $row['Role'],
            'CreatedAt' => $row['CreatedAt']
        ];
    }
    
    $response['success'] = true;
    $response['users'] = $users;
    
} catch (Exception $e) {
    $response['error'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
