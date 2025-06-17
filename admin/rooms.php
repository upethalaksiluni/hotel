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
                    <a href="guests.php">
                        <i class="fas fa-users"></i>
                        <span>Guests</span>
                    </a>
                </li>
                <li>
                    <a href="reports.php">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <div class="container-fluid">
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
                                <h3 class="text-primary">45</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h5 class="card-title">Available</h5>
                                <h3 class="text-success">28</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-user-check fa-2x text-warning mb-2"></i>
                                <h5 class="card-title">Occupied</h5>
                                <h3 class="text-warning">15</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-tools fa-2x text-danger mb-2"></i>
                                <h5 class="card-title">Maintenance</h5>
                                <h3 class="text-danger">2</h3>
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
                                    <tr>
                                        <td><strong>101</strong></td>
                                        <td>Deluxe Lake View</td>
                                        <td>1st Floor</td>
                                        <td>2 Guests</td>
                                        <td><strong>$150.00</strong></td>
                                        <td>
                                            <select class="form-select form-select-sm">
                                                <option value="available" selected>Available</option>
                                                <option value="occupied">Occupied</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </td>
                                        <td>
                                            <small class="text-muted">WiFi, AC, TV, Mini Bar</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info me-1" onclick="editRoom(1)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteRoom(1)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>102</strong></td>
                                        <td>Premier Ocean View</td>
                                        <td>1st Floor</td>
                                        <td>3 Guests</td>
                                        <td><strong>$220.00</strong></td>
                                        <td>
                                            <select class="form-select form-select-sm">
                                                <option value="available">Available</option>
                                                <option value="occupied" selected>Occupied</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </td>
                                        <td>
                                            <small class="text-muted">WiFi, AC, TV, Mini Bar, Balcony</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info me-1" onclick="editRoom(2)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteRoom(2)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>201</strong></td>
                                        <td>Executive Suite</td>
                                        <td>2nd Floor</td>
                                        <td>4 Guests</td>
                                        <td><strong>$350.00</strong></td>
                                        <td>
                                            <select class="form-select form-select-sm">
                                                <option value="available" selected>Available</option>
                                                <option value="occupied">Occupied</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </td>
                                        <td>
                                            <small class="text-muted">WiFi, AC, TV, Mini Bar, Jacuzzi, Living Room</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info me-1" onclick="editRoom(3)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteRoom(3)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>202</strong></td>
                                        <td>Deluxe Lake View</td>
                                        <td>2nd Floor</td>
                                        <td>2 Guests</td>
                                        <td><strong>$160.00</strong></td>
                                        <td>
                                            <select class="form-select form-select-sm">
                                                <option value="available">Available</option>
                                                <option value="occupied">Occupied</option>
                                                <option value="maintenance" selected>Maintenance</option>
                                            </select>
                                        </td>
                                        <td>
                                            <small class="text-muted">WiFi, AC, TV, Mini Bar</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info me-1" onclick="editRoom(4)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteRoom(4)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
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
                <div class="modal-body">
                    <form id="addRoomForm">
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
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Floor Number</label>
                                    <input type="number" class="form-control" name="floor_number" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Capacity</label>
                                    <input type="number" class="form-control" name="capacity" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Price per Night</label>
                                    <input type="number" step="0.01" class="form-control" name="price_per_night" required>
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
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]" value="wifi">
                                        <label class="form-check-label">
                                            <i class="fas fa-wifi"></i> WiFi
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]" value="ac">
                                        <label class="form-check-label">
                                            <i class="fas fa-snowflake"></i> Air Conditioning
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]" value="tv">
                                        <label class="form-check-label">
                                            <i class="fas fa-tv"></i> Television
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]" value="minibar">
                                        <label class="form-check-label">
                                            <i class="fas fa-glass-martini-alt"></i> Mini Bar
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]" value="balcony">
                                        <label class="form-check-label">
                                            <i class="fas fa-door-open"></i> Balcony
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]" value="jacuzzi">
                                        <label class="form-check-label">
                                            <i class="fas fa-hot-tub"></i> Jacuzzi
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addRoom()">Add Room</button>
                </div>
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
                <div class="modal-body">
                    <form id="editRoomForm">
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
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Floor Number</label>
                                    <input type="number" class="form-control" name="floor_number" id="edit_floor_number" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Capacity</label>
                                    <input type="number" class="form-control" name="capacity" id="edit_capacity" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Price per Night</label>
                                    <input type="number" step="0.01" class="form-control" name="price_per_night" id="edit_price_per_night" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Room Image</label>
                            <input type="file" class="form-control" name="room_image" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateRoom()">Update Room</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const body = document.body;

            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('show');
                    body.classList.toggle('sidebar-open');
                } else {
                    sidebar.classList.toggle('hidden');
                    content.classList.toggle('expanded');
                }
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                        body.classList.remove('sidebar-open');
                    }
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    body.classList.remove('sidebar-open');
                }
            });
        });

        // Room Management Functions
        function editRoom(roomId) {
            // Sample data - in real application, fetch from server
            const sampleData = {
                1: { room_number: '101', room_type: 'Deluxe Lake View', floor_number: 1, capacity: 2, price_per_night: 150.00, description: 'Beautiful lake view room with modern amenities.' },
                2: { room_number: '102', room_type: 'Premier Ocean View', floor_number: 1, capacity: 3, price_per_night: 220.00, description: 'Stunning ocean view with premium facilities.' },
                3: { room_number: '201', room_type: 'Executive Suite', floor_number: 2, capacity: 4, price_per_night: 350.00, description: 'Luxurious suite with separate living area.' },
                4: { room_number: '202', room_type: 'Deluxe Lake View', floor_number: 2, capacity: 2, price_per_night: 160.00, description: 'Comfortable room with lake view.' }
            };

            const room = sampleData[roomId];
            if (room) {
                document.getElementById('edit_room_id').value = roomId;
                document.getElementById('edit_room_number').value = room.room_number;
                document.getElementById('edit_room_type').value = room.room_type;
                document.getElementById('edit_floor_number').value = room.floor_number;
                document.getElementById('edit_capacity').value = room.capacity;
                document.getElementById('edit_price_per_night').value = room.price_per_night;
                document.getElementById('edit_description').value = room.description;

                const editModal = new bootstrap.Modal(document.getElementById('editRoomModal'));
                editModal.show();
            }
        }

        function deleteRoom(roomId) {
            if (confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
                // In real application, send delete request to server
                alert('Room deleted successfully!');
                // Reload the page or update the table
                location.reload();
            }
        }

        function addRoom() {
            const form = document.getElementById('addRoomForm');
            const formData = new FormData(form);
            
            // In real application, send data to server
            alert('Room added successfully!');
            
            // Close modal and reload page
            const addModal = bootstrap.Modal.getInstance(document.getElementById('addRoomModal'));
            addModal.hide();
            location.reload();
        }

        function updateRoom() {
            const form = document.getElementById('editRoomForm');
            const formData = new FormData(form);
            
            // In real application, send update request to server
            alert('Room updated successfully!');
            
            // Close modal and reload page
            const editModal = bootstrap.Modal.getInstance(document.getElementById('editRoomModal'));
            editModal.hide();
            location.reload();
        }

        // Status change handler
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('form-select') && e.target.name === 'status') {
                const roomId = e.target.getAttribute('data-room-id');
                const status = e.target.value;
                
                // In real application, send status update to server
                console.log(`Updating room ${roomId} status to ${status}`);
                
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'toast-notification';
                toast.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show position-fixed" 
                         style="top: 20px; right: 20px; z-index: 9999;">
                        <i class="fas fa-check-circle me-2"></i>
                        Room status updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                document.body.appendChild(toast);
                
                // Auto remove after 3 seconds
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        });

        // Search functionality
        function searchRooms() {
            const searchInput = document.getElementById('roomSearch');
            const searchTerm = searchInput.value.toLowerCase();
            const tableRows = document.querySelectorAll('#roomsTable tbody tr');
            
            tableRows.forEach(row => {
                const roomNumber = row.cells[0].textContent.toLowerCase();
                const roomType = row.cells[1].textContent.toLowerCase();
                const floor = row.cells[2].textContent.toLowerCase();
                
                if (roomNumber.includes(searchTerm) || 
                    roomType.includes(searchTerm) || 
                    floor.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Filter by status
        function filterByStatus(status) {
            const tableRows = document.querySelectorAll('#roomsTable tbody tr');
            
            tableRows.forEach(row => {
                const statusSelect = row.querySelector('select[name="status"]');
                const currentStatus = statusSelect.value;
                
                if (status === 'all' || currentStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Export functionality
        function exportRooms() {
            // In real application, generate and download CSV/Excel file
            alert('Exporting rooms data...');
        }

        // Print functionality
        function printRooms() {
            window.print();
        }

        // Bulk actions
        function selectAllRooms() {
            const checkboxes = document.querySelectorAll('input[name="room_select"]');
            const selectAll = document.getElementById('selectAll');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            
            updateBulkActions();
        }

        function updateBulkActions() {
            const checkedBoxes = document.querySelectorAll('input[name="room_select"]:checked');
            const bulkActions = document.getElementById('bulkActions');
            
            if (checkedBoxes.length > 0) {
                bulkActions.style.display = 'block';
            } else {
                bulkActions.style.display = 'none';
            }
        }

        function bulkStatusUpdate(status) {
            const checkedBoxes = document.querySelectorAll('input[name="room_select"]:checked');
            
            if (checkedBoxes.length === 0) {
                alert('Please select at least one room.');
                return;
            }
            
            if (confirm(`Are you sure you want to update ${checkedBoxes.length} room(s) to ${status} status?`)) {
                checkedBoxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const statusSelect = row.querySelector('select[name="status"]');
                    statusSelect.value = status;
                });
                
                alert(`${checkedBoxes.length} room(s) status updated to ${status}.`);
            }
        }

        // Advanced search modal
        function showAdvancedSearch() {
            const advancedSearchModal = new bootstrap.Modal(document.getElementById('advancedSearchModal'));
            advancedSearchModal.show();
        }

        function applyAdvancedSearch() {
            const form = document.getElementById('advancedSearchForm');
            const formData = new FormData(form);
            
            // In real application, apply advanced filters
            console.log('Advanced search filters:', Object.fromEntries(formData));
            
            const advancedSearchModal = bootstrap.Modal.getInstance(document.getElementById('advancedSearchModal'));
            advancedSearchModal.hide();
            
            alert('Advanced search filters applied!');
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => {
                new bootstrap.Tooltip(tooltip);
            });
        });

        // Real-time updates (simulate with WebSocket)
        function simulateRealTimeUpdates() {
            setInterval(() => {
                // Simulate random status changes
                const statuses = ['available', 'occupied', 'maintenance'];
                const randomStatus = statuses[Math.floor(Math.random() * statuses.length)];
                const randomRow = Math.floor(Math.random() * 4) + 1;
                
                const statusSelects = document.querySelectorAll('select[name="status"]');
                if (statusSelects[randomRow - 1]) {
                    statusSelects[randomRow - 1].value = randomStatus;
                    
                    // Update stats
                    updateStats();
                }
            }, 30000); // Update every 30 seconds
        }

        function updateStats() {
            const statusSelects = document.querySelectorAll('select[name="status"]');
            let available = 0, occupied = 0, maintenance = 0;
            
            statusSelects.forEach(select => {
                if (select.value === 'available') available++;
                else if (select.value === 'occupied') occupied++;
                else if (select.value === 'maintenance') maintenance++;
            });
            
            // Update stat cards (if they exist)
            const availableCard = document.querySelector('.text-success h3');
            const occupiedCard = document.querySelector('.text-warning h3');
            const maintenanceCard = document.querySelector('.text-danger h3');
            
            if (availableCard) availableCard.textContent = available;
            if (occupiedCard) occupiedCard.textContent = occupied;
            if (maintenanceCard) maintenanceCard.textContent = maintenance;
        }

        // Start real-time updates simulation
        // simulateRealTimeUpdates();
    </script>

    <!-- Additional Styles for Enhanced UI -->
    <style>
        /* Print styles */
        @media print {
            #sidebar, #sidebarToggle, .modal, .btn {
                display: none !important;
            }
            
            #content {
                margin-left: 0 !important;
                width: 100% !important;
            }
            
            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }
        }

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #1d4ed8;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Toast notifications */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        /* Enhanced table styles */
        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        /* Status indicators */
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .status-available { background-color: #10b981; }
        .status-occupied { background-color: #f59e0b; }
        .status-maintenance { background-color: #ef4444; }

        /* Smooth transitions */
        * {
            transition: color 0.2s ease, background-color 0.2s ease;
        }

        /* Custom scrollbar for sidebar */
        #sidebar::-webkit-scrollbar {
            width: 6px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: #374151;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: #6b7280;
            border-radius: 3px;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Enhanced button styles */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Card hover effects */
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Form enhancements */
        .form-control:focus, .form-select:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 0.2rem rgba(29, 78, 216, 0.25);
        }

        /* Modal enhancements */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-bottom: 1px solid #e5e7eb;
            border-radius: 12px 12px 0 0;
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive table improvements */
        @media (max-width: 768px) {
            .table-responsive {
                border-radius: 8px;
            }
            
            .table th, .table td {
                font-size: 0.875rem;
                padding: 0.5rem;
            }
        }
    </style>
</body>
</html>