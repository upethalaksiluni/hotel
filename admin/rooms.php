<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle room deletion
if (isset($_POST['delete_room'])) {
    $room_id = $_POST['room_id'];
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
}

// Handle room status update
if (isset($_POST['update_status'])) {
    $room_id = $_POST['room_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE rooms SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $room_id);
    $stmt->execute();
}

// Get all rooms with their facilities
$rooms_query = "SELECT r.*, GROUP_CONCAT(f.name) as facilities 
                FROM rooms r 
                LEFT JOIN room_facility_mapping rfm ON r.id = rfm.room_id 
                LEFT JOIN room_facilities f ON rfm.facility_id = f.id 
                GROUP BY r.id";
$rooms_result = $conn->query($rooms_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management - The Royal Grand Colombo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>The Royal Grand Colombo Admin</h3>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="index.html"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="active">
                    <a href="rooms.php"><i class="fas fa-bed"></i> Rooms</a>
                </li>
                <li>
                    <a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a>
                </li>
                <li>
                    <a href="facilities.php"><i class="fas fa-concierge-bell"></i> Facilities</a>
                </li>
                <li>
                    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Room Management</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                        <i class="fas fa-plus"></i> Add New Room
                    </button>
                </div>

                <!-- Rooms Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Room Number</th>
                                        <th>Type</th>
                                        <th>Floor</th>
                                        <th>Capacity</th>
                                        <th>Price/Night</th>
                                        <th>Status</th>
                                        <th>Facilities</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($room = $rooms_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                                        <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                                        <td><?php echo htmlspecialchars($room['floor_number']); ?></td>
                                        <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                                        <td>$<?php echo number_format($room['price_per_night'], 2); ?></td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="available" <?php echo $room['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                                                    <option value="occupied" <?php echo $room['status'] == 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                                                    <option value="maintenance" <?php echo $room['status'] == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($room['facilities']); ?></small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="editRoom(<?php echo $room['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                                <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                                                <button type="submit" name="delete_room" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Room Modal -->
    <div class="modal fade" id="addRoomModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addRoomForm" action="process_room.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Room Number</label>
                            <input type="text" class="form-control" name="room_number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Room Type</label>
                            <input type="text" class="form-control" name="room_type" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Floor Number</label>
                            <input type="number" class="form-control" name="floor_number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="number" class="form-control" name="capacity" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price per Night</label>
                            <input type="number" step="0.01" class="form-control" name="price_per_night" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Facilities</label>
                            <div class="row">
                                <?php
                                $facilities_query = "SELECT * FROM room_facilities";
                                $facilities_result = $conn->query($facilities_query);
                                while ($facility = $facilities_result->fetch_assoc()):
                                ?>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]" value="<?php echo $facility['id']; ?>">
                                        <label class="form-check-label">
                                            <i class="<?php echo $facility['icon']; ?>"></i>
                                            <?php echo htmlspecialchars($facility['name']); ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Room</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin.js"></script>
    <script>
        function editRoom(roomId) {
            // Implement edit room functionality
            alert('Edit room ' + roomId);
        }
    </script>
</body>
</html>