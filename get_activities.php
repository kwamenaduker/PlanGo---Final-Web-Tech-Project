<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

// Check if user is admin
if (!isset($_SESSION['UserID']) || !isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Admin') {
    echo json_encode(["error" => "Unauthorized access"]);
    exit();
}

$response = ["success" => false];

try {
    // Get filter parameters
    $activityType = $_GET['type'] ?? 'all';
    $date = $_GET['date'] ?? null;

    // Base query
    $query = "SELECT a.*, u.FirstName, u.LastName 
              FROM ActivityLogs a 
              LEFT JOIN Users u ON a.UserID = u.UserID 
              WHERE 1=1";

    // Add filters
    if ($activityType !== 'all') {
        $query .= " AND a.ActivityType = '$activityType'";
    }
    if ($date) {
        $query .= " AND DATE(a.Timestamp) = '$date'";
    }

    // Order by most recent
    $query .= " ORDER BY a.Timestamp DESC LIMIT 50";

    $result = $conn->query($query);
    $activities = [];

    while ($row = $result->fetch_assoc()) {
        // Format the activity data
        $activity = [
            'id' => $row['LogID'],
            'type' => $row['ActivityType'],
            'action' => $row['Action'],
            'description' => $row['Description'],
            'timestamp' => $row['Timestamp'],
            'user' => $row['UserID'] ? $row['FirstName'] . ' ' . $row['LastName'] : 'System'
        ];

        $activities[] = $activity;
    }

    $response['success'] = true;
    $response['activities'] = $activities;

} catch (Exception $e) {
    $response['error'] = "Database error: " . $e->getMessage();
}

echo json_encode($response);
?>
