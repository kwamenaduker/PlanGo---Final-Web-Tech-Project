<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if (isset($_GET['tripId'])) {
    $tripId = intval($_GET['tripId']);

    $sql = "SELECT BudgetID, Category, Amount FROM Budgets WHERE TripID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $tripId);
        $stmt->execute();
        $result = $stmt->get_result();
        $budgets = $result->fetch_all(MYSQLI_ASSOC);

        $response["success"] = true;
        $response["budgets"] = $budgets;
        $stmt->close();
    } else {
        $response["error"] = "Database error: " . $conn->error;
    }
} else {
    $response["error"] = "Invalid request.";
}

echo json_encode($response);
?>
