<?php
session_start();
require_once '../config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = $_SESSION['user_id'];
        $room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
        $check_in = isset($_POST['check_in']) ? $_POST['check_in'] : '';
        $check_out = isset($_POST['check_out']) ? $_POST['check_out'] : '';
        $special_requests = isset($_POST['special_requests']) ? $_POST['special_requests'] : '';

        // Validate input
        if (!$room_id || !$check_in || !$check_out) {
            throw new Exception("Missing required fields.");
        }

        // Get user details
        $user_query = "SELECT name, email FROM users WHERE id = ?";
        $stmt = $conn->prepare($user_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();
        $user = $user_result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            throw new Exception("User not found");
        }

        // Get room price
        $room_query = "SELECT price_per_night FROM rooms WHERE id = ?";
        $stmt = $conn->prepare($room_query);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $room_result = $stmt->get_result();
        $room = $room_result->fetch_assoc();
        $stmt->close();

        if (!$room) {
            throw new Exception("Room not found");
        }

        // Calculate total price
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $nights = $check_in_date->diff($check_out_date)->days;
        if ($nights < 1) {
            throw new Exception("Check-out date must be after check-in date.");
        }
        $total_price = $nights * $room['price_per_night'];

        // Check room availability for the selected dates
        $availability_query = "SELECT COUNT(*) as count FROM reservations 
            WHERE room_id = ? 
            AND status IN ('pending', 'confirmed')
            AND NOT (check_out_date <= ? OR check_in_date >= ?)";
        $stmt = $conn->prepare($availability_query);
        $stmt->bind_param("iss", $room_id, $check_in, $check_out);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row['count'] > 0) {
            throw new Exception("Room is not available for the selected dates.");
        }

        // Insert reservation
        $insert_query = "INSERT INTO reservations (user_id, room_id, check_in_date, check_out_date, 
                        guest_name, guest_email, guest_phone, total_price, special_requests, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, '', ?, ?, 'pending', NOW())";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iisssds", $user_id, $room_id, $check_in, $check_out, 
                         $user['name'], $user['email'], $total_price, $special_requests);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Reservation successful
            $_SESSION['booking_success'] = true;
            $_SESSION['booking_room_type'] = $room_id;
            $_SESSION['booking_check_in'] = $check_in;
            $_SESSION['booking_check_out'] = $check_out;
            $_SESSION['booking_total_price'] = $total_price;
            header("Location: booking_confirmation.php");
            exit();
        } else {
            throw new Exception("Error in making reservation. Please try again.");
        }
    } catch (Exception $e) {
        $_SESSION['booking_error'] = $e->getMessage();
        header("Location: room-booking.php");
        exit();
    }
} else {
    header("Location: room-booking.php");
    exit();
}
?>