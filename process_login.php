<?php
session_start();
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        // Check if user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        if (!$stmt) {
            throw new Exception("Database error");
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Clear any existing error messages
            unset($_SESSION['error']);

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: indexroot.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password";
            header("Location: login.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "An error occurred. Please try again.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>