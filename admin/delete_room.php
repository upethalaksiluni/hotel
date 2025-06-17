<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        throw new Exception('Unauthorized access');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
    if ($room_id <= 0) {
        throw new Exception('Invalid room ID');
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete facilities mapping
        $stmt = $conn->prepare("DELETE FROM room_facility_mapping WHERE room_id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare facility mapping deletion');
        }
        $stmt->bind_param("i", $room_id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete facility mappings');
        }

        // Delete room
        $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare room deletion');
        }
        $stmt->bind_param("i", $room_id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete room');
        }

        // If we get here, commit the transaction
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Room deleted successfully']);
    } catch (Exception $e) {
        // Rollback on any error
        $conn->rollback();
        throw $e;
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    // Close statement and connection
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();

    }}

?>