<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $itineraryId = intval($_POST['itineraryId']);
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

    $sql = "UPDATE Itineraries SET Activity = ?, ActivityDate = ?, StartTime = ?, EndTime = ?, Notes = ? WHERE ItineraryID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssssi", $activity, $activityDate, $startTime, $endTime, $notes, $itineraryId);
        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Itinerary updated successfully.";
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
