<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if (isset($_SESSION['UserID'])) {
    $userId = $_SESSION['UserID'];

    // Query to fetch plansCreated
    $plansQuery = "SELECT COUNT(*) AS plansCreated FROM Trips WHERE UserID = ?";
    $stmt = $conn->prepare($plansQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['plansCreated'] = $result->fetch_assoc()['plansCreated'];

    // Query to fetch upcomingTrips
    $today = date("Y-m-d");
    $upcomingQuery = "SELECT COUNT(*) AS upcomingTrips FROM Trips WHERE UserID = ? AND StartDate >= ?";
    $stmt = $conn->prepare($upcomingQuery);
    $stmt->bind_param("is", $userId, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['upcomingTrips'] = $result->fetch_assoc()['upcomingTrips'];

    // Query to fetch savedPlaces
    $savedPlacesQuery = "SELECT COUNT(*) AS savedPlaces FROM SavedPlaces WHERE UserID = ?";
    $stmt = $conn->prepare($savedPlacesQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['savedPlaces'] = $result->fetch_assoc()['savedPlaces'];

    // Query to fetch activities
    $activitiesQuery = "
    SELECT CONCAT('Created a new trip to ', Destination, ': ', TripName) AS Description 
    FROM Trips 
    WHERE UserID = ? 
    ORDER BY CreatedAt DESC 
    LIMIT 5";
    $stmt = $conn->prepare($activitiesQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row['Description'];
    }

    // Combine stats and activities in response
    $response["success"] = true;
    $response["stats"] = $stats;
    $response["activities"] = $activities;
    $response["user"] = ["fname" => $_SESSION['FirstName']];
} else {
    $response["error"] = "User not logged in.";
}

echo json_encode($response);
exit;
?>
