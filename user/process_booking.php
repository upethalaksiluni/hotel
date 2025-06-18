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

    // Validate dates
    if (empty($check_in) || empty($check_out) || strtotime($check_out) <= strtotime($check_in)) {
        $_SESSION['error'] = "Invalid check-in or check-out date.";
        header('Location: room-booking.php');
        exit();
    }

    // Check room availability
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM reservations WHERE room_id = ? AND status IN ('pending','confirmed') AND NOT (check_out_date <= ? OR check_in_date >= ?)");
    $stmt->bind_param("iss", $room_id, $check_in, $check_out);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result['count'] > 0) {
        $_SESSION['error'] = "Room is not available for the selected dates.";
        header('Location: room-booking.php');
        exit();
    }

    // Get price per night
    $stmt = $conn->prepare("SELECT price_per_night FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $room = $stmt->get_result()->fetch_assoc();
    $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
    $total_price = $room['price_per_night'] * $nights;

    // Insert reservation
    $stmt = $conn->prepare("INSERT INTO reservations (room_id, user_id, check_in_date, check_out_date, total_price, status, special_requests) VALUES (?, ?, ?, ?, ?, 'pending', ?)");
    $stmt->bind_param("iissds", $room_id, $user_id, $check_in, $check_out, $total_price, $special_requests);
    $stmt->execute();

    $_SESSION['success'] = "Room booked successfully!";
    header('Location: dashboard.php');
    exit();
} else {
    header('Location: room-booking.php');
    exit();
}
?>