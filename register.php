<?php
header("Content-Type: application/json");
include 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ["success" => false, "error" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Input Validation
    if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($confirm_password)) {
        $response["error"] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $response["error"] = "Passwords do not match.";
    } else {
        // Check if email already exists
        $sql = "SELECT * FROM Users WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response["error"] = "Email is already registered.";
        } else {
            // Hash password and insert into database
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO Users (FirstName, LastName, Email, Password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $fname, $lname, $email, $hashed_password);

            if ($stmt->execute()) {
                $response["success"] = true;
            } else {
                $response["error"] = "An unexpected error occurred. Please try again.";
            }
        }

        $stmt->close();
        $conn->close();
    }

    echo json_encode($response);
    exit;
}
?>