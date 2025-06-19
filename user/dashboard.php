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

// Get user's reservations
$user_id = $_SESSION['user_id'];
$reservations_query = "SELECT r.*, ro.room_type, ro.room_number, ro.price_per_night 
                      FROM reservations r 
                      JOIN rooms ro ON r.room_id = ro.id 
                      WHERE r.user_id = ? 
                      ORDER BY r.created_at DESC";

$stmt = $conn->prepare($reservations_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservations = $stmt->get_result();

// Get user info
$user_query = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

$content = <<<HTML
<div class="max-w-7xl mx-auto">
    <!-- Welcome Section -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome back, {$user['name']}!</h2>
        <p class="text-gray-600">Manage your bookings and account settings from here.</p>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-bed text-3xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Book a Room</h3>
                    <p class="text-gray-600">Find and book your perfect room</p>
                    <a href="room-booking.php" class="text-blue-600 hover:text-blue-800 font-medium">Book Now →</a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-calendar-alt text-3xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">My Bookings</h3>
                    <p class="text-gray-600">View and manage your reservations</p>
                    <a href="#bookings" class="text-green-600 hover:text-green-800 font-medium">View All →</a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-user text-3xl text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">My Profile</h3>
                    <p class="text-gray-600">Update your account information</p>
                    <a href="profile.php" class="text-purple-600 hover:text-purple-800 font-medium">Edit Profile →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div id="bookings" class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">My Reservations</h3>
        </div>
        <div class="p-6">
HTML;

if ($reservations->num_rows > 0) {
    $content .= '<div class="space-y-4">';
    
    while ($reservation = $reservations->fetch_assoc()) {
        $status_colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'completed' => 'bg-blue-100 text-blue-800'
        ];
        
        $status_color = $status_colors[$reservation['status']] ?? 'bg-gray-100 text-gray-800';
        
        $check_in_formatted = date('M d, Y', strtotime($reservation['check_in_date']));
        $check_out_formatted = date('M d, Y', strtotime($reservation['check_out_date']));
        
        $content .= <<<HTML
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4 mb-2">
                            <h4 class="text-lg font-semibold text-gray-800">Reservation #{$reservation['id']}</h4>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {$status_color}">
                                {$reservation['status']}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Room: <span class="font-medium text-gray-800">{$reservation['room_type']} (#{$reservation['room_number']})</span></p>
                                <p class="text-sm text-gray-600">Check-in: <span class="font-medium text-gray-800">{$check_in_formatted}</span></p>
                                <p class="text-sm text-gray-600">Check-out: <span class="font-medium text-gray-800">{$check_out_formatted}</span></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total: <span class="font-medium text-gray-800">\${$reservation['total_price']}</span></p>
                                <p class="text-sm text-gray-600">Booked: <span class="font-medium text-gray-800">{$reservation['created_at']}</span></p>
        HTML;
        
        if (!empty($reservation['special_requests'])) {
            $content .= '<p class="text-sm text-gray-600">Special Requests: <span class="font-medium text-gray-800">' . htmlspecialchars($reservation['special_requests']) . '</span></p>';
        }
        
        $content .= <<<HTML
                            </div>
                        </div>
                    </div>
                    <div class="ml-4">
        HTML;
        
        // Show cancel button only for pending reservations
        if ($reservation['status'] === 'pending') {
            $content .= <<<HTML
                        <button onclick="cancelReservation({$reservation['id']})" 
                                class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors">
                            Cancel
                        </button>
            HTML;
        }
        
        $content .= <<<HTML
                    </div>
                </div>
            </div>
        HTML;
    }
    
    $content .= '</div>';
} else {
    $content .= <<<HTML
        <div class="text-center py-8">
            <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
            <h4 class="text-lg font-medium text-gray-800 mb-2">No Reservations Yet</h4>
            <p class="text-gray-600 mb-4">You haven't made any bookings yet. Start exploring our rooms!</p>
            <a href="./reservation.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Book Your First Room
            </a>
        </div>
    HTML;
}

$content .= <<<HTML
        </div>
    </div>
</div>

<script>
function cancelReservation(reservationId) {
    if (confirm('Are you sure you want to cancel this reservation?')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'cancel-reservation.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'reservation_id';
        input.value = reservationId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
HTML;

include 'layouts/app.php';
?>