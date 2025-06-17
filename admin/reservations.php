<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.html');
    exit();
}

// Handle reservation status update
if (isset($_POST['update_status'])) {
    $reservation_id = $_POST['reservation_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $reservation_id);
    $stmt->execute();
}

// Get all reservations with room and user details
$reservations_query = "SELECT r.*, rm.room_number, rm.room_type, u.name as user_name 
                      FROM reservations r 
                      LEFT JOIN rooms rm ON r.room_id = rm.id 
                      LEFT JOIN users u ON r.user_id = u.id 
                      ORDER BY r.check_in_date DESC";
$reservations_result = $conn->query($reservations_query);

// Get single reservation for editing/viewing
$edit_reservation = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_query = "SELECT r.*, rm.room_number, rm.room_type 
                   FROM reservations r 
                   LEFT JOIN rooms rm ON r.room_id = rm.id 
                   WHERE r.id = ?";
    $edit_stmt = $conn->prepare($edit_query);
    $edit_stmt->bind_param("i", $edit_id);
    $edit_stmt->execute();
    $edit_result = $edit_stmt->get_result();
    $edit_reservation = $edit_result->fetch_assoc();
}

$view_reservation = null;
if (isset($_GET['view_id'])) {
    $view_id = $_GET['view_id'];
    $view_query = "SELECT r.*, rm.room_number, rm.room_type, u.name as user_name 
                   FROM reservations r 
                   LEFT JOIN rooms rm ON r.room_id = rm.id 
                   LEFT JOIN users u ON r.user_id = u.id 
                   WHERE r.id = ?";
    $view_stmt = $conn->prepare($view_query);
    $view_stmt->bind_param("i", $view_id);
    $view_stmt->execute();
    $view_result = $view_stmt->get_result();
    $view_reservation = $view_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Management - The Royal Grand Colombo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .table-responsive {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-confirmed { background-color: #d1ecf1; color: #0c5460; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .status-completed { background-color: #d4edda; color: #155724; }
        
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
            
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .table th, .table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.8rem;
            }
            
            .btn-group-vertical .btn {
                margin-bottom: 0.25rem;
            }
        }

        /* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;500;600;700;800&display=swap');

/* Root Variables */
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --accent-color: #e74c3c;
    --success-color: #27ae60;
    --warning-color: #f39c12;
    --info-color: #17a2b8;
    --light-color: #f8f9fa;
    --dark-color: #212529;
    --sidebar-width: 250px;
    --sidebar-collapsed-width: 60px;
    --header-height: 60px;
    --border-radius: 8px;
    --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

/* Global Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Nunito', sans-serif;
    background-color: var(--light-color);
    color: var(--dark-color);
    line-height: 1.6;
}

/* Wrapper */
.wrapper {
    display: flex;
    width: 100%;
    min-height: 100vh;
    position: relative;
}

/* Sidebar Styles */
#sidebar {
    width: var(--sidebar-width);
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 999;
    background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
    color: white;
    transition: var(--transition);
    box-shadow: var(--box-shadow);
    overflow-y: auto;
}

#sidebar.active {
    margin-left: -var(--sidebar-width);
}

/* Sidebar Header */
.sidebar-header {
    padding: 20px;
    background: rgba(0, 0, 0, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-header h3 {
    color: white;
    font-weight: 600;
    font-size: 1.2rem;
    margin: 0;
    text-align: center;
}

/* Sidebar Navigation */
#sidebar ul.components {
    padding: 20px 0;
    list-style: none;
}

#sidebar ul li {
    margin: 0;
}

#sidebar ul li a {
    padding: 15px 25px;
    font-size: 1rem;
    display: block;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: var(--transition);
    border-left: 3px solid transparent;
}

#sidebar ul li a:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    border-left-color: var(--secondary-color);
    transform: translateX(5px);
}

#sidebar ul li.active > a {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    border-left-color: var(--secondary-color);
}

#sidebar ul li a i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

/* Content Area */
#content {
    width: calc(100% - var(--sidebar-width));
    margin-left: var(--sidebar-width);
    min-height: 100vh;
    transition: var(--transition);
    padding: 20px;
}

#sidebar.active ~ #content {
    width: 100%;
    margin-left: 0;
}

/* Card Styles */
.card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    background: white;
}

.card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.card-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
    color: white;
    border-bottom: none;
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
}

.card-body {
    padding: 1.5rem;
}

/* Button Styles */
.btn {
    border-radius: var(--border-radius);
    font-weight: 500;
    transition: var(--transition);
    border: none;
    padding: 0.5rem 1rem;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-primary {
    background: linear-gradient(135deg, var(--secondary-color) 0%, #2980b9 100%);
}

.btn-success {
    background: linear-gradient(135deg, var(--success-color) 0%, #229954 100%);
}

.btn-warning {
    background: linear-gradient(135deg, var(--warning-color) 0%, #d68910 100%);
}

.btn-danger {
    background: linear-gradient(135deg, var(--accent-color) 0%, #c0392b 100%);
}

.btn-info {
    background: linear-gradient(135deg, var(--info-color) 0%, #138496 100%);
}

/* Table Styles */
.table {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: var(--primary-color);
    background-color: rgba(52, 73, 94, 0.05);
    border-bottom: 2px solid rgba(52, 73, 94, 0.1);
}

.table td {
    vertical-align: middle;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.table-hover tbody tr:hover {
    background-color: rgba(52, 73, 94, 0.05);
}

.table-dark th {
    background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
    border-color: rgba(255, 255, 255, 0.1);
}

/* Form Styles */
.form-control, .form-select {
    border-radius: var(--border-radius);
    border: 2px solid #e9ecef;
    transition: var(--transition);
    padding: 0.75rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}

.form-label {
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

/* Modal Styles */
.modal-content {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
    color: white;
    border-bottom: none;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
}

.modal-header .btn-close {
    filter: invert(1);
}

/* Alert Styles */
.alert {
    border: none;
    border-radius: var(--border-radius);
    font-weight: 500;
}

.alert-success {
    background: linear-gradient(135deg, rgba(39, 174, 96, 0.1) 0%, rgba(34, 153, 84, 0.1) 100%);
    color: var(--success-color);
    border-left: 4px solid var(--success-color);
}

.alert-danger {
    background: linear-gradient(135deg, rgba(231, 76, 60, 0.1) 0%, rgba(192, 57, 43, 0.1) 100%);
    color: var(--accent-color);
    border-left: 4px solid var(--accent-color);
}

.alert-warning {
    background: linear-gradient(135deg, rgba(243, 156, 18, 0.1) 0%, rgba(214, 137, 16, 0.1) 100%);
    color: var(--warning-color);
    border-left: 4px solid var(--warning-color);
}

.alert-info {
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(19, 132, 150, 0.1) 100%);
    color: var(--info-color);
    border-left: 4px solid var(--info-color);
}

/* Badge Styles */
.badge {
    font-weight: 500;
    border-radius: 50px;
    padding: 0.35em 0.75em;
}

/* Status Select Styles */
.status-select {
    min-width: 120px;
    font-size: 0.875rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    :root {
        --sidebar-width: 100%;
    }
    
    #sidebar {
        margin-left: -100%;
        width: 100%;
    }
    
    #sidebar.active {
        margin-left: 0;
    }
    
    #content {
        width: 100%;
        margin-left: 0;
        padding: 15px;
    }
    
    .sidebar-header h3 {
        font-size: 1rem;
    }
    
    #sidebar ul li a {
        padding: 12px 20px;
        font-size: 0.9rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .form-control, .form-select {
        padding: 0.5rem;
        font-size: 0.9rem;
    }
    
    h2 {
        font-size: 1.5rem;
    }
    
    h5 {
        font-size: 1.1rem;
    }
}

@media (max-width: 576px) {
    #content {
        padding: 10px;
    }
    
    .table th, .table td {
        padding: 0.5rem 0.25rem;
        font-size: 0.8rem;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 0.25rem;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .form-control, .form-select {
        font-size: 0.875rem;
    }
    
    .btn {
        font-size: 0.8rem;
    }
    
    .sidebar-header {
        padding: 15px;
    }
    
    #sidebar ul li a {
        padding: 10px 15px;
        font-size: 0.85rem;
    }
    
    .alert {
        font-size: 0.875rem;
        padding: 0.75rem;
    }
}

/* Mobile Sidebar Toggle */
@media (max-width: 768px) {
    .mobile-sidebar-toggle {
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1000;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--box-shadow);
    }
    
    .mobile-sidebar-toggle:hover {
        background: #34495e;
        color: white;
    }
}

/* Scrollbar Styles */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #34495e;
}

/* Loading Spinner */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Utility Classes */
.text-primary { color: var(--primary-color) !important; }
.text-secondary { color: var(--secondary-color) !important; }
.bg-primary { background-color: var(--primary-color) !important; }
.bg-secondary { background-color: var(--secondary-color) !important; }

/* Animation Classes */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

.slide-in {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(0); }
}

/* Print Styles */
@media print {
    #sidebar {
        display: none;
    }
    
    #content {
        width: 100% !important;
        margin-left: 0 !important;
    }
    
    .btn, .alert {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
    </style>
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
                <li>
                    <a href="rooms.php"><i class="fas fa-bed"></i> Rooms</a>
                </li>
                <li class="active">
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
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                    <h2 class="mb-2 mb-md-0">Reservation Management</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReservationModal">
                        <i class="fas fa-plus"></i> Add New Reservation
                    </button>
                </div>

                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['success']) {
                        case '1': echo 'Reservation added successfully!'; break;
                        case '2': echo 'Reservation updated successfully!'; break;
                        case '3': echo 'Reservation deleted successfully!'; break;
                        default: echo 'Operation completed successfully!';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Error: <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Reservations Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Guest Name</th>
                                        <th class="d-none d-md-table-cell">Room</th>
                                        <th class="d-none d-lg-table-cell">Check In</th>
                                        <th class="d-none d-lg-table-cell">Check Out</th>
                                        <th class="d-none d-md-table-cell">Total Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($reservation = $reservations_result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($reservation['id']); ?></td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($reservation['guest_name']); ?></div>
                                            <small class="text-muted d-block"><?php echo htmlspecialchars($reservation['guest_email']); ?></small>
                                            <small class="text-muted d-md-none">
                                                Room: <?php echo htmlspecialchars($reservation['room_number']); ?><br>
                                                <?php echo date('M d, Y', strtotime($reservation['check_in_date'])); ?> - 
                                                <?php echo date('M d, Y', strtotime($reservation['check_out_date'])); ?><br>
                                                $<?php echo number_format($reservation['total_price'], 2); ?>
                                            </small>
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            <div class="fw-bold"><?php echo htmlspecialchars($reservation['room_number']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($reservation['room_type']); ?></small>
                                        </td>
                                        <td class="d-none d-lg-table-cell"><?php echo date('M d, Y', strtotime($reservation['check_in_date'])); ?></td>
                                        <td class="d-none d-lg-table-cell"><?php echo date('M d, Y', strtotime($reservation['check_out_date'])); ?></td>
                                        <td class="d-none d-md-table-cell fw-bold">$<?php echo number_format($reservation['total_price'], 2); ?></td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                                <select name="status" class="form-select form-select-sm status-select" onchange="this.form.submit()">
                                                    <option value="pending" <?php echo $reservation['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="confirmed" <?php echo $reservation['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                    <option value="cancelled" <?php echo $reservation['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    <option value="completed" <?php echo $reservation['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical d-md-none" role="group">
                                                <button class="btn btn-sm btn-info mb-1" onclick="viewReservation(<?php echo $reservation['id']; ?>)">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                <button class="btn btn-sm btn-warning" onclick="editReservation(<?php echo $reservation['id']; ?>)">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            </div>
                                            <div class="btn-group d-none d-md-inline-flex" role="group">
                                                <button class="btn btn-sm btn-info" onclick="viewReservation(<?php echo $reservation['id']; ?>)" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning" onclick="editReservation(<?php echo $reservation['id']; ?>)" title="Edit Reservation">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteReservation(<?php echo $reservation['id']; ?>)" title="Delete Reservation">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
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

    <!-- Add Reservation Modal -->
    <div class="modal fade" id="addReservationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addReservationForm" action="process_reservation.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Room</label>
                                <select class="form-select" name="room_id" required>
                                    <option value="">Select Room</option>
                                    <?php
                                    $rooms_query = "SELECT id, room_number, room_type, price_per_night FROM rooms WHERE status = 'available'";
                                    $rooms_result = $conn->query($rooms_query);
                                    while ($room = $rooms_result->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $room['id']; ?>" data-price="<?php echo $room['price_per_night']; ?>">
                                        <?php echo htmlspecialchars($room['room_number'] . ' - ' . $room['room_type'] . ' ($' . $room['price_per_night'] . '/night)'); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guest Name</label>
                                <input type="text" class="form-control" name="guest_name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guest Email</label>
                                <input type="email" class="form-control" name="guest_email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guest Phone</label>
                                <input type="tel" class="form-control" name="guest_phone" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check In Date</label>
                                <input type="date" class="form-control" name="check_in_date" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check Out Date</label>
                                <input type="date" class="form-control" name="check_out_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Special Requests</label>
                            <textarea class="form-control" name="special_requests" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="alert alert-info">
                                <strong>Total Price: $<span id="totalPrice">0.00</span></strong>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Reservation</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Reservation Modal -->
    <div class="modal fade" id="viewReservationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reservation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewReservationContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Reservation Modal -->
    <div class="modal fade" id="editReservationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editReservationContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin.js"></script>
    <script>
        // Calculate total price in add form
        function calculatePrice() {
            const roomSelect = document.querySelector('select[name="room_id"]');
            const checkInInput = document.querySelector('input[name="check_in_date"]');
            const checkOutInput = document.querySelector('input[name="check_out_date"]');
            const totalPriceSpan = document.getElementById('totalPrice');
            
            if (roomSelect.value && checkInInput.value && checkOutInput.value) {
                const pricePerNight = roomSelect.options[roomSelect.selectedIndex].dataset.price;
                const checkIn = new Date(checkInInput.value);
                const checkOut = new Date(checkOutInput.value);
                const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                
                if (nights > 0) {
                    const totalPrice = pricePerNight * nights;
                    totalPriceSpan.textContent = totalPrice.toFixed(2);
                } else {
                    totalPriceSpan.textContent = '0.00';
                }
            }
        }
        
        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const roomSelect = document.querySelector('select[name="room_id"]');
            const checkInInput = document.querySelector('input[name="check_in_date"]');
            const checkOutInput = document.querySelector('input[name="check_out_date"]');
            
            if (roomSelect) roomSelect.addEventListener('change', calculatePrice);
            if (checkInInput) checkInInput.addEventListener('change', calculatePrice);
            if (checkOutInput) checkOutInput.addEventListener('change', calculatePrice);
            
            // Set minimum checkout date when checkin changes
            if (checkInInput && checkOutInput) {
                checkInInput.addEventListener('change', function() {
                    const checkInDate = new Date(this.value);
                    checkInDate.setDate(checkInDate.getDate() + 1);
                    checkOutInput.min = checkInDate.toISOString().split('T')[0];
                });
            }
        });

        function viewReservation(id) {
            fetch(`get_reservation.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('viewReservationContent').innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Guest Information</h6>
                                    <p><strong>Name:</strong> ${data.reservation.guest_name}</p>
                                    <p><strong>Email:</strong> ${data.reservation.guest_email}</p>
                                    <p><strong>Phone:</strong> ${data.reservation.guest_phone}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Reservation Details</h6>
                                    <p><strong>Room:</strong> ${data.reservation.room_number} - ${data.reservation.room_type}</p>
                                    <p><strong>Check In:</strong> ${new Date(data.reservation.check_in_date).toLocaleDateString()}</p>
                                    <p><strong>Check Out:</strong> ${new Date(data.reservation.check_out_date).toLocaleDateString()}</p>
                                    <p><strong>Total Price:</strong> $${parseFloat(data.reservation.total_price).toFixed(2)}</p>
                                    <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(data.reservation.status)}">${data.reservation.status}</span></p>
                                </div>
                            </div>
                            ${data.reservation.special_requests ? `
                                <hr>
                                <h6 class="fw-bold">Special Requests</h6>
                                <p>${data.reservation.special_requests}</p>
                            ` : ''}
                            <hr>
                            <small class="text-muted">Created: ${new Date(data.reservation.created_at).toLocaleString()}</small>
                        `;
                        new bootstrap.Modal(document.getElementById('viewReservationModal')).show();
                    } else {
                        alert('Error loading reservation details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading reservation details');
                });
        }

        function editReservation(id) {
            fetch(`get_reservation.php?id=${id}&edit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('editReservationContent').innerHTML = `
                            <form action="process_reservation.php" method="POST">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="reservation_id" value="${data.reservation.id}">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Room</label>
                                        <select class="form-select" name="room_id" required>
                                            ${data.rooms.map(room => `
                                                <option value="${room.id}" ${room.id == data.reservation.room_id ? 'selected' : ''}>
                                                    ${room.room_number} - ${room.room_type} ($${room.price_per_night}/night)
                                                </option>
                                            `).join('')}
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guest Name</label>
                                        <input type="text" class="form-control" name="guest_name" value="${data.reservation.guest_name}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guest Email</label>
                                        <input type="email" class="form-control" name="guest_email" value="${data.reservation.guest_email}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Guest Phone</label>
                                        <input type="tel" class="form-control" name="guest_phone" value="${data.reservation.guest_phone}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Check In Date</label>
                                        <input type="date" class="form-control" name="check_in_date" value="${data.reservation.check_in_date}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Check Out Date</label>
                                        <input type="date" class="form-control" name="check_out_date" value="${data.reservation.check_out_date}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Special Requests</label>
                                    <textarea class="form-control" name="special_requests" rows="3">${data.reservation.special_requests || ''}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Reservation</button>
                            </form>
                        `;
                        new bootstrap.Modal(document.getElementById('editReservationModal')).show();
                    } else {
                        alert('Error loading reservation details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading reservation details');
                });
        }

        function deleteReservation(id) {
            if (confirm('Are you sure you want to delete this reservation?')) {
                window.location.href = `process_reservation.php?action=delete&id=${id}`;
            }
        }

        function getStatusColor(status) {
            switch(status) {
                case 'pending': return 'warning';
                case 'confirmed': return 'info';
                case 'cancelled': return 'danger';
                case 'completed': return 'success';
                default: return 'secondary';
            }
        }
    </script>
</body>
</html>