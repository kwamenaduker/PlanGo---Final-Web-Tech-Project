<?php
header('Content-Type: application/json');
session_start();
include 'config.php';

$response = ['success' => false];

// Debug connection
if (!isset($conn)) {
    $response['error'] = 'Database connection not established';
    $response['debug'] = ['config_included' => isset($config)];
    echo json_encode($response);
    exit();
}

// Check database connection
if ($conn->connect_error) {
    $response['error'] = 'Database connection failed: ' . $conn->connect_error;
    echo json_encode($response);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['UserID'])) {
    $response['error'] = 'Unauthorized access: No UserID in session';
    $response['debug'] = ['session' => $_SESSION];
    echo json_encode($response);
    exit();
}

try {
    $userId = $_SESSION['UserID'];
    
    // First, verify if the user exists
    $userCheck = $conn->prepare("SELECT UserID FROM Users WHERE UserID = ?");
    if (!$userCheck) {
        throw new Exception("Error preparing user check query: " . $conn->error);
    }
    
    $userCheck->bind_param('i', $userId);
    $userCheck->execute();
    $userResult = $userCheck->get_result();
    
    if ($userResult->num_rows === 0) {
        $response['error'] = 'User not found in database';
        $response['debug'] = ['userId' => $userId];
        echo json_encode($response);
        exit();
    }

    // Get trip statistics
    $tripStatsQuery = "SELECT 
        COUNT(*) as totalTrips,
        SUM(CASE WHEN EndDate < CURRENT_DATE() THEN 1 ELSE 0 END) as completedTrips,
        SUM(CASE WHEN StartDate > CURRENT_DATE() THEN 1 ELSE 0 END) as upcomingTrips
        FROM Trips 
        WHERE UserID = ?";
    
    $stmt = $conn->prepare($tripStatsQuery);
    if (!$stmt) {
        throw new Exception("Error preparing trip stats query: " . $conn->error);
    }
    
    $stmt->bind_param('i', $userId);
    if (!$stmt->execute()) {
        throw new Exception("Error executing trip stats query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Error getting trip stats result: " . $stmt->error);
    }
    
    $tripStats = $result->fetch_assoc();
    
    // Get total and average budget from Budgets table
    $budgetQuery = "SELECT 
        COALESCE(SUM(b.Amount), 0) as totalBudget,
        COALESCE(AVG(b.Amount), 0) as avgTripCost
        FROM Trips t
        LEFT JOIN Budgets b ON t.TripID = b.TripID
        WHERE t.UserID = ?";
    
    $stmt = $conn->prepare($budgetQuery);
    if (!$stmt) {
        throw new Exception("Error preparing budget query: " . $conn->error);
    }
    
    $stmt->bind_param('i', $userId);
    if (!$stmt->execute()) {
        throw new Exception("Error executing budget query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Error getting budget result: " . $stmt->error);
    }
    
    $budgetStats = $result->fetch_assoc();

    $response['success'] = true;
    $response['stats'] = [
        'totalTrips' => (int)($tripStats['totalTrips'] ?? 0),
        'completedTrips' => (int)($tripStats['completedTrips'] ?? 0),
        'upcomingTrips' => (int)($tripStats['upcomingTrips'] ?? 0),
        'totalBudget' => (float)($budgetStats['totalBudget'] ?? 0),
        'avgTripCost' => (float)($budgetStats['avgTripCost'] ?? 0)
    ];

    // Add debug information
    $response['debug'] = [
        'userId' => $userId,
        'queries' => [
            'tripStats' => $tripStatsQuery,
            'budget' => $budgetQuery
        ],
        'raw_data' => [
            'tripStats' => $tripStats,
            'budgetStats' => $budgetStats
        ]
    ];

} catch (Exception $e) {
    $response['error'] = 'Database error: ' . $e->getMessage();
    $response['debug'] = [
        'errorDetails' => $e->getTraceAsString(),
        'userId' => $userId ?? null,
        'lastQuery' => $conn->error ?? null
    ];
}

echo json_encode($response);
?>
