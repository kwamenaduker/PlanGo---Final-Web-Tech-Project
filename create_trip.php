<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

// Check if user is logged in and is admin
if (!isset($_SESSION['UserID']) || !isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
    $response['error'] = 'Unauthorized access';
    echo json_encode($response);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Extract data
    $userId = $data['userId'] ?? null;
    $tripName = trim($data['tripName'] ?? '');
    $destination = trim($data['destination'] ?? '');
    $startDate = $data['startDate'] ?? '';
    $endDate = $data['endDate'] ?? '';
    $budget = $data['budget'] ?? 0;
    $description = trim($data['description'] ?? '');

    // Input Validation
    if (empty($userId) || empty($tripName) || empty($destination) || empty($startDate) || empty($endDate)) {
        $response["error"] = "All fields except description are required.";
    } elseif (new DateTime($startDate) > new DateTime($endDate)) {
        $response["error"] = "Start date cannot be later than end date.";
    } else {
        try {
            // Start transaction
            $conn->begin_transaction();

            // Insert trip into database
            $sql = "INSERT INTO Trips (UserID, TripName, Destination, StartDate, EndDate, Description) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparing trip insert: " . $conn->error);
            }
            
            $stmt->bind_param("isssss", $userId, $tripName, $destination, $startDate, $endDate, $description);
            
            if (!$stmt->execute()) {
                throw new Exception("Error executing trip insert: " . $stmt->error);
            }
            
            $tripId = $stmt->insert_id;

            // Insert budget with default category 'General'
            if ($budget > 0) {
                $budgetSql = "INSERT INTO Budgets (TripID, Category, Amount) VALUES (?, 'General', ?)";
                $budgetStmt = $conn->prepare($budgetSql);
                
                if (!$budgetStmt) {
                    throw new Exception("Error preparing budget insert: " . $conn->error);
                }
                
                $budgetStmt->bind_param("id", $tripId, $budget);
                
                if (!$budgetStmt->execute()) {
                    throw new Exception("Error executing budget insert: " . $budgetStmt->error);
                }
            }

            // Log activity
            $activitySql = "INSERT INTO Activities (UserID, Description) VALUES (?, ?)";
            $activityStmt = $conn->prepare($activitySql);
            $activityDescription = "Created a new trip: $tripName";
            
            if (!$activityStmt) {
                throw new Exception("Error preparing activity insert: " . $conn->error);
            }
            
            $activityStmt->bind_param("is", $userId, $activityDescription);
            
            if (!$activityStmt->execute()) {
                throw new Exception("Error executing activity insert: " . $activityStmt->error);
            }

            // If we got here, commit the transaction
            $conn->commit();
            
            $response["success"] = true;
            $response["message"] = "Trip created successfully";
            $response["tripId"] = $tripId;

        } catch (Exception $e) {
            // Something went wrong, rollback changes
            $conn->rollback();
            $response["error"] = "Failed to create trip: " . $e->getMessage();
        }
    }
} else {
    $response["error"] = "Invalid request method.";
}

echo json_encode($response);
?>
