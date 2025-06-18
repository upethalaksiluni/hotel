<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

try {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        throw new Exception('Unauthorized access');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['user_id'])) {
        throw new Exception('User ID is required');
    }

    $user_id = (int)$_POST['user_id'];
    
    // Prevent deleting self
    if ($user_id === $_SESSION['user_id']) {
        throw new Exception('Cannot delete your own account');
    }
    
    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete user's reservations first
        $stmt = $conn->prepare("DELETE FROM reservations WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Delete user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete user');
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
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