<?php
session_start();

echo "<pre>";
print_r($_SESSION); // Outputs session variables for debugging
echo "</pre>";

// Redirect to login if user is not logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.html");
    exit;
}

// Redirect to dashboard if user is not an admin
if ($_SESSION['Role'] !== 'Admin') {
    header("Location: admin_dashboard.html");
    exit;
}


?>
