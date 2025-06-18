<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

try {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        throw new Exception('Unauthorized access');
    }

    if (!isset($_GET['id'])) {
        throw new Exception('User ID is required');
    }

    $user_id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Failed to prepare statement');
    }
    
    $stmt->bind_param("i", $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query');
    }
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        throw new Exception('User not found');
    }
    
    echo json_encode(['success' => true, 'user' => $user]);
    
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