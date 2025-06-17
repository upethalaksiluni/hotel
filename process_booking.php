<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_type = $_POST['room_type'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $user_id = $_SESSION['user_id'];

    // Get available room
    $room_query = "SELECT r.* FROM rooms r 
                  WHERE r.room_type = ? AND r.status = 'available'
                  AND NOT EXISTS (
                      SELECT 1 FROM reservations res 
                      WHERE res.room_id = r.id 
                      AND res.status != 'cancelled'
                      AND ((res.check_in_date <= ? AND res.check_out_date > ?) 
                      OR (res.check_in_date < ? AND res.check_out_date >= ?)
                      OR (res.check_in_date >= ? AND res.check_out_date <= ?))
                  )
                  LIMIT 1";
    
    $stmt = $conn->prepare($room_query);
    $stmt->bind_param("sssssss", $room_type, $check_out, $check_in, $check_out, $check_in, $check_in, $check_out);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $room = $result->fetch_assoc();
        
        // Calculate total price
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $nights = $check_in_date->diff($check_out_date)->days;
        $total_price = $room['price_per_night'] * $nights;

        // Insert reservation
        $insert_query = "INSERT INTO reservations (user_id, room_id, check_in_date, check_out_date, total_price, status, created_at, seen_by_admin) 
                        VALUES (?, ?, ?, ?, ?, 'pending', NOW(), 0)";
        
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iissd", $user_id, $room['id'], $check_in, $check_out, $total_price);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Your booking has been submitted successfully! We will confirm your reservation shortly.";
            header('Location: user/dashboard.php');
            exit();
        } else {
            $_SESSION['error_message'] = "There was an error processing your booking. Please try again.";
            header('Location: book.php?type=' . urlencode($room_type) . '&check_in=' . urlencode($check_in) . '&check_out=' . urlencode($check_out));
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Sorry, the room is no longer available for the selected dates.";
        header('Location: book.php?type=' . urlencode($room_type) . '&check_in=' . urlencode($check_in) . '&check_out=' . urlencode($check_out));
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?> 