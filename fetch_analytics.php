<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ["success" => false];

// Debug: Log session data
error_log("Session data: " . print_r($_SESSION, true));

if (!isset($conn)) {
    $response['error'] = "Database connection not established";
    $response['debug'] = "config.php might not be included properly";
    echo json_encode($response);
    exit();
}

if ($conn->connect_error) {
    $response['error'] = "Database connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit();
}

if (isset($_SESSION['UserID'])) {
    $userId = $_SESSION['UserID'];
    $role = $_SESSION['Role'];

    try {
        $data = [];

        if ($role === 'User') {
            // Total Trips
            $stmt = $conn->prepare("SELECT COUNT(*) AS TotalTrips FROM Trips WHERE UserID = ?");
            if (!$stmt) {
                throw new Exception("Error preparing total trips query: " . $conn->error);
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $data['totalTrips'] = $result->fetch_assoc()['TotalTrips'];

            // Completed Trips (end date is in the past)
            $stmt = $conn->prepare("SELECT COUNT(*) AS CompletedTrips FROM Trips WHERE UserID = ? AND EndDate < CURDATE()");
            if (!$stmt) {
                throw new Exception("Error preparing completed trips query: " . $conn->error);
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $data['completedTrips'] = $result->fetch_assoc()['CompletedTrips'];

            // Upcoming Trips (start date is in the future)
            $stmt = $conn->prepare("SELECT COUNT(*) AS UpcomingTrips FROM Trips WHERE UserID = ? AND StartDate > CURDATE()");
            if (!$stmt) {
                throw new Exception("Error preparing upcoming trips query: " . $conn->error);
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $data['upcomingTrips'] = $result->fetch_assoc()['UpcomingTrips'];

            // Total Budgets
            $stmt = $conn->prepare("SELECT COALESCE(SUM(Amount), 0) AS TotalBudget FROM Budgets WHERE TripID IN (SELECT TripID FROM Trips WHERE UserID = ?)");
            if (!$stmt) {
                throw new Exception("Error preparing total budget query: " . $conn->error);
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $data['totalBudget'] = $result->fetch_assoc()['TotalBudget'];

            // Average Trip Budget
            $stmt = $conn->prepare("
                SELECT COALESCE(AVG(TripBudget), 0) AS AvgBudget 
                FROM (
                    SELECT t.TripID, COALESCE(SUM(b.Amount), 0) AS TripBudget 
                    FROM Trips t 
                    LEFT JOIN Budgets b ON t.TripID = b.TripID 
                    WHERE t.UserID = ? 
                    GROUP BY t.TripID
                ) AS TripBudgets
            ");
            if (!$stmt) {
                throw new Exception("Error preparing average budget query: " . $conn->error);
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $avgBudget = $result->fetch_assoc()['AvgBudget'];
            $data['averageBudget'] = $avgBudget;

            // Budget Breakdown
            $stmt = $conn->prepare("
                SELECT Category, COALESCE(SUM(Amount), 0) AS TotalAmount 
                FROM Budgets 
                WHERE TripID IN (SELECT TripID FROM Trips WHERE UserID = ?) 
                GROUP BY Category
            ");
            if (!$stmt) {
                throw new Exception("Error preparing budget breakdown query: " . $conn->error);
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $data['budgetBreakdown'] = $result->fetch_all(MYSQLI_ASSOC);
        } elseif ($role === 'Admin') {
            // Debug: Log that we're in admin section
            error_log("Processing admin analytics");

            // Total Users
            $stmt = $conn->prepare("SELECT COUNT(*) AS TotalUsers FROM Users WHERE Role = 'User'");
            if (!$stmt) {
                throw new Exception("Error preparing users query: " . $conn->error);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data['totalUsers'] = $result->fetch_assoc()['TotalUsers'];

            // Total Trips
            $stmt = $conn->prepare("SELECT COUNT(*) AS TotalTrips FROM Trips");
            if (!$stmt) {
                throw new Exception("Error preparing trips query: " . $conn->error);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data['totalTrips'] = $result->fetch_assoc()['TotalTrips'];

            // Popular Destinations
            $stmt = $conn->prepare("
                SELECT Destination, COUNT(*) AS TripsCount 
                FROM Trips 
                GROUP BY Destination 
                ORDER BY TripsCount DESC 
                LIMIT 5
            ");
            if (!$stmt) {
                throw new Exception("Error preparing destinations query: " . $conn->error);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data['popularDestinations'] = $result->fetch_all(MYSQLI_ASSOC);

            // Total Budget
            $stmt = $conn->prepare("SELECT COALESCE(SUM(Amount), 0) AS TotalBudget FROM Budgets");
            if (!$stmt) {
                throw new Exception("Error preparing total budget query: " . $conn->error);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data['totalBudget'] = $result->fetch_assoc()['TotalBudget'];

            // Average Trip Budget
            $stmt = $conn->prepare("
                SELECT COALESCE(AVG(TripBudget), 0) AS AvgBudget 
                FROM (
                    SELECT t.TripID, COALESCE(SUM(b.Amount), 0) AS TripBudget 
                    FROM Trips t 
                    LEFT JOIN Budgets b ON t.TripID = b.TripID 
                    GROUP BY t.TripID
                ) AS TripBudgets
            ");
            if (!$stmt) {
                throw new Exception("Error preparing average budget query: " . $conn->error);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $avgBudget = $result->fetch_assoc()['AvgBudget'];
            
            // Debug: Log the average budget value
            error_log("Average Budget: " . $avgBudget);
            
            $data['averageBudget'] = $avgBudget;

            // Budget Breakdown
            $stmt = $conn->prepare("
                SELECT Category, COALESCE(SUM(Amount), 0) AS TotalAmount 
                FROM Budgets 
                GROUP BY Category
            ");
            if (!$stmt) {
                throw new Exception("Error preparing budget breakdown query: " . $conn->error);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data['budgetBreakdown'] = $result->fetch_all(MYSQLI_ASSOC);
        }

        // Debug: Log the final data array
        error_log("Final data array: " . print_r($data, true));

        $response['success'] = true;
        $response['data'] = $data;
    } catch (Exception $e) {
        error_log("Error in fetch_analytics.php: " . $e->getMessage());
        $response['error'] = $e->getMessage();
        $response['debug'] = $e->getTraceAsString();
    }
} else {
    $response['error'] = "User not logged in.";
}

// Debug: Log the final response
error_log("Final response: " . print_r($response, true));

echo json_encode($response);
?>
