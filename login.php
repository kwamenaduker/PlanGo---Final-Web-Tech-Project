<?php
header("Content-Type: application/json");
session_start();
include 'config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = ["success" => false, "error" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Input Validation
    if (empty($email) || empty($password)) {
        $response["error"] = "Email and password are required.";
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = "Invalid email format.";
        echo json_encode($response);
        exit;
    }

    // Check if user exists
    $sql = "SELECT * FROM Users WHERE Email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $response["error"] = "Invalid email or password.";
        } else {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['Password'])) {
                $_SESSION['UserID'] = $user['UserID'];
                $_SESSION['FirstName'] = $user['FirstName'];
                $_SESSION['LastName'] = $user['LastName'];
                $_SESSION['Role'] = $user['Role'];

                $response["success"] = true;
                $response["role"] = $user['Role']; // Include role in the response
            } else {
                $response["error"] = "Invalid email or password.";
            }
        }

        $stmt->close();
    } else {
        $response["error"] = "Database query error: " . $conn->error;
    }

    $conn->close();
} else {
    $response["error"] = "Invalid request method.";
}

echo json_encode($response);
exit;
?>
