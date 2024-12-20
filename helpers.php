<?php
// Ensure the session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check user authorization by role
function isAuthorized($requiredRole) {
    // Check if the user is logged in
    if (!isset($_SESSION['UserID'])) {
        echo json_encode(["success" => false, "error" => "User not logged in."]);
        exit;
    }

    // Check if the user has the required role
    if ($_SESSION['Role'] !== $requiredRole) {
        echo json_encode(["success" => false, "error" => "Access denied."]);
        exit;        
    }
}
?>
