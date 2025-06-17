<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                // Add new room
                $room_number = $_POST['room_number'];
                $room_type = $_POST['room_type'];
                $floor_number = $_POST['floor_number'];
                $capacity = $_POST['capacity'];
                $price_per_night = $_POST['price_per_night'];
                $description = $_POST['description'];
                $facilities = isset($_POST['facilities']) ? $_POST['facilities'] : [];

                // Start transaction
                $conn->begin_transaction();

                try {
                    // Insert room
                    $stmt = $conn->prepare("INSERT INTO rooms (room_number, room_type, floor_number, capacity, price_per_night, description) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssiids", $room_number, $room_type, $floor_number, $capacity, $price_per_night, $description);
                    $stmt->execute();
                    $room_id = $conn->insert_id;

                    // Insert room facilities
                    if (!empty($facilities)) {
                        $stmt = $conn->prepare("INSERT INTO room_facility_mapping (room_id, facility_id) VALUES (?, ?)");
                        foreach ($facilities as $facility_id) {
                            $stmt->bind_param("ii", $room_id, $facility_id);
                            $stmt->execute();
                        }
                    }

                    $conn->commit();
                    header('Location: rooms.php?success=1');
                } catch (Exception $e) {
                    $conn->rollback();
                    header('Location: rooms.php?error=' . urlencode($e->getMessage()));
                }
                break;

            case 'edit':
                // Edit existing room
                $room_id = $_POST['room_id'];
                $room_number = $_POST['room_number'];
                $room_type = $_POST['room_type'];
                $floor_number = $_POST['floor_number'];
                $capacity = $_POST['capacity'];
                $price_per_night = $_POST['price_per_night'];
                $description = $_POST['description'];
                $facilities = isset($_POST['facilities']) ? $_POST['facilities'] : [];

                // Start transaction
                $conn->begin_transaction();

                try {
                    // Update room
                    $stmt = $conn->prepare("UPDATE rooms SET room_number = ?, room_type = ?, floor_number = ?, capacity = ?, price_per_night = ?, description = ? WHERE id = ?");
                    $stmt->bind_param("ssiidsi", $room_number, $room_type, $floor_number, $capacity, $price_per_night, $description, $room_id);
                    $stmt->execute();

                    // Delete existing facilities
                    $stmt = $conn->prepare("DELETE FROM room_facility_mapping WHERE room_id = ?");
                    $stmt->bind_param("i", $room_id);
                    $stmt->execute();

                    // Insert new facilities
                    if (!empty($facilities)) {
                        $stmt = $conn->prepare("INSERT INTO room_facility_mapping (room_id, facility_id) VALUES (?, ?)");
                        foreach ($facilities as $facility_id) {
                            $stmt->bind_param("ii", $room_id, $facility_id);
                            $stmt->execute();
                        }
                    }

                    $conn->commit();
                    header('Location: rooms.php?success=2');
                } catch (Exception $e) {
                    $conn->rollback();
                    header('Location: rooms.php?error=' . urlencode($e->getMessage()));
                }
                break;
        }
    }
}

$conn->close();
?> 