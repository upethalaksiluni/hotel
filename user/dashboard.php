<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get user's reservations
$reservations_query = "SELECT r.*, rm.room_type, rm.room_number 
                      FROM reservations r 
                      JOIN rooms rm ON r.room_id = rm.id 
                      WHERE r.user_id = ? 
                      ORDER BY r.check_in_date DESC";
$stmt = $conn->prepare($reservations_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservations = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - The Royal Grand Colombo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a1a1a;
            --secondary-color: #666;
            --accent-color: #c8a97e;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fa;
        }

        .dashboard-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../images/dashboard-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }

        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .reservation-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .reservation-card:hover {
            transform: translateY(-5px);
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .status-pending { background-color: #ffeeba; color: #856404; }
        .status-confirmed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .status-completed { background-color: #cce5ff; color: #004085; }
    </style>
</head>
<body>
    <!-- Include your navigation here -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Dashboard Header -->
    <div class="dashboard-header text-center">
        <div class="container">
            <h1 class="display-4">Welcome, <?php echo htmlspecialchars($user['name']); ?></h1>
            <p class="lead">Manage your bookings and profile</p>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="container">
        <div class="row">
            <!-- Profile Section -->
            <div class="col-md-4">
                <div class="profile-card">
                    <h3 class="mb-4">My Profile</h3>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <p class="form-control-static"><?php echo htmlspecialchars($user['name']); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <p class="form-control-static"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <a href="edit-profile.php" class="btn btn-outline-primary">Edit Profile</a>
                </div>
            </div>

            <!-- Reservations Section -->
            <div class="col-md-8">
                <h3 class="mb-4">My Reservations</h3>
                <?php if ($reservations->num_rows > 0): ?>
                    <?php while ($reservation = $reservations->fetch_assoc()): ?>
                        <div class="reservation-card">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5><?php echo htmlspecialchars($reservation['room_type']); ?></h5>
                                    <p class="mb-1">
                                        <i class="fas fa-calendar me-2"></i>
                                        <?php echo date('M d, Y', strtotime($reservation['check_in_date'])); ?> - 
                                        <?php echo date('M d, Y', strtotime($reservation['check_out_date'])); ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="fas fa-bed me-2"></i>
                                        Room <?php echo htmlspecialchars($reservation['room_number']); ?>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fas fa-dollar-sign me-2"></i>
                                        Total: $<?php echo number_format($reservation['total_price'], 2); ?>
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="status-badge status-<?php echo strtolower($reservation['status']); ?>">
                                        <?php echo ucfirst($reservation['status']); ?>
                                    </span>
                                    <?php if ($reservation['status'] == 'pending'): ?>
                                        <button class="btn btn-sm btn-danger mt-2" onclick="cancelReservation(<?php echo $reservation['id']; ?>)">
                                            Cancel
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        You haven't made any reservations yet. 
                        <a href="../rooms.php" class="alert-link">Book a room now!</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cancelReservation(reservationId) {
            if (confirm('Are you sure you want to cancel this reservation?')) {
                window.location.href = `cancel_reservation.php?id=${reservationId}`;
            }
        }
    </script>
</body>
</html> 