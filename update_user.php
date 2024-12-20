<?php
include 'config.php';
include 'helpers.php';
isAuthorized('Admin');

$response = ["success" => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $sql = "UPDATE Users SET FirstName = ?, LastName = ?, Email = ?, Role = ? WHERE UserID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssi", $firstName, $lastName, $email, $role, $userId);
        $stmt->execute();
        $response["success"] = true;
        $response["message"] = "User updated successfully.";
    } else {
        $response["error"] = "Failed to prepare statement: " . $conn->error;
    }
}

echo json_encode($response);
?>
