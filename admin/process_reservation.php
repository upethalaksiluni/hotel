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
                // Add new reservation
                $room_id = $_POST['room_id'];
                $guest_name = $_POST['guest_name'];
                $guest_email = $_POST['guest_email'];
                $guest_phone = $_POST['guest_phone'];
                $check_in_date = $_POST['check_in_date'];
                $check_out_date = $_POST['check_out_date'];
                $special_requests = $_POST['special_requests'];

                // Calculate total price
                $stmt = $conn->prepare("SELECT price_per_night FROM rooms WHERE id = ?");
                $stmt->bind_param("i", $room_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $room = $result->fetch_assoc();
                
                $check_in = new DateTime($check_in_date);
                $check_out = new DateTime($check_out_date);
                $nights = $check_in->diff($check_out)->days;
                $total_price = $room['price_per_night'] * $nights;

                // Start transaction
                $conn->begin_transaction();

                try {
                    // Insert reservation
                    $stmt = $conn->prepare("INSERT INTO reservations (room_id, user_id, check_in_date, check_out_date, guest_name, guest_email, guest_phone, total_price, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("iisssssds", $room_id, $_SESSION['user_id'], $check_in_date, $check_out_date, $guest_name, $guest_email, $guest_phone, $total_price, $special_requests);
                    $stmt->execute();

                    // Update room status
                    $stmt = $conn->prepare("UPDATE rooms SET status = 'occupied' WHERE id = ?");
                    $stmt->bind_param("i", $room_id);
                    $stmt->execute();

                    $conn->commit();
                    header('Location: reservations.php?success=1');
                } catch (Exception $e) {
                    $conn->rollback();
                    header('Location: reservations.php?error=' . urlencode($e->getMessage()));
                }
                break;

            case 'edit':
                // Edit existing reservation
                $reservation_id = $_POST['reservation_id'];
                $room_id = $_POST['room_id'];
                $guest_name = $_POST['guest_name'];
                $guest_email = $_POST['guest_email'];
                $guest_phone = $_POST['guest_phone'];
                $check_in_date = $_POST['check_in_date'];
                $check_out_date = $_POST['check_out_date'];
                $special_requests = $_POST['special_requests'];

                // Calculate total price
                $stmt = $conn->prepare("SELECT price_per_night FROM rooms WHERE id = ?");
                $stmt->bind_param("i", $room_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $room = $result->fetch_assoc();
                
                $check_in = new DateTime($check_in_date);
                $check_out = new DateTime($check_out_date);
                $nights = $check_in->diff($check_out)->days;
                $total_price = $room['price_per_night'] * $nights;

                // Start transaction
                $conn->begin_transaction();

                try {
                    // Get old room_id
                    $stmt = $conn->prepare("SELECT room_id FROM reservations WHERE id = ?");
                    $stmt->bind_param("i", $reservation_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $old_room = $result->fetch_assoc();

                    // Update reservation
                    $stmt = $conn->prepare("UPDATE reservations SET room_id = ?, check_in_date = ?, check_out_date = ?, guest_name = ?, guest_email = ?, guest_phone = ?, total_price = ?, special_requests = ? WHERE id = ?");
                    $stmt->bind_param("isssssdsi", $room_id, $check_in_date, $check_out_date, $guest_name, $guest_email, $guest_phone, $total_price, $special_requests, $reservation_id);
                    $stmt->execute();

                    // Update room statuses
                    if ($old_room['room_id'] != $room_id) {
                        // Set old room as available
                        $stmt = $conn->prepare("UPDATE rooms SET status = 'available' WHERE id = ?");
                        $stmt->bind_param("i", $old_room['room_id']);
                        $stmt->execute();

                        // Set new room as occupied
                        $stmt = $conn->prepare("UPDATE rooms SET status = 'occupied' WHERE id = ?");
                        $stmt->bind_param("i", $room_id);
                        $stmt->execute();
                    }

                    $conn->commit();
                    header('Location: reservations.php?success=2');
                } catch (Exception $e) {
                    $conn->rollback();
                    header('Location: reservations.php?error=' . urlencode($e->getMessage()));
                }
                break;
        }
    }
}

$conn->close();
?> 