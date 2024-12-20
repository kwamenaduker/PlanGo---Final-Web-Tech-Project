<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $itineraryId = intval($_POST['itineraryId']);

    $sql = "DELETE FROM Itineraries WHERE ItineraryID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $itineraryId);
        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Itinerary deleted successfully.";
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
