<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tripId = intval($_POST['tripId']);
    $activity = trim($_POST['activity']);
    $activityDate = $_POST['activityDate'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $notes = trim($_POST['notes']);

    if (empty($activity) || empty($activityDate) || empty($startTime) || empty($endTime)) {
        $response["error"] = "All fields except notes are required.";
        echo json_encode($response);
        exit;
    }

    $sql = "INSERT INTO Itineraries (TripID, Activity, ActivityDate, StartTime, EndTime, Notes) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("isssss", $tripId, $activity, $activityDate, $startTime, $endTime, $notes);
        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Activity added successfully.";
        } else {
            $response["error"] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $response["error"] = "Failed to prepare statement: " . $conn->error;
    }
}

echo json_encode($response);
?>
