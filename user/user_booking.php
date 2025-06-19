<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = (int)$_POST['booking_id'];
    
    // Verify the booking belongs to the current user
    $verify_stmt = $conn->prepare("SELECT id FROM reservations WHERE id = ? AND user_id = ? AND status IN ('pending', 'confirmed')");
    $verify_stmt->bind_param("ii", $booking_id, $user_id);
    $verify_stmt->execute();
    
    if ($verify_stmt->get_result()->num_rows > 0) {
        $cancel_stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
        $cancel_stmt->bind_param("i", $booking_id);
        
        if ($cancel_stmt->execute()) {
            $_SESSION['success'] = "Booking cancelled successfully";
        } else {
            $_SESSION['error'] = "Failed to cancel booking";
        }
    } else {
        $_SESSION['error'] = "Invalid booking or cannot cancel this booking";
    }
    
    header('Location: user-bookings.php');
    exit();
}

// Get user's bookings with room details
$bookings_query = "SELECT r.*, rt.room_type, rt.room_number, rt.description as room_description,
                          r.check_in_date, r.check_out_date, r.total_price, r.status, 
                          r.special_requests, r.created_at, r.id as booking_id,
                          DATEDIFF(r.check_out_date, r.check_in_date) as nights
                   FROM reservations r
                   JOIN rooms rt ON r.room_id = rt.id
                   WHERE r.user_id = ?
                   ORDER BY r.created_at DESC";

$stmt = $conn->prepare($bookings_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();

// Get booking statistics
$stats_query = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings
                FROM reservations WHERE user_id = ?";
$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Shangri-La Hotel</title>
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
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="flex-shrink-0 flex items-center">
                        <i class="fas fa-hotel text-hotel-gold text-2xl mr-2"></i>
                        <span class="font-bold text-xl text-hotel-dark">Shangri-La</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="../rooms.php" class="text-gray-700 hover:text-hotel-gold px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-bed mr-1"></i> Rooms
                    </a>
                    <a href="user-booking.php" class="bg-hotel-gold text-white px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-calendar-check mr-1"></i> My Bookings
                    </a>
                    <a href="logout.php" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-4 mt-4 relative">
            <span class="block sm:inline"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
            <button onclick="document.getElementById('success-alert').remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-4 mt-4 relative">
            <span class="block sm:inline"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
            <button onclick="document.getElementById('error-alert').remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="bg-gradient-to-r from-hotel-dark to-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold mb-2">My Bookings</h1>
            <p class="text-lg opacity-90">Manage your hotel reservations</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-blue-600"><?php echo $stats['total_bookings']; ?></div>
                <div class="text-sm text-gray-600">Total Bookings</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-yellow-600"><?php echo $stats['pending_bookings']; ?></div>
                <div class="text-sm text-gray-600">Pending</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-green-600"><?php echo $stats['confirmed_bookings']; ?></div>
                <div class="text-sm text-gray-600">Confirmed</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-purple-600"><?php echo $stats['completed_bookings']; ?></div>
                <div class="text-sm text-gray-600">Completed</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-red-600"><?php echo $stats['cancelled_bookings']; ?></div>
                <div class="text-sm text-gray-600">Cancelled</div>
            </div>
        </div>
    </div>

    <!-- Bookings List -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <?php if ($bookings->num_rows > 0): ?>
            <div class="space-y-6">
                <?php while ($booking = $bookings->fetch_assoc()): 
                    $status_colors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'confirmed' => 'bg-green-100 text-green-800',
                        'completed' => 'bg-purple-100 text-purple-800',
                        'cancelled' => 'bg-red-100 text-red-800'
                    ];
                    $status_color = $status_colors[$booking['status']] ?? 'bg-gray-100 text-gray-800';
                    
                    $can_cancel = in_array($booking['status'], ['pending', 'confirmed']) && 
                                 strtotime($booking['check_in_date']) > time();
                ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-4">
                                        <h3 class="text-xl font-semibold text-gray-900 mr-4">
                                            <?php echo htmlspecialchars($booking['room_type']); ?>
                                        </h3>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $status_color; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-door-open text-hotel-gold mr-2"></i>
                                            <span>Room #<?php echo htmlspecialchars($booking['room_number']); ?></span>
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-calendar-alt text-hotel-gold mr-2"></i>
                                            <span><?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?></span>
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-calendar-check text-hotel-gold mr-2"></i>
                                            <span><?php echo date('M j, Y', strtotime($booking['check_out_date'])); ?></span>
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-moon text-hotel-gold mr-2"></i>
                                            <span><?php echo $booking['nights']; ?> night<?php echo $booking['nights'] != 1 ? 's' : ''; ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($booking['special_requests'])): ?>
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-comment text-hotel-gold mr-2"></i>
                                                <strong>Special Requests:</strong> <?php echo htmlspecialchars($booking['special_requests']); ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="text-sm text-gray-500">
                                        Booked on: <?php echo date('M j, Y g:i A', strtotime($booking['created_at'])); ?>
                                    </div>
                                </div>
                                
                                <div class="mt-4 lg:mt-0 lg:ml-6 text-right">
                                    <div class="text-2xl font-bold text-hotel-gold mb-4">
                                        $<?php echo number_format($booking['total_price'], 2); ?>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <button onclick="viewBookingDetails(<?php echo $booking['booking_id']; ?>)" 
                                                class="w-full lg:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-eye mr-1"></i> View Details
                                        </button>
                                        
                                        <?php if ($can_cancel): ?>
                                            <button onclick="cancelBooking(<?php echo $booking['booking_id']; ?>)" 
                                                    class="w-full lg:w-auto px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                                <i class="fas fa-times mr-1"></i> Cancel
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-600 mb-2">No Bookings Found</h3>
                <p class="text-gray-500 mb-6">You haven't made any bookings yet.</p>
                <a href="rooms.php" class="inline-block bg-hotel-gold text-white px-6 py-3 rounded-md hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-bed mr-2"></i> Browse Rooms
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Booking Details Modal -->
    <div id="bookingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Booking Details</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="modalContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Cancel Booking</h3>
                <p class="text-sm text-gray-500 mb-4">Are you sure you want to cancel this booking? This action cannot be undone.</p>
                <form id="cancelForm" method="POST">
                    <input type="hidden" name="booking_id" id="cancelBookingId">
                    <div class="flex justify-center space-x-3">
                        <button type="button" onclick="closeCancelModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            No, Keep It
                        </button>
                        <button type="submit" name="cancel_booking" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            Yes, Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function viewBookingDetails(bookingId) {
            // In a real application, you would fetch details via AJAX
            document.getElementById('bookingModal').classList.remove('hidden');
            document.getElementById('modalContent').innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
            
            // Simulate loading
            setTimeout(() => {
                document.getElementById('modalContent').innerHTML = `
                    <div class="space-y-4">
                        <p class="text-gray-600">Detailed information about booking #${bookingId} would be displayed here.</p>
                        <div class="border-t pt-4">
                            <h4 class="font-semibold mb-2">Contact Information</h4>
                            <p class="text-sm text-gray-600">For any questions about your booking, please contact our reception at:</p>
                            <p class="text-sm"><strong>Phone:</strong> +94 11 123 4567</p>
                            <p class="text-sm"><strong>Email:</strong> reservations@shangrila.com</p>
                        </div>
                    </div>
                `;
            }, 1000);
        }

        function cancelBooking(bookingId) {
            document.getElementById('cancelBookingId').value = bookingId;
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('bookingModal').classList.add('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.getElementById('bookingModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) closeCancelModal();
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[id$="-alert"]');
            alerts.forEach(alert => alert.remove());
        }, 5000);
    </script>
</body>
</html>