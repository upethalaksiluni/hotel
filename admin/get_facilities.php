<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

try {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        throw new Exception('Unauthorized access');
    }

    if (!isset($_GET['id'])) {
        throw new Exception('Facility ID is required');
    }

    $facility_id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("SELECT * FROM room_facilities WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Failed to prepare statement');
    }
    
    $stmt->bind_param("i", $facility_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query');
    }
    
    $result = $stmt->get_result();
    $facility = $result->fetch_assoc();
    
    if (!$facility) {
        throw new Exception('Facility not found');
    }
    
    echo json_encode(['success' => true, 'facility' => $facility]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>