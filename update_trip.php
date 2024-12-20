<?php
ini_set('display_errors', 1);  // Enable error display
error_reporting(E_ALL);        // Report all errors
header("Content-Type: application/json");
include 'config.php';
session_start();

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['UserID'])) {
    $userId = $_SESSION["UserID"];
    $tripId = $_POST['tripId'];
    $tripName = $_POST['tripName'];
    $destination = $_POST['destination'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $description = $_POST['description'];

    if (empty($tripName) || empty($startDate) || empty($endDate)) {
        $response["error"] = "Trip name, start date, and end date are required.";
    } elseif (strtotime($startDate) > strtotime($endDate)) {
        $response["error"] = "Start date cannot be later than end date.";
    } else {
        $sql = "UPDATE Trips 
            SET TripName = ?, Destination = ?, StartDate = ?, EndDate = ?, Description = ? 
            WHERE TripID = ? AND UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssiii", $tripName, $destination, $startDate, $endDate, $description, $tripId, $userId);

        if ($stmt->execute()) {
            // Log activity in Activities table
            $activityQuery = "INSERT INTO Activities (UserID, Description) VALUES (?, ?)";
            $activityDescription = "Updated a trip: $tripName.";
            $activityStmt = $conn->prepare($activityQuery);
            $activityStmt->bind_param("is", $userId, $activityDescription);
            $activityStmt->execute();

            $response["success"] = true;
            $response["message"] = "Trip updated successfully!";
        } else {
            $response["error"] = "Failed to update trip. Please try again.";
        }

        $stmt->close();
        $conn->close();

    }
} else {
    $response["error"] = "Unauthorized request.";
}

echo json_encode($response);
?>
