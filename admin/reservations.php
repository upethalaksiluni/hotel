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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Reservation Management</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReservationModal">
                        <i class="fas fa-plus"></i> Add New Reservation
                    </button>
                </div>

                <!-- Reservations Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Guest Name</th>
                                        <th>Room</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($reservation = $reservations_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($reservation['id']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($reservation['guest_name']); ?><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($reservation['guest_email']); ?></small>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($reservation['room_number']); ?><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($reservation['room_type']); ?></small>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($reservation['check_in_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($reservation['check_out_date'])); ?></td>
                                        <td>$<?php echo number_format($reservation['total_price'], 2); ?></td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="pending" <?php echo $reservation['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="confirmed" <?php echo $reservation['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                    <option value="cancelled" <?php echo $reservation['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    <option value="completed" <?php echo $reservation['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewReservation(<?php echo $reservation['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="editReservation(<?php echo $reservation['id']; ?>)">
                                                <i class="fas fa-edit"></i>
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

    <!-- Add Reservation Modal -->
    <div class="modal fade" id="addReservationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addReservationForm" action="process_reservation.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Room</label>
                            <select class="form-select" name="room_id" required>
                                <?php
                                $rooms_query = "SELECT id, room_number, room_type FROM rooms WHERE status = 'available'";
                                $rooms_result = $conn->query($rooms_query);
                                while ($room = $rooms_result->fetch_assoc()):
                                ?>
                                <option value="<?php echo $room['id']; ?>">
                                    <?php echo htmlspecialchars($room['room_number'] . ' - ' . $room['room_type']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guest Name</label>
                            <input type="text" class="form-control" name="guest_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guest Email</label>
                            <input type="email" class="form-control" name="guest_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guest Phone</label>
                            <input type="tel" class="form-control" name="guest_phone" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Check In Date</label>
                            <input type="date" class="form-control" name="check_in_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Check Out Date</label>
                            <input type="date" class="form-control" name="check_out_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Special Requests</label>
                            <textarea class="form-control" name="special_requests" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Reservation</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin.js"></script>
    <script>
        function viewReservation(id) {
            // Implement view reservation details
            alert('View reservation ' + id);
        }

        function editReservation(id) {
            // Implement edit reservation
            alert('Edit reservation ' + id);
        }
    </script>
</body>
</html> 