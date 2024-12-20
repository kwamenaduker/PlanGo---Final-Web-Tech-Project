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

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['userId']) || !isset($data['firstname']) || !isset($data['lastname']) || !isset($data['email']) || !isset($data['role'])) {
    $response['error'] = 'Missing required fields';
    echo json_encode($response);
    exit();
}

try {
    $query = "UPDATE Users SET FirstName = ?, LastName = ?, Email = ?, Role = ? WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Error preparing query: " . $conn->error);
    }
    
    $stmt->bind_param('ssssi', 
        $data['firstname'],
        $data['lastname'],
        $data['email'],
        $data['role'],
        $data['userId']
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing query: " . $stmt->error);
    }
    
    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'User updated successfully';
    } else {
        $response['error'] = 'No changes made to user';
    }
    
} catch (Exception $e) {
    $response['error'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
