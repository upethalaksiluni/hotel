<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No reservation ID provided']);
    exit();
}

$reservation_id = $_GET['id'];

try {
    // Get reservation details
    $query = "SELECT r.*, rm.room_number, rm.room_type, u.name as user_name 
              FROM reservations r 
              LEFT JOIN rooms rm ON r.room_id = rm.id 
              LEFT JOIN users u ON r.user_id = u.id 
              WHERE r.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();
    
    if (!$reservation) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Reservation not found']);
        exit();
    }
    
    $response = [
        'success' => true,
        'reservation' => $reservation
    ];
    
    // If edit mode, also get available rooms
    if (isset($_GET['edit'])) {
        $rooms_query = "SELECT id, room_number, room_type, price_per_night 
                       FROM rooms 
                       WHERE status = 'available' OR id = ?
                       ORDER BY room_number";
        $rooms_stmt = $conn->prepare($rooms_query);
        $rooms_stmt->bind_param("i", $reservation['room_id']);
        $rooms_stmt->execute();
        $rooms_result = $rooms_stmt->get_result();
        
        $rooms = [];
        while ($room = $rooms_result->fetch_assoc()) {
            $rooms[] = $room;
        }
        
        $response['rooms'] = $rooms;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>