<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$title = 'Dashboard';
$header = 'My Dashboard';

// Get user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    // Handle error - user not found
    $_SESSION['error'] = "User not found";
    header('Location: ../logout.php');
    exit();
}

// Get user's reservations with more details
$reservations_query = "SELECT r.*, rm.room_type, rm.room_number, rm.price_per_night
                      FROM reservations r 
                      JOIN rooms rm ON r.room_id = rm.id 
                      WHERE r.user_id = ? 
                      ORDER BY r.created_at DESC";
$stmt = $conn->prepare($reservations_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservations = $stmt->get_result();

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total_bookings,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings
    FROM reservations WHERE user_id = ?";
$stmt = $conn->prepare($stats_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

$content = <<<HTML
<div class="max-w-7xl mx-auto">
HTML;

// Display messages
if (isset($_SESSION['success'])) {
    $content .= '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">' . 
                htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $content .= '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">' . 
                htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}

$content .= <<<HTML
    <!-- Welcome Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4 font-playfair">Welcome back, {$user['name']}!</h2>
        <p class="text-gray-600">Manage your bookings and explore our exclusive services.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900">{$stats['total_bookings']}</p>
                    <p class="text-sm text-gray-500">Total Bookings</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900">{$stats['pending_bookings']}</p>
                    <p class="text-sm text-gray-500">Pending</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900">{$stats['confirmed_bookings']}</p>
                    <p class="text-sm text-gray-500">Confirmed</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900">{$stats['completed_bookings']}</p>
                    <p class="text-sm text-gray-500">Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="room-booking.php" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bed text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Book a Room</h3>
                    <p class="text-sm text-gray-500">Find and book your perfect room</p>
                </div>
            </div>
        </a>
        
        <a href="profile.php" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">My Profile</h3>
                    <p class="text-sm text-gray-500">Update your personal information</p>
                </div>
            </div>
        </a>
        
        <a href="reservations.php" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">My Reservations</h3>
                    <p class="text-sm text-gray-500">View all your bookings</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Reservations -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Recent Reservations</h3>
                <a href="reservations.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
        </div>
        
        <div class="p-6">
HTML;

if ($reservations && $reservations->num_rows > 0) {
    $content .= '<div class="space-y-4">';
    $count = 0;
    while (($reservation = $reservations->fetch_assoc()) && $count < 5) {
        $status_colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'completed' => 'bg-blue-100 text-blue-800'
        ];
        
        $status_color = $status_colors[$reservation['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
        
        $check_in = date('M j, Y', strtotime($reservation['check_in_date']));
        $check_out = date('M j, Y', strtotime($reservation['check_out_date']));
        
        $content .= <<<HTML
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bed text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">{$reservation['room_type']} - Room #{$reservation['room_number']}</h4>
                        <p class="text-sm text-gray-600">{$check_in} - {$check_out}</p>
                        <p class="text-sm text-gray-500">Total: \${$reservation['total_price']}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {$status_color}">
                        {$reservation['status']}
                    </span>
                    <button onclick="viewReservation({$reservation['id']})" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        HTML;
        $count++;
    }
    $content .= '</div>';
} else {
    $content .= <<<HTML
        <div class="text-center py-8">
            <i class="fas fa-calendar-alt text-gray-300 text-4xl mb-4"></i>
            <h4 class="text-lg font-semibold text-gray-600 mb-2">No Reservations Yet</h4>
            <p class="text-gray-500 mb-4">You haven't made any reservations. Start by booking a room!</p>
            <a href="room-booking.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                <i class="fas fa-bed mr-2"></i>
                Book Now
            </a>
        </div>
    HTML;
}

$content .= <<<HTML
        </div>
    </div>
</div>
HTML;

$scripts = <<<HTML
<script>
function viewReservation(reservationId) {
    window.location.href = 'reservation_details.php?id=' + reservationId;
}
</script>
HTML;

include 'layouts/app.php';
?>