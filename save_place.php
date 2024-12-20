<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['UserID'])) {
    $userId = $_SESSION['UserID'];
    $placeName = trim($_POST['placeName']);
    $location = isset($_POST['location']) ? trim($_POST['location']) : null;
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;

    if (empty($placeName)) {
        $response["error"] = "Place name is required.";
    } else {
        // Check for duplicates
        $checkQuery = "SELECT * FROM SavedPlaces WHERE UserID = ? AND PlaceName = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("is", $userId, $placeName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response["error"] = "This place is already saved.";
        } else {
            // Insert new place
            $insertQuery = "INSERT INTO SavedPlaces (UserID, PlaceName, Location, Notes) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("isss", $userId, $placeName, $location, $notes);

            if ($stmt->execute()) {
                $response["success"] = true;
                $response["message"] = "Place saved successfully!";
            } else {
                $response["error"] = "Failed to save place. Please try again.";
            }

            $stmt->close();
            $conn->close();
        }
    }
} else {
    $response["error"] = "Invalid request or user not logged in.";
}

echo json_encode($response);
exit;
?>
