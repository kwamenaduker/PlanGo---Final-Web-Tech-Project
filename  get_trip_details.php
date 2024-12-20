<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if (isset($_GET['tripId']) && isset($_SESSION['UserID'])) {
    $tripId = $_GET['tripId'];
    $userId = $_SESSION['UserID'];

    if (!is_numeric($tripId)) {
        $response["error"] = "Invalid Trip ID.";
        http_response_code(400);
        echo json_encode($response);
        exit;
    }

    $query = "SELECT TripName, Destination, StartDate, EndDate, Description FROM Trips WHERE TripID = ? AND UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $tripId, $userId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $response["success"] = true;
            $response["trip"] = $row;
        } else {
            $response["error"] = "Trip not found.";
            http_response_code(404);
        }
    } else {
        $response["error"] = "Database error.";
        error_log("Database error: " . $stmt->error); // Log the error
        http_response_code(500);
    }
} else {
    $response["error"] = "Invalid request.";
    http_response_code(400);
}

echo json_encode($response);
?>
