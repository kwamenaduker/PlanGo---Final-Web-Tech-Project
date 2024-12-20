<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['UserID'])) {
    $userId = $_SESSION['UserID'];
    $tripId = $_POST['tripId'];

    if (empty($tripId)) {
        $response["error"] = "Trip ID is required.";
    } else {
        $sql = "DELETE FROM Trips WHERE TripID = ? AND UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $tripId, $userId);

        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Trip deleted successfully.";
        } else {
            $response["error"] = "Failed to delete trip. Please try again.";
        }

        $stmt->close();
    }
} else {
    $response["error"] = "Invalid request or user not logged in.";
}

echo json_encode($response);
exit;
?>
