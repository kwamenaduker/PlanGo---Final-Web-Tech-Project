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
    $userQuery = "SELECT COUNT(*) as total_users FROM Users WHERE Role = 'User'";
    $userResult = $conn->query($userQuery);
    $totalUsers = $userResult->fetch_assoc()['total_users'];

    // Get most popular destination
    $destinationQuery = "
        SELECT Destination, COUNT(*) as visit_count 
        FROM Trips 
        GROUP BY Destination 
        ORDER BY visit_count DESC 
        LIMIT 1
    ";
    $destinationResult = $conn->query($destinationQuery);
    $popularDestination = $destinationResult->fetch_assoc();

    // Get average trip budget
    $budgetQuery = "
        SELECT AVG(Amount) as avg_budget 
        FROM Budgets
    ";
    $budgetResult = $conn->query($budgetQuery);
    $avgBudget = $budgetResult->fetch_assoc()['avg_budget'];

    $response['success'] = true;
    $response['data'] = [
        'total_users' => $totalUsers,
        'popular_destination' => $popularDestination ? $popularDestination['Destination'] : 'N/A',
        'average_budget' => $avgBudget ? round($avgBudget, 2) : 0
    ];

} catch (Exception $e) {
    $response['error'] = 'Error fetching statistics: ' . $e->getMessage();
}

echo json_encode($response);
?>
