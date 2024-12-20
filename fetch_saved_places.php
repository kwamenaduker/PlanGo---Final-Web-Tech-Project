<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if (isset($_SESSION['UserID'])) {
    $userId = $_SESSION['UserID'];

    try {
        $query = "SELECT PlaceID, PlaceName, Location, Notes, CreatedAt FROM SavedPlaces WHERE UserID = ? ORDER BY CreatedAt DESC";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $places = [];
        while ($row = $result->fetch_assoc()) {
            $places[] = $row;
        }

        $response["success"] = true;
        $response["places"] = $places;
    } catch (Exception $e) {
        $response["error"] = $e->getMessage();
    }
} else {
    $response["error"] = "User not logged in.";
}

echo json_encode($response);
exit;
?>
