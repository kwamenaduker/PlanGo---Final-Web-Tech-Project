<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tripId = $_POST['tripId'];
    $category = trim($_POST['category']);
    $amount = floatval($_POST['amount']);

    if (empty($category) || $amount <= 0) {
        $response["error"] = "Invalid category or amount.";
        echo json_encode($response);
        exit;
    }

    $sql = "INSERT INTO Budgets (TripID, Category, Amount) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("isd", $tripId, $category, $amount);
        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Budget added successfully.";
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
