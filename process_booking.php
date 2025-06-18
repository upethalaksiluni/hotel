<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = intval($_POST['room_id']);
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $special_requests = trim($_POST['special_requests'] ?? '');
    $user_id = $_SESSION['user_id'];

    // Basic validation
    if (empty($check_in) || empty($check_out)) {
        $_SESSION['error'] = "Please select both check-in and check-out dates.";
        header('Location: room-booking.php');
        exit();
    }

    // Check if check-out date is after check-in date
    if (strtotime($check_out) <= strtotime($check_in)) {
        $_SESSION['error'] = "Check-out date must be after check-in date.";
        header('Location: room-booking.php');
        exit();
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Check room availability - Fixed the date overlap logic
        $availability_query = "SELECT COUNT(*) as count FROM reservations 
                             WHERE room_id = ? AND status IN ('pending', 'confirmed')
                             AND NOT (check_out_date <= ? OR check_in_date >= ?)";
        
        $stmt = $conn->prepare($availability_query);
        $stmt->bind_param("iss", $room_id, $check_in, $check_out);
        $stmt->execute();
        $result = $stmt->get_result();
        $availability = $result->fetch_assoc();

        if ($availability['count'] > 0) {
            throw new Exception("Room is not available for the selected dates");
        }

        // Get room details and verify it exists
        $room_query = "SELECT price_per_night, room_type FROM rooms WHERE id = ? AND status = 'available'";
        $stmt = $conn->prepare($room_query);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $room_result = $stmt->get_result();
        
        if ($room_result->num_rows === 0) {
            throw new Exception("Selected room is not available");
        }
        
        $room = $room_result->fetch_assoc();

        // Calculate total price
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $nights = $check_in_date->diff($check_out_date)->days;
        
        if ($nights <= 0) {
            throw new Exception("Invalid date range");
        }
        
        $total_price = $room['price_per_night'] * $nights;

        // Get user details for the reservation
        $user_query = "SELECT name, email FROM users WHERE id = ?";
        $stmt = $conn->prepare($user_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        $user = $user_result->fetch_assoc();

        // Insert reservation with user details
        $insert_query = "INSERT INTO reservations (user_id, room_id, check_in_date, check_out_date, 
                        guest_name, guest_email, guest_phone, total_price, special_requests, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, '', ?, ?, 'pending', NOW())";
        
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iissssds", $user_id, $room_id, $check_in, $check_out, 
                         $user['name'], $user['email'], $total_price, $special_requests);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to create reservation: " . $stmt->error);
        }

        // Don't update room status to 'occupied' immediately - only when confirmed
        // The room should remain 'available' until the booking is confirmed by admin

        $conn->commit();
        $_SESSION['success'] = "Room booked successfully! Your reservation is pending confirmation. You will be notified once confirmed.";
        header('Location: dashboard.php');
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
        header('Location: room-booking.php');
        exit();
    }
} else {
    header('Location: room-booking.php');
    exit();
}
?>