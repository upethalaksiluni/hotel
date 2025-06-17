<?php
session_start();

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'shangrila_db';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Check if user exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Debug information
        error_log("Attempting login for email: " . $email);
        error_log("Stored hash: " . $user['password']);
        error_log("Input password: " . $password);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit();
        } else {
            error_log("Password verification failed");
            $_SESSION['error'] = "Invalid password";
        }
    } else {
        error_log("User not found: " . $email);
        $_SESSION['error'] = "User not found";
    }

    // If login fails, redirect back to login page
    header("Location: login.php");
    exit();
}

$conn->close();
?>