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

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        try {
            $query = "SELECT t.*, u.FirstName, u.LastName, u.Email 
                     FROM Trips t 
                     JOIN Users u ON t.UserID = u.UserID 
                     WHERE 1=1";
            
            $params = [];
            $types = "";
            
            // Filter by user
            if (isset($_GET['user_id']) && $_GET['user_id'] !== '') {
                $query .= " AND t.UserID = ?";
                $params[] = $_GET['user_id'];
                $types .= "i";
            }
            
            // Search functionality
            if (isset($_GET['search']) && $_GET['search'] !== '') {
                $searchTerm = "%" . $_GET['search'] . "%";
                $query .= " AND (t.TripName LIKE ? OR t.Destination LIKE ? OR u.FirstName LIKE ? OR u.LastName LIKE ?)";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
                $types .= "ssss";
            }
            
            $query .= " ORDER BY t.StartDate DESC";
            
            $stmt = $conn->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $trips = [];
            
            while ($row = $result->fetch_assoc()) {
                $trips[] = $row;
            }
            
            $response['success'] = true;
            $response['trips'] = $trips;
        } catch (Exception $e) {
            $response['error'] = "Database error: " . $e->getMessage();
        }
        break;

    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['user_id'], $data['trip_name'], $data['destination'], $data['start_date'], $data['end_date'], $data['budget'])) {
            $response['error'] = "Missing required fields";
            break;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO Trips (UserID, TripName, Destination, StartDate, EndDate, Budget) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssd", $data['user_id'], $data['trip_name'], $data['destination'], $data['start_date'], $data['end_date'], $data['budget']);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Trip created successfully";
                $response['trip_id'] = $conn->insert_id;
            } else {
                $response['error'] = "Failed to create trip";
            }
        } catch (Exception $e) {
            $response['error'] = "Database error: " . $e->getMessage();
        }
        break;

    case 'update':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['trip_id'], $data['trip_name'], $data['destination'], $data['start_date'], $data['end_date'], $data['budget'])) {
            $response['error'] = "Missing required fields";
            break;
        }

        try {
            $stmt = $conn->prepare("UPDATE Trips SET TripName = ?, Destination = ?, StartDate = ?, EndDate = ?, Budget = ? WHERE TripID = ?");
            $stmt->bind_param("ssssdi", $data['trip_name'], $data['destination'], $data['start_date'], $data['end_date'], $data['budget'], $data['trip_id']);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Trip updated successfully";
            } else {
                $response['error'] = "Failed to update trip";
            }
        } catch (Exception $e) {
            $response['error'] = "Database error: " . $e->getMessage();
        }
        break;

    case 'delete':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['trip_id'])) {
            $response['error'] = "Missing trip ID";
            break;
        }

        try {
            // First delete related records in other tables
            $stmt = $conn->prepare("DELETE FROM Budgets WHERE TripID = ?");
            $stmt->bind_param("i", $data['trip_id']);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM Trips WHERE TripID = ?");
            $stmt->bind_param("i", $data['trip_id']);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Trip deleted successfully";
            } else {
                $response['error'] = "Failed to delete trip";
            }
        } catch (Exception $e) {
            $response['error'] = "Database error: " . $e->getMessage();
        }
        break;

    default:
        $response['error'] = "Invalid action";
        break;
}

echo json_encode($response);
?>
