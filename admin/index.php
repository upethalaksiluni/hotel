<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get statistics
require_once '../config/database.php';

// Total rooms
$rooms_query = "SELECT COUNT(*) as total_rooms FROM rooms";
$rooms_result = $conn->query($rooms_query);
$total_rooms = $rooms_result->fetch_assoc()['total_rooms'];

// Available rooms
$available_query = "SELECT COUNT(*) as available_rooms FROM rooms WHERE status = 'available'";
$available_result = $conn->query($available_query);
$available_rooms = $available_result->fetch_assoc()['available_rooms'];

// Total reservations
$reservations_query = "SELECT COUNT(*) as total_reservations FROM reservations";
$reservations_result = $conn->query($reservations_query);
$total_reservations = $reservations_result->fetch_assoc()['total_reservations'];

// Get count of unseen reservations
$unseen_query = "SELECT COUNT(*) as unseen_count FROM reservations WHERE seen_by_admin = 0";
$unseen_result = $conn->query($unseen_query);
$unseen_count = $unseen_result->fetch_assoc()['unseen_count'];

// Mark all unseen reservations as seen
if ($unseen_count > 0) {
    $conn->query("UPDATE reservations SET seen_by_admin = 1 WHERE seen_by_admin = 0");
}

// Recent reservations
$recent_query = "SELECT r.*, rm.room_number, rm.room_type 
                FROM reservations r 
                LEFT JOIN rooms rm ON r.room_id = rm.id 
                ORDER BY r.created_at DESC LIMIT 5";
$recent_result = $conn->query($recent_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - The Royal Grand Colombo</title>
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

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
        }

        /* Top Bar */
        .top-bar {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 0;
            font-size: 0.9rem;
        }

        .top-bar a {
            color: white;
            text-decoration: none;
        }

        .social-links a {
            margin-left: 15px;
        }

        /* Navigation */
        .navbar {
            padding: 20px 0;
            background-color: white !important;
        }

        .navbar-brand img {
            height: 50px;
        }

        .nav-link {
            color: var(--primary-color) !important;
            font-weight: 500;
            padding: 10px 20px !important;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--accent-color) !important;
        }

        /* Dashboard Content */
        .dashboard-header {
            position: relative;
            overflow: hidden;
            padding: 100px 0;
            margin-bottom: 50px;
        }

        .video-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .video-background video {
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            object-fit: cover;
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .dashboard-header .container {
            position: relative;
            z-index: 1;
        }

        .dashboard-header h1,
        .dashboard-header p {
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .dashboard-header h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }

        .dashboard-header p {
            font-size: 1.5rem;
            opacity: 0.9;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2rem;
            color: var(--accent-color);
            margin-bottom: 15px;
        }

        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .stat-card p {
            color: var(--secondary-color);
            margin: 0;
        }

        .recent-reservations {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .table th {
            font-weight: 600;
            color: var(--primary-color);
        }

        .badge {
            padding: 8px 12px;
            font-weight: 500;
        }

        .badge.bg-success {
            background-color: #28a745 !important;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
        }

        .badge.bg-danger {
            background-color: #dc3545 !important;
        }

        /* Footer */
        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 80px 0 0;
            margin-top: 80px;
        }

        .footer h5 {
            color: var(--accent-color);
            margin-bottom: 25px;
            font-weight: 600;
        }

        .footer p {
            color: #999;
            line-height: 1.8;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 15px;
        }

        .footer-links a {
            color: #999;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--accent-color);
        }

        .footer-bottom {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 20px 0;
            margin-top: 60px;
        }

        .footer-bottom p {
            margin: 0;
            color: #999;
        }

        .footer-social a {
            color: #999;
            margin-left: 20px;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .footer-social a:hover {
            color: var(--accent-color);
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <span><i class="fas fa-phone me-2"></i>+94 11 7 888 288</span>
                    <span class="ms-3"><i class="fas fa-envelope me-2"></i>info@The Royal Grand Colombo.com</span>
                </div>
                <div class="col-md-6 text-end">
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <img src="../images/logo.webp" alt="The Royal Grand Colombo" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Dashboard
                            <?php if ($unseen_count > 0): ?>
                                <span class="badge bg-danger ms-1"><?php echo $unseen_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rooms.php">Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reservations.php">Reservations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="facilities.php">Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Header -->
    <div class="dashboard-header text-center">
        <div class="video-background">
            <video autoplay muted loop playsinline webkit-playsinline preload="auto" id="myVideo">
                <source src="../video/indexvideo.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="video-overlay"></div>
        </div>
        <div class="container position-relative">
            <h1>Admin Dashboard</h1>
            <p class="lead">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="container">
        <!-- Statistics -->
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fas fa-bed"></i>
                    <h3><?php echo $total_rooms; ?></h3>
                    <p>Total Rooms</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fas fa-door-open"></i>
                    <h3><?php echo $available_rooms; ?></h3>
                    <p>Available Rooms</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fas fa-calendar-check"></i>
                    <h3><?php echo $total_reservations; ?></h3>
                    <p>Total Reservations</p>
                </div>
            </div>
        </div>

        <!-- Recent Reservations -->
        <div class="recent-reservations">
            <h4 class="mb-4">Recent Reservations</h4>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($reservation = $recent_result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $reservation['id']; ?></td>
                            <td><?php echo htmlspecialchars($reservation['guest_name']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['room_type'] . ' (' . $reservation['room_number'] . ')'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($reservation['check_in_date'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($reservation['check_out_date'])); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $reservation['status'] == 'confirmed' ? 'success' : 
                                        ($reservation['status'] == 'pending' ? 'warning' : 'danger'); 
                                ?>">
                                    <?php echo ucfirst($reservation['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="reservations.php?id=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>About The Royal Grand Colombo</h5>
                    <p>Experience luxury and comfort at its finest. Our hotel offers world-class amenities, exceptional service, and unforgettable memories.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="../about.php">About Us</a></li>
                        <li><a href="rooms.php">Rooms & Suites</a></li>
                        <li><a href="../dining.php">Dining</a></li>
                        <li><a href="../experience.php">Experience</a></li>
                        <li><a href="../contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt me-2"></i> 1 Galle Face, Colombo 2, Sri Lanka</li>
                        <li><i class="fas fa-phone me-2"></i>+94 11 7 888 288</li>
                        <li><i class="fas fa-envelope me-2"></i>info@The Royal Grand Colombo.com</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p>&copy; 2025 The Royal Grand Colombo Hotel. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="footer-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>