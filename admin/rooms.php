<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Fetch all rooms with their facilities
$rooms_query = "
    SELECT r.*, 
           GROUP_CONCAT(DISTINCT rf.name ORDER BY rf.name SEPARATOR ', ') as facility_names,
           GROUP_CONCAT(DISTINCT rf.id ORDER BY rf.id) as facility_ids
    FROM rooms r 
    LEFT JOIN room_facility_mapping rfm ON r.id = rfm.room_id 
    LEFT JOIN room_facilities rf ON rfm.facility_id = rf.id
    GROUP BY r.id
    ORDER BY r.floor_number, r.room_number
";

$rooms_result = $conn->query($rooms_query);

// Fetch all available facilities for the form
$facilities_query = "SELECT id, name, description, icon FROM room_facilities ORDER BY name";
$facilities_result = $conn->query($facilities_query);
$facilities = [];
while ($facility = $facilities_result->fetch_assoc()) {
    $facilities[] = $facility;
}

// Count rooms by status
$stats_query = "SELECT status, COUNT(*) as count FROM rooms GROUP BY status";
$stats_result = $conn->query($stats_query);
$stats = ['available' => 0, 'occupied' => 0, 'maintenance' => 0];
while ($stat = $stats_result->fetch_assoc()) {
    $stats[$stat['status']] = $stat['count'];
}
$total_rooms = array_sum($stats);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management - The Royal Grand Colombo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        #sidebar {
            background-color: #1f2937 !important;
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        #sidebar.hidden {
            margin-left: -250px;
        }

        .sidebar-header {
            background-color: #111827 !important;
            padding: 20px;
            border-bottom: 1px solid #374151;
        }

        .sidebar-header h3 {
            color: white !important;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }

        .sidebar-nav {
            flex-grow: 1;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .sidebar-nav li {
            border-bottom: 1px solid #374151;
        }

        .sidebar-nav li a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: white !important;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .sidebar-nav li a:hover {
            background-color: #374151 !important;
            color: #60a5fa !important;
            transform: translateX(5px);
        }

        .sidebar-nav li a.active {
            background-color: #1d4ed8 !important;
            color: white !important;
            border-right: 4px solid #3b82f6;
        }

        .sidebar-nav li a i {
            width: 20px;
            margin-right: 15px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar-footer {
            border-top: 1px solid #374151;
            margin-top: auto;
        }

        .sidebar-footer a {
            background-color: #dc2626 !important;
        }

        .sidebar-footer a:hover {
            background-color: #b91c1c !important;
        }

        /* Content Styles */
        #content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 20px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        #content.expanded {
            margin-left: 0;
            width: 100%;
        }

        /* Mobile Toggle Button */
        #sidebarToggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1100;
            background-color: #1f2937;
            color: white;
            border: none;
            padding: 10px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #sidebarToggle:hover {
            background-color: #374151;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }

            #sidebar.show {
                margin-left: 0;
            }

            #content {
                margin-left: 0;
                width: 100%;
            }

            #sidebarToggle {
                display: block;
            }

            body.sidebar-open {
                overflow: hidden;
            }

            body.sidebar-open::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }
        }

        @media (min-width: 769px) {
            #sidebarToggle {
                display: none !important;
            }
        }

        /* Custom Card Styles */
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .card-header {
            background-color: #1f2937;
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }

        .table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border: none;
        }

        .table td {
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .btn-primary {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .btn-primary:hover {
            background-color: #1e40af;
            border-color: #1e40af;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-available {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-occupied {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-maintenance {
            background-color: #fef3c7;
            color: #92400e;
        }

        .loading {
            display: none;
        }

        .loading.show {
            display: inline-block;
        }
    </style>
</head>

<body>
    <!-- Mobile Toggle Button -->
    <button id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>The Royal Grand Colombo Admin</h3>
            </div>

            <!-- Update the sidebar navigation -->
            <ul class="sidebar-nav">
                <li>
                    <a href="index.php">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="rooms.php" class="active">
                        <i class="fas fa-bed"></i>
                        <span>Rooms</span>
                    </a>
                </li>
                <li>
                    <a href="reservations.php">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Reservations</span>
                    </a>
                </li>
                <li>
                    <a href="facilities.php">
                        <i class="fas fa-concierge-bell"></i>
                        <span>Facilities</span>
                    </a>
                </li>
                <li>
                    <a href="users.php">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="mt-auto border-t border-gray-800">
                    <a href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <div class="container-fluid">
                <!-- Success/Error Messages -->
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

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">Room Management</h2>
                        <p class="text-muted mb-0">Manage hotel rooms, availability, and pricing</p>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                        <i class="fas fa-plus me-2"></i>Add New Room
                    </button>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-bed fa-2x text-primary mb-2"></i>
                                <h5 class="card-title">Total Rooms</h5>
                                <h3 class="text-primary"><?php echo $total_rooms; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h5 class="card-title">Available</h5>
                                <h3 class="text-success"><?php echo $stats['available']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-user-check fa-2x text-warning mb-2"></i>
                                <h5 class="card-title">Occupied</h5>
                                <h3 class="text-warning"><?php echo $stats['occupied']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-tools fa-2x text-danger mb-2"></i>
                                <h5 class="card-title">Maintenance</h5>
                                <h3 class="text-danger"><?php echo $stats['maintenance']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rooms Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>All Rooms
                        </h5>
                    </div>
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
                                        <td><strong><?php echo htmlspecialchars($room['room_number']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                                        <td><?php echo $room['floor_number']; ?><?php echo $room['floor_number'] == 1 ? 'st' : ($room['floor_number'] == 2 ? 'nd' : ($room['floor_number'] == 3 ? 'rd' : 'th')); ?> Floor</td>
                                        <td><?php echo $room['capacity']; ?> Guests</td>
                                        <td><strong>$<?php echo number_format($room['price_per_night'], 2); ?></strong></td>
                                        <td>
                                            <select class="form-select form-select-sm status-select" data-room-id="<?php echo $room['id']; ?>">
                                                <option value="available" <?php echo $room['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                                                <option value="occupied" <?php echo $room['status'] == 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                                                <option value="maintenance" <?php echo $room['status'] == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                            </select>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?php echo htmlspecialchars($room['facility_names'] ?: 'No facilities'); ?></small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info me-1" onclick="editRoom(<?php echo $room['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteRoom(<?php echo $room['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="process_room.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Room Number</label>
                                    <input type="text" class="form-control" name="room_number" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Room Type</label>
                                    <select class="form-control" name="room_type" required>
                                        <option value="">Select Room Type</option>
                                        <option value="Deluxe Lake View">Deluxe Lake View</option>
                                        <option value="Premier Ocean View">Premier Ocean View</option>
                                        <option value="Executive Suite">Executive Suite</option>
                                        <option value="Standard Room">Standard Room</option>
                                        <option value="Presidential Suite">Presidential Suite</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Floor Number</label>
                                    <input type="number" class="form-control" name="floor_number" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Capacity</label>
                                    <input type="number" class="form-control" name="capacity" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Price per Night</label>
                                    <input type="number" step="0.01" class="form-control" name="price_per_night" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Facilities</label>
                            <div class="row">
                                <?php foreach ($facilities as $facility): ?>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]" value="<?php echo $facility['id']; ?>">
                                        <label class="form-check-label">
                                            <i class="<?php echo $facility['icon'] ?: 'fas fa-check'; ?>"></i> <?php echo htmlspecialchars($facility['name']); ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Room Image</label>
                            <input type="file" class="form-control" name="room_image" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="loading spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Add Room
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Room Modal -->
    <div class="modal fade" id="editRoomModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="process_room.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="room_id" id="edit_room_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Room Number</label>
                                    <input type="text" class="form-control" name="room_number" id="edit_room_number" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Room Type</label>
                                    <select class="form-control" name="room_type" id="edit_room_type" required>
                                        <option value="Deluxe Lake View">Deluxe Lake View</option>
                                        <option value="Premier Ocean View">Premier Ocean View</option>
                                        <option value="Executive Suite">Executive Suite</option>
                                        <option value="Standard Room">Standard Room</option>
                                        <option value="Presidential Suite">Presidential Suite</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Floor Number</label>
                                    <input type="number" class="form-control" name="floor_number" id="edit_floor_number" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Capacity</label>
                                    <input type="number" class="form-control" name="capacity" id="edit_capacity" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Price per Night</label>
                                    <input type="number" step="0.01" class="form-control" name="price_per_night" id="edit_price_per_night" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Facilities</label>
                            <div class="row" id="edit_facilities_container">
                                <?php foreach ($facilities as $facility): ?>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input edit-facility-checkbox" type="checkbox" name="facilities[]" value="<?php echo $facility['id']; ?>" id="edit_facility_<?php echo $facility['id']; ?>">
                                        <label class="form-check-label" for="edit_facility_<?php echo $facility['id']; ?>">
                                            <i class="<?php echo $facility['icon'] ?: 'fas fa-check'; ?>"></i> <?php echo htmlspecialchars($facility['name']); ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Room Image</label>
                            <input type="file" class="form-control" name="room_image" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="loading spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Update Room
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteRoomModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this room? This action cannot be undone.</p>
                    <p><strong>Room: <span id="delete_room_info"></span></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Room</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
function editRoom(roomId) {
    fetch(`get_room.php?id=${roomId}`)
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                const room = res.data;
                document.getElementById('edit_room_id').value = room.id;
                document.getElementById('edit_room_number').value = room.room_number;
                document.getElementById('edit_room_type').value = room.room_type;
                document.getElementById('edit_floor_number').value = room.floor_number;
                document.getElementById('edit_capacity').value = room.capacity;
                document.getElementById('edit_price_per_night').value = room.price_per_night;
                document.getElementById('edit_description').value = room.description;

                // Uncheck all first
                document.querySelectorAll('.edit-facility-checkbox').forEach(cb => cb.checked = false);
                // Then check only the facilities this room has
                room.facilities.forEach(id => {
                    const checkbox = document.getElementById(`edit_facility_${id}`);
                    if (checkbox) checkbox.checked = true;
                });

                new bootstrap.Modal(document.getElementById('editRoomModal')).show();
            } else {
                alert("Failed to fetch room data: " + res.error);
            }
        })
        .catch(err => {
            console.error(err);
            alert("An error occurred while fetching room data.");
        });
}

function deleteRoom(roomId) {
    if (confirm("Are you sure you want to delete this room? This action cannot be undone.")) {
        fetch('delete_room.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `room_id=${roomId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Reload to reflect deletion
            } else {
                alert("Error: " + data.error);
            }
        })
        .catch(error => {
            console.error("Delete failed:", error);
            alert("An error occurred while deleting the room.");
        });
    }
}


</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
