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
    
    // Get room data with facilities
    $stmt = $conn->prepare("
        SELECT r.*, 
               GROUP_CONCAT(DISTINCT rfm.facility_id) as facility_ids,
               GROUP_CONCAT(DISTINCT rf.name) as facility_names
        FROM rooms r 
        LEFT JOIN room_facility_mapping rfm ON r.id = rfm.room_id 
        LEFT JOIN room_facilities rf ON rfm.facility_id = rf.id
        WHERE r.id = ?
        GROUP BY r.id
    ");
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $room_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    
    if (!$room) {
        throw new Exception('Room not found');
    }
    
    // Convert facilities string to array of integers
    $room['facilities'] = $room['facility_ids'] ? array_map('intval', explode(',', $room['facility_ids'])) : [];
    $room['facility_names'] = $room['facility_names'] ? explode(',', $room['facility_names']) : [];
    
    // Remove the facility_ids key as it's not needed in response
    unset($room['facility_ids']);
    
    echo json_encode(['success' => true, 'data' => $room]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>