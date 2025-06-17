<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

try {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        throw new Exception('Unauthorized access');
    }

    if (!isset($_GET['id'])) {
        throw new Exception('Room ID is required');
    }

    $room_id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("SELECT r.*, GROUP_CONCAT(rfm.facility_id) as facilities 
                           FROM rooms r 
                           LEFT JOIN room_facility_mapping rfm ON r.id = rfm.room_id 
                           WHERE r.id = ?
                           GROUP BY r.id");
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement');
    }
    
    $stmt->bind_param("i", $room_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query');
    }
    
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    
    if (!$room) {
        throw new Exception('Room not found');
    }
    
    // Convert facilities string to array of integers
    $room['facilities'] = $room['facilities'] ? array_map('intval', explode(',', $room['facilities'])) : [];
    
    echo json_encode(['success' => true, 'data' => $room]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}