<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if booking success data exists
if (!isset($_SESSION['booking_success'])) {
    header('Location: rooms.php');
    exit();
}

$booking_data = $_SESSION['booking_success'];
unset($_SESSION['booking_success']); // Clear the session data
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Shangri-La Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'hotel-gold': '#c8a97e',
                        'hotel-dark': '#2c3e50',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="flex-shrink-0 flex items-center">
                        <i class="fas fa-hotel text-hotel-gold text-2xl mr-2"></i>
                        <span class="font-bold text-xl text-hotel-dark">Shangri-La</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="rooms.php" class="text-gray-700 hover:text-hotel-gold px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-bed mr-1"></i> Rooms
                    </a>
                    <a href="user-bookings.php" class="text-gray-700 hover:text-hotel-gold px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-calendar-check mr-1"></i> My Bookings
                    </a>
                    <a href="logout.php" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Confirmation Content -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Success Animation -->
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 animate-pulse">
                    <i class="fas fa-check text-4xl text-green-600"></i>
                </div>
                <h1 class="mt-6 text-3xl font-extrabold text-gray-900">Booking Confirmed!</h1>
                <p class="mt-2 text-sm text-gray-600">Thank you for choosing Shangri-La Hotel. Your booking details are as follows:</p>
            </div>  
            <!-- Booking Details -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Booking Details</h2>
                <p class="text-gray-700"><strong>Room Type:</strong> <?php echo htmlspecialchars($booking_data['room_type']); ?></p>
                <p class="text-gray-700"><strong>Check-in Date:</strong> <?php echo htmlspecialchars($booking_data['checkin_date']); ?></p>
                <p class="text-gray-700"><strong>Check-out Date:</strong> <?php echo htmlspecialchars($booking_data['checkout_date']); ?></p>
                <p class="text-gray-700"><strong>Total Price:</strong> $<?php echo htmlspecialchars(number_format($booking_data['total_price'], 2)); ?></p>
                <p class="text-gray-700 mt-4">We look forward to welcoming you to our hotel. If you have any questions or need assistance, please contact us at <a href="mailto:info@shangri-la.com">info@shangri-la.com</a>.</p>
            </div>
            <!-- Back to Rooms Button -->
            <div class="text-center mt-6">
                <a href="rooms.php" class="inline-flex items-center px-4 py-2 bg-hotel-gold text-white font-semibold rounded-md hover:bg-hotel-dark transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Rooms
                </a>
            </div>
        </div>  
    </div>
    
</body>
</html>