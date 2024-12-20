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

if (!isset($data['tripId']) || !isset($data['tripName']) || !isset($data['destination']) || 
    !isset($data['startDate']) || !isset($data['endDate']) || !isset($data['description']) || 
    !isset($data['userId']) || !isset($data['budget'])) {
    $response['error'] = 'Missing required fields';
    echo json_encode($response);
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Update trip details
    $query = "UPDATE Trips SET 
              TripName = ?, 
              Destination = ?, 
              StartDate = ?, 
              EndDate = ?, 
              Description = ?,
              UserID = ?
              WHERE TripID = ?";
              
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Error preparing trip update query: " . $conn->error);
    }
    
    $stmt->bind_param('sssssii', 
        $data['tripName'],
        $data['destination'],
        $data['startDate'],
        $data['endDate'],
        $data['description'],
        $data['userId'],
        $data['tripId']
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing trip update query: " . $stmt->error);
    }

    // Update budget
    $budgetQuery = "UPDATE Budgets SET Amount = ? WHERE TripID = ?";
    $budgetStmt = $conn->prepare($budgetQuery);
    
    if (!$budgetStmt) {
        throw new Exception("Error preparing budget update query: " . $conn->error);
    }
    
    $budgetStmt->bind_param('di', $data['budget'], $data['tripId']);
    
    if (!$budgetStmt->execute()) {
        throw new Exception("Error executing budget update query: " . $budgetStmt->error);
    }

    // If we got here, commit the transaction
    $conn->commit();
    
    $response['success'] = true;
    $response['message'] = 'Trip updated successfully';
    
} catch (Exception $e) {
    // Something went wrong, rollback changes
    $conn->rollback();
    $response['error'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
