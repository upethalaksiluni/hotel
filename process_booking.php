<?php
// Get user details for the reservation
$user_query = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    throw new Exception("User not found");
}

// Insert reservation with user details (leave guest_phone empty)
$insert_query = "INSERT INTO reservations (user_id, room_id, check_in_date, check_out_date, 
                guest_name, guest_email, guest_phone, total_price, special_requests, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, '', ?, ?, 'pending', NOW())";

$stmt = $conn->prepare($insert_query);
$stmt->bind_param("iisssds", $user_id, $room_id, $check_in, $check_out, 
                 $user['name'], $user['email'], $total_price, $special_requests);