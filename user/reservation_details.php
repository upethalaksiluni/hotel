<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$reservation_id = intval($_GET['id'] ?? 0);

if (!$reservation_id) {
    $_SESSION['error'] = "Invalid reservation ID.";
    header('Location: reservations.php');
    exit();
}

// Get reservation details
$reservation_query = "SELECT r.*, rm.room_type, rm.room_number, rm.price_per_night, rm.description, rm.floor_number, rm.capacity,
                      GROUP_CONCAT(rf.name SEPARATOR ', ') as facilities,
                      GROUP_CONCAT(rf.icon SEPARATOR ', ') as facility_icons
                      FROM reservations r 
                      JOIN rooms rm ON r.room_id = rm.id 
                      LEFT JOIN room_facility_mapping rfm ON rm.id = rfm.room_id 
                      LEFT JOIN room_facilities rf ON rfm.facility_id = rf.id
                      WHERE r.id = ? AND r.user_id = ?
                      GROUP BY r.id";

$stmt = $conn->prepare($reservation_query);
$stmt->bind_param("ii", $reservation_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Reservation not found.";
    header('Location: reservations.php');
    exit();
}

$reservation = $result->fetch_assoc();

$title = 'Reservation Details';
$header = 'Reservation Details';

// Calculate nights and dates
$check_in_date = new DateTime($reservation['check_in_date']);
$check_out_date = new DateTime($reservation['check_out_date']);
$nights = $check_in_date->diff($check_out_date)->days;

$check_in = $check_in_date->format('M j, Y');
$check_out = $check_out_date->format('M j, Y');
$created = date('M j, Y g:i A', strtotime($reservation['created_at']));

// Parse facilities
$facilities = !empty($reservation['facilities']) ? explode(', ', $reservation['facilities']) : [];
$facility_icons = !empty($reservation['facility_icons']) ? explode(', ', $reservation['facility_icons']) : [];

// Status colors
$status_colors = [
    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'confirmed' => 'bg-green-100 text-green-800 border-green-200',
    'cancelled' => 'bg-red-100 text-red-800 border-red-200',
    'completed' => 'bg-blue-100 text-blue-800 border-blue-200'
];

$status_color = $status_colors[$reservation['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';

// Check if can be cancelled
$can_cancel = false;
if (in_array($reservation['status'], ['pending', 'confirmed'])) {
    $check_in_time = strtotime($reservation['check_in_date']);
    $current_time = time();
    $hours_until_checkin = ($check_in_time - $current_time) / 3600;
    $can_cancel = $hours_until_checkin >= 24;
}

$content = <<<HTML
<div class="max-w-4xl mx-auto">
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
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 font-playfair">Reservation #{$reservation['id']}</h2>
                <p class="text-gray-600">Booked on {$created}</p>
            </div>
            <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full border {$status_color}">
                {$reservation['status']}
            </span>
        </div>
        
        <div class="flex space-x-4">
            <a href="reservations.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Reservations
            </a>
HTML;

if ($can_cancel) {
    $content .= <<<HTML
            <button onclick="cancelReservation({$reservation['id']})" 
                    class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                <i class="fas fa-times mr-2"></i>
                Cancel Reservation
            </button>
    HTML;
}

$content .= <<<HTML
        </div>
    </div>

    <!-- Room Details -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Room Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h4 class="text-lg font-semibold text-gray-900">{$reservation['room_type']}</h4>
                <p class="text-gray-600 mb-2">Room #{$reservation['room_number']} • Floor {$reservation['floor_number']}</p>
                <p class="text-gray-700">{$reservation['description']}</p>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Capacity:</span>
                    <span class="font-medium">{$reservation['capacity']} guests</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Price per night:</span>
                    <span class="font-medium">\${$reservation['price_per_night']}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Number of nights:</span>
                    <span class="font-medium">{$nights}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-gray-900 font-semibold">Total Amount:</span>
                    <span class="text-blue-600 font-bold text-lg">\${$reservation['total_price']}</span>
                </div>
            </div>
        </div>

        <!-- Room Facilities -->
        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-3">Room Facilities</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
HTML;

if (!empty($facilities)) {
    for ($i = 0; $i < count($facilities); $i++) {
        $facility = $facilities[$i];
        $icon = isset($facility_icons[$i]) ? $facility_icons[$i] : 'fas fa-check';
        
        $content .= <<<HTML
                <div class="flex items-center space-x-2 text-gray-700">
                    <i class="{$icon} text-blue-600"></i>
                    <span class="text-sm">{$facility}</span>
                </div>
        HTML;
    }
} else {
    $content .= '<p class="text-gray-500">No facilities information available.</p>';
}

$content .= <<<HTML
            </div>
        </div>
    </div>

    <!-- Booking Details -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Booking Details</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Check-in Date</label>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar-plus text-green-600"></i>
                        <span class="text-lg font-semibold text-gray-900">{$check_in}</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Check-out Date</label>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar-minus text-red-600"></i>
                        <span class="text-lg font-semibold text-gray-900">{$check_out}</span>
                    </div>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Guest Name</label>
                    <p class="text-gray-900 font-medium">{$reservation['guest_name']}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Contact Email</label>
                    <p class="text-gray-900">{$reservation['guest_email']}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Phone Number</label>
                    <p class="text-gray-900">{$reservation['guest_phone']}</p>
                </div>
            </div>
        </div>
HTML;

if (!empty($reservation['special_requests'])) {
    $content .= <<<HTML
        
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-500 mb-2">Special Requests</label>
            <div class="bg-gray-50 p-4 rounded-md">
                <p class="text-gray-900">{$reservation['special_requests']}</p>
            </div>
        </div>
    HTML;
}

$content .= <<<HTML
    </div>

    <!-- Important Information -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-3">
            <i class="fas fa-info-circle mr-2"></i>
            Important Information
        </h3>
        <ul class="space-y-2 text-blue-800">
            <li class="flex items-start">
                <i class="fas fa-clock mt-1 mr-2 text-blue-600"></i>
                <span>Check-in time: 3:00 PM | Check-out time: 11:00 AM</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-id-card mt-1 mr-2 text-blue-600"></i>
                <span>Valid government-issued photo ID required at check-in</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-ban mt-1 mr-2 text-blue-600"></i>
                <span>Cancellations must be made at least 24 hours before check-in</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-phone mt-1 mr-2 text-blue-600"></i>
                <span>For any changes or assistance, contact us at +94 11 234 5678</span>
            </li>
        </ul>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-auto">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Cancel Reservation</h3>
            </div>
            <p class="text-gray-600 mb-6">Are you sure you want to cancel this reservation? This action cannot be undone.</p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeCancelModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                    No, Keep It
                </button>
                <form method="POST" action="reservations.php" style="display: inline;">
                    <input type="hidden" name="reservation_id" id="cancelReservationId">
                    <button type="submit" name="cancel_reservation" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                        Yes, Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
HTML;

$scripts = <<<HTML
<script>
function cancelReservation(reservationId) {
    document.getElementById('cancelReservationId').value = reservationId;
    document.getElementById('cancelModal').classList.remove('hidden');
    document.getElementById('cancelModal').classList.add('flex');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancelModal').classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});

// Print functionality
function printReservation() {
    window.print();
}
</script>
HTML;

include 'layouts/app.php';
?>