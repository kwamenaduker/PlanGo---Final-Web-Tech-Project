<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if (isset($_GET['tripId'])) {
    $tripId = intval($_GET['tripId']);

    $sql = "SELECT * FROM Itineraries WHERE TripID = ? ORDER BY ActivityDate, StartTime";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $tripId);
        $stmt->execute();
        $result = $stmt->get_result();
        $itineraries = $result->fetch_all(MYSQLI_ASSOC);

        $response["success"] = true;
        $response["itineraries"] = $itineraries;
        $stmt->close();
    } else {
        $response["error"] = "Database error: " . $conn->error;
    }
} else {
    $response["error"] = "Invalid request.";
}

echo json_encode($response);
?>
