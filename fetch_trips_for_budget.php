<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if (isset($_SESSION['UserID'])) {
    $userId = $_SESSION['UserID'];

    $sql = "SELECT TripID, TripName FROM Trips WHERE UserID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $trips = $result->fetch_all(MYSQLI_ASSOC);

        $response["success"] = true;
        $response["trips"] = $trips;
        $stmt->close();
    } else {
        $response["error"] = "Database error: " . $conn->error;
    }
} else {
    $response["error"] = "User not logged in.";
}

echo json_encode($response);
?>
