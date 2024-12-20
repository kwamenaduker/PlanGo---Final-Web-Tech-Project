<?php
header("Content-Type: application/json");
session_start();
include 'config.php';

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $budgetId = intval($_POST['budgetId']);
    $category = trim($_POST['category']);
    $amount = floatval($_POST['amount']);

    if (empty($category) || $amount <= 0) {
        $response["error"] = "Invalid category or amount.";
        echo json_encode($response);
        exit;
    }

    $sql = "UPDATE Budgets SET Category = ?, Amount = ? WHERE BudgetID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sdi", $category, $amount, $budgetId);
        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Budget updated successfully.";
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
