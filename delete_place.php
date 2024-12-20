<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if (isset($_POST['placeId']) && isset($_SESSION['UserID'])) {
    $placeId = $_POST['placeId'];
    $userId = $_SESSION['UserID'];

    // Validate ownership
    $checkQuery = "SELECT * FROM SavedPlaces WHERE PlaceID = ? AND UserID = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $placeId, $userId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Delete the place
        $deleteQuery = "DELETE FROM SavedPlaces WHERE PlaceID = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $placeId);

        if ($deleteStmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Place deleted successfully.";
        } else {
            $response["error"] = "Failed to delete the place.";
        }
    } else {
        $response["error"] = "Place not found or access denied.";
    }
} else {
    $response["error"] = "Invalid request.";
}

echo json_encode($response);
?>
