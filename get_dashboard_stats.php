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
    // Get total users
    $userQuery = "SELECT COUNT(*) as total FROM Users WHERE Role != 'Admin'";
    $userResult = $conn->query($userQuery);
    $totalUsers = $userResult->fetch_assoc()['total'];

    // Get active trips (trips that haven't ended yet)
    $tripQuery = "SELECT COUNT(*) as total FROM Trips WHERE EndDate >= CURDATE()";
    $tripResult = $conn->query($tripQuery);
    $activeTrips = $tripResult->fetch_assoc()['total'];

    // Get total revenue (sum of all trip budgets)
    $revenueQuery = "SELECT SUM(Budget) as total FROM Trips";
    $revenueResult = $conn->query($revenueQuery);
    $totalRevenue = $revenueResult->fetch_assoc()['total'] ?? 0;

    $response['success'] = true;
    $response['stats'] = [
        'totalUsers' => $totalUsers,
        'activeTrips' => $activeTrips,
        'totalRevenue' => floatval($totalRevenue)
    ];
} catch (Exception $e) {
    $response['error'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>
