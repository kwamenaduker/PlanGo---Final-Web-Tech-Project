<?php
include 'config.php';
include 'helpers.php';
isAuthorized('Admin');

$response = ["success" => false];

try {
    session_start();
    $adminName = $_SESSION['FirstName']; // Get admin's first name from session

    // Exclude the admin user from the user list
    $sql = "SELECT UserID, FirstName, LastName, Email, Role FROM Users WHERE Role != 'Admin'";
    $result = $conn->query($sql);
    $users = $result->fetch_all(MYSQLI_ASSOC);

    $response["success"] = true;
    $response["adminName"] = $adminName; // Add admin name to response
    $response["users"] = $users;
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

echo json_encode($response);
?>
