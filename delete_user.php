<?php
include 'config.php';
include 'helpers.php';
isAuthorized('Admin');

$response = ["success" => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'];

    $sql = "DELETE FROM Users WHERE UserID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $response["success"] = true;
        $response["message"] = "User deleted successfully.";
    } else {
        $response["error"] = "Failed to prepare statement: " . $conn->error;
    }
}

echo json_encode($response);
?>
