<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if (isset($_POST['placeId'], $_POST['placeName'], $_SESSION['UserID'])) {
    $placeId = $_POST['placeId'];
    $placeName = trim($_POST['placeName']);
    $location = isset($_POST['location']) ? trim($_POST['location']) : null;
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;
    $userId = $_SESSION['UserID'];

    // Validate ownership
    $checkQuery = "SELECT * FROM SavedPlaces WHERE PlaceID = ? AND UserID = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $placeId, $userId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Update the place
        $updateQuery = "UPDATE SavedPlaces SET PlaceName = ?, Location = ?, Notes = ? WHERE PlaceID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("sssi", $placeName, $location, $notes, $placeId);

        if ($updateStmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Place updated successfully.";
        } else {
            $response["error"] = "Failed to update the place.";
        }
    } else {
        $response["error"] = "Place not found or access denied.";
    }
} else {
    $response["error"] = "Invalid request. Ensure all required fields are provided.";
}

echo json_encode($response);
?>
