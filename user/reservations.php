<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$title = 'My Reservations';
$header = 'My Reservations';

$user_id = $_SESSION['user_id'];

// Handle cancellation request
if (isset($_POST['cancel_reservation'])) {
    $reservation_id = intval($_POST['reservation_id']);
    
    // Check if reservation belongs to user and can be cancelled
    $check_query = "SELECT * FROM reservations WHERE id = ? AND user_id = ? AND status IN ('pending', 'confirmed')";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $reservation_id, $user_id);
    $stmt->execute();
    $reservation = $stmt->get_result()->fetch_assoc();
    
    if ($reservation) {
        // Check if cancellation is allowed (at least 24 hours before check-in)
        $check_in_time = strtotime($reservation['check_in_date']);
        $current_time = time();
        $hours_until_checkin = ($check_in_time - $current_time) / 3600;
        
        if ($hours_until_checkin >= 24) {
            $cancel_query = "UPDATE reservations SET status = 'cancelled' WHERE id = ?";
            $stmt = $conn->prepare($cancel_query);
            $stmt->bind_param("i", $reservation_id);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Reservation cancelled successfully.";
            } else {
                $_SESSION['error'] = "Failed to cancel reservation.";
            }
        } else {
            $_SESSION['error'] = "Reservations can only be cancelled at least 24 hours before check-in.";
        }
    } else {
        $_SESSION['error'] = "Reservation not found or cannot be cancelled.";
    }
    
    header('Location: reservations.php');
    exit();
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';

// Build query with filters
$reservations_query = "SELECT r.*, rm.room_type, rm.room_number, rm.price_per_night, rm.description
                      FROM reservations r 
                      JOIN rooms rm ON r.room_id = rm.id 
                      WHERE r.user_id = ?";

$params = [$user_id];
$types = "i";

if (!empty($status_filter)) {
    $reservations_query .= " AND r.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($date_filter)) {
    $reservations_query .= " AND r.check_in_date >= ?";
    $params[] = $date_filter;
    $types .= "s";
}

$reservations_query .= " ORDER BY r.created_at DESC";

$stmt = $conn->prepare($reservations_query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$reservations = $stmt->get_result();

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
    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="date" value="{$date_filter}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Reservations List -->
    <div class="space-y-6">
HTML;

if ($reservations->num_rows > 0) {
    while ($reservation = $reservations->fetch_assoc()) {
        $status_colors = [
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'confirmed' => 'bg-green-100 text-green-800 border-green-200',
            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
            'completed' => 'bg-blue-100 text-blue-800 border-blue-200'
        ];
        
        $status_color = $status_colors[$reservation['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
        
        $check_in = date('M j, Y', strtotime($reservation['check_in_date']));
        $check_out = date('M j, Y', strtotime($reservation['check_out_date']));
        $created = date('M j, Y g:i A', strtotime($reservation['created_at']));
        
        // Calculate nights
        $check_in_date = new DateTime($reservation['check_in_date']);
        $check_out_date = new DateTime($reservation['check_out_date']);
        $nights = $check_in_date->diff($check_out_date)->days;
        
        // Check if can be cancelled
        $can_cancel = false;
        if (in_array($reservation['status'], ['pending', 'confirmed'])) {
            $check_in_time = strtotime($reservation['check_in_date']);
            $current_time = time();
            $hours_until_checkin = ($check_in_time - $current_time) / 3600;
            $can_cancel = $hours_until_checkin >= 24;
        }
        
        $content .= <<<HTML
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">{$reservation['room_type']}</h3>
                            <p class="text-sm text-gray-600">Room #{$reservation['room_number']} • Reservation #{$reservation['id']}</p>
                        </div>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full border {$status_color}">
                            {$reservation['status']}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Check-in</label>
                            <p class="text-lg font-semibold text-gray-900">{$check_in}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Check-out</label>
                            <p class="text-lg font-semibold text-gray-900">{$check_out}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nights</label>
                            <p class="text-lg font-semibold text-gray-900">{$nights}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Total Price</label>
                            <p class="text-lg font-semibold text-blue-600">\${$reservation['total_price']}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Guest Name</label>
                            <p class="text-gray-900">{$reservation['guest_name']}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Guest Email</label>
                            <p class="text-gray-900">{$reservation['guest_email']}</p>
                        </div>
                    </div>
HTML;

        if (!empty($reservation['special_requests'])) {
            $content .= <<<HTML
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-500">Special Requests</label>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-md">{$reservation['special_requests']}</p>
                </div>
            HTML;
        }

        $content .= <<<HTML
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-500">Booked on {$created}</p>
                        <div class="flex space-x-3">
                            <a href="reservation_details.php?id={$reservation['id']}" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-eye mr-2"></i>
                                View Details
                            </a>
HTML;

        if ($can_cancel) {
            $content .= <<<HTML
                            <button onclick="cancelReservation({$reservation['id']})" 
                                    class="inline-flex items-center px-3 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </button>
            HTML;
        }

        $content .= <<<HTML
                        </div>
                    </div>
                </div>
            </div>
        HTML;
    }
} else {
    $content .= <<<HTML
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-calendar-alt text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-4">No Reservations Found</h3>
            <p class="text-gray-500 mb-6">You don't have any reservations matching your criteria.</p>
            <a href="room-booking.php" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                <i class="fas fa-bed mr-2"></i>
                Book a Room
            </a>
        </div>
    HTML;
}

$content .= <<<HTML
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
                <form method="POST" style="display: inline;">
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
</script>
HTML;

include 'layouts/app.php';
?>