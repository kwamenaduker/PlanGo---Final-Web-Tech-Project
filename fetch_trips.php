<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if (isset($_SESSION['UserID'])) {
    $userId = $_SESSION['UserID'];
    $today = date("Y-m-d");

    // Get search and filter parameters
    $search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

    // Base query for trips with search functionality
    $query = "SELECT TripID, TripName, Destination, StartDate, EndDate 
              FROM Trips 
              WHERE UserID = ? AND (TripName LIKE ? OR Destination LIKE ?)";
    $params = [$userId, $search, $search];
    $types = "iss";

    // Apply date filters if provided
    if ($startDate && $endDate) {
        $query .= " AND ((StartDate BETWEEN ? AND ?) OR (EndDate BETWEEN ? AND ?))";
        $params[] = $startDate;
        $params[] = $endDate;
        $params[] = $startDate;
        $params[] = $endDate;
        $types .= "ssss";
    }

    $query .= " ORDER BY StartDate ASC";

    try {
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $upcomingTrips = [];
        $previousTrips = [];

        while ($row = $result->fetch_assoc()) {
            if ($row['StartDate'] >= $today) {
                $upcomingTrips[] = $row;
            } else {
                $previousTrips[] = $row;
            }
        }

        $response["success"] = true;
        $response["upcomingTrips"] = $upcomingTrips;
        $response["previousTrips"] = $previousTrips;
    } catch (Exception $e) {
        $response["error"] = $e->getMessage();
    }
} else {
    $response["error"] = "User not logged in.";
}

echo json_encode($response);
?>
