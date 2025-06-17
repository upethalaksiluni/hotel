<?php
session_start();
require_once '../config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    // Validate required fields
                    $required_fields = ['room_number', 'room_type', 'floor_number', 'capacity', 'price_per_night'];
                    foreach ($required_fields as $field) {
                        if (empty($_POST[$field])) {
                            throw new Exception("$field is required");
                        }
                    }

                    // Sanitize input
                    $room_number = trim($_POST['room_number']);
                    $room_type = trim($_POST['room_type']);
                    $floor_number = (int)$_POST['floor_number'];
                    $capacity = (int)$_POST['capacity'];
                    $price_per_night = (float)$_POST['price_per_night'];
                    $description = trim($_POST['description'] ?? '');
                    $facilities = isset($_POST['facilities']) ? $_POST['facilities'] : [];

                    // Start transaction
                    $conn->begin_transaction();

                    // Check if room number already exists
                    $check_stmt = $conn->prepare("SELECT id FROM rooms WHERE room_number = ?");
                    $check_stmt->bind_param("s", $room_number);
                    $check_stmt->execute();
                    if ($check_stmt->get_result()->num_rows > 0) {
                        throw new Exception("Room number already exists");
                    }

                    // Insert room
                    $stmt = $conn->prepare("INSERT INTO rooms (room_number, room_type, floor_number, capacity, price_per_night, description) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssiids", $room_number, $room_type, $floor_number, $capacity, $price_per_night, $description);
                    if (!$stmt->execute()) {
                        throw new Exception("Error inserting room: " . $stmt->error);
                    }
                    $room_id = $conn->insert_id;

                    // Insert facilities
                    if (!empty($facilities)) {
                        $facility_stmt = $conn->prepare("INSERT INTO room_facility_mapping (room_id, facility_id) VALUES (?, ?)");
                        foreach ($facilities as $facility_id) {
                            $facility_stmt->bind_param("ii", $room_id, $facility_id);
                            if (!$facility_stmt->execute()) {
                                throw new Exception("Error adding facilities");
                            }
                        }
                    }

                    $conn->commit();
                    $_SESSION['success'] = "Room added successfully";
                    header('Location: rooms.php');
                    exit();
                    break;

                case 'edit':
                    // Validate required fields
                    $required_fields = ['room_id', 'room_number', 'room_type', 'floor_number', 'capacity', 'price_per_night'];
                    foreach ($required_fields as $field) {
                        if (empty($_POST[$field])) {
                            throw new Exception("$field is required");
                        }
                    }

                    $room_id = (int)$_POST['room_id'];
                    $room_number = trim($_POST['room_number']);
                    $room_type = trim($_POST['room_type']);
                    $floor_number = (int)$_POST['floor_number'];
                    $capacity = (int)$_POST['capacity'];
                    $price_per_night = (float)$_POST['price_per_night'];
                    $description = trim($_POST['description'] ?? '');
                    $facilities = isset($_POST['facilities']) ? $_POST['facilities'] : [];

                    // Start transaction
                    $conn->begin_transaction();

                    try {
                        // Check if room number exists for other rooms
                        $check_stmt = $conn->prepare("SELECT id FROM rooms WHERE room_number = ? AND id != ?");
                        $check_stmt->bind_param("si", $room_number, $room_id);
                        $check_stmt->execute();
                        if ($check_stmt->get_result()->num_rows > 0) {
                            throw new Exception("Room number already exists");
                        }

                        // Update room
                        $stmt = $conn->prepare("UPDATE rooms SET room_number=?, room_type=?, floor_number=?, 
                                              capacity=?, price_per_night=?, description=? WHERE id=?");
                        $stmt->bind_param("ssiidsi", $room_number, $room_type, $floor_number, 
                                         $capacity, $price_per_night, $description, $room_id);
                        if (!$stmt->execute()) {
                            throw new Exception("Error updating room: " . $stmt->error);
                        }

                        // Delete existing facilities
                        $stmt = $conn->prepare("DELETE FROM room_facility_mapping WHERE room_id = ?");
                        $stmt->bind_param("i", $room_id);
                        $stmt->execute();

                        // Insert new facilities
                        if (!empty($facilities)) {
                            $stmt = $conn->prepare("INSERT INTO room_facility_mapping (room_id, facility_id) VALUES (?, ?)");
                            foreach ($facilities as $facility_id) {
                                $stmt->bind_param("ii", $room_id, $facility_id);
                                if (!$stmt->execute()) {
                                    throw new Exception("Error updating facilities");
                                }
                            }
                        }

                        // Handle room image upload
                        if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === 0) {
                            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                            $filename = $_FILES['room_image']['name'];
                            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            
                            if (in_array($ext, $allowed)) {
                                $new_filename = strtolower(str_replace(' ', '-', $room_type)) . '.' . $ext;
                                $upload_path = '../images/' . $new_filename;
                                
                                if (move_uploaded_file($_FILES['room_image']['tmp_name'], $upload_path)) {
                                    // Image uploaded successfully
                                    // You might want to store the image path in the database
                                } else {
                                    throw new Exception("Failed to upload image");
                                }
                            } else {
                                throw new Exception("Invalid file type");
                            }
                        }

                        $conn->commit();
                        $_SESSION['success'] = "Room updated successfully";
                        header('Location: rooms.php');
                        exit();
                    } catch (Exception $e) {
                        $conn->rollback();
                        $_SESSION['error'] = $e->getMessage();
                        header('Location: rooms.php');
                        exit();
                    }
                    break;
            }
        }
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
        header('Location: rooms.php');
        exit();
    }
}

$conn->close();
header('Location: rooms.php');
exit();
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php 
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php 
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>