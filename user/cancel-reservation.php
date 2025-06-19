<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'])) {
    $reservation_id = intval($_POST['reservation_id']);
    $user_id = $_SESSION['user_id'];

    try {
        // Start transaction
        $conn->begin_transaction();

        // Check if reservation belongs to user and is pending
        $check_query = "SELECT * FROM reservations WHERE id = ? AND user_id = ? AND status = 'pending'";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ii", $reservation_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Reservation not found or cannot be cancelled");
        }

        // Update reservation status to cancelled
        $update_query = "UPDATE reservations SET status = 'cancelled' WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ii", $reservation_id, $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to cancel reservation");
        }

        $conn->commit();
        $_SESSION['success'] = "Reservation cancelled successfully";

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid request";
}

header('Location: dashboard.php');
exit();
?>