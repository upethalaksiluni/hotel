<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$title = 'Room Booking';
$header = 'Room Booking';

$styles = <<<HTML
<style>
    .room-card {
        transition: transform 0.3s ease;
    }
    .room-card:hover {
        transform: translateY(-5px);
    }
</style>
HTML;

// Get available rooms
$rooms_query = "SELECT r.*, GROUP_CONCAT(rf.name) as facilities 
                FROM rooms r 
                LEFT JOIN room_facility_mapping rfm ON r.id = rfm.room_id 
                LEFT JOIN room_facilities rf ON rfm.facility_id = rf.id 
                WHERE r.status = 'available'
                GROUP BY r.id";
$rooms = $conn->query($rooms_query);

$content = <<<HTML
<div class="max-w-7xl mx-auto">
    <!-- Search & Filter Section -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form class="grid grid-cols-1 md:grid-cols-4 gap-4" method="GET" action="">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                <input type="date" name="check_in" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                <input type="date" name="check_out" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Room Type</label>
                <select name="room_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="standard">Standard</option>
                    <option value="deluxe">Deluxe</option>
                    <option value="suite">Suite</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                    Search Rooms
                </button>
            </div>
        </form>
    </div>

    <!-- Available Rooms -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
HTML;

while ($room = $rooms->fetch_assoc()) {
    $facilities = explode(',', $room['facilities']);
    $facility_list = '';
    
    foreach ($facilities as $facility) {
        if (!empty($facility)) {
            $facility_list .= '<li class="flex items-center space-x-2"><i class="fas fa-check text-green-500"></i><span>' . htmlspecialchars($facility) . '</span></li>';
        }
    }

    $room_id = (int)$room['id'];
    $room_type = addslashes($room['room_type']);
    $price_per_night = (float)$room['price_per_night'];
    $room_number = addslashes($room['room_number'] ?? '');
    $room_image = strtolower(str_replace(' ', '-', $room['room_type'])) . '.jpg';

    $content .= <<<HTML
        <div class="room-card bg-white rounded-lg shadow overflow-hidden">
            <img src="../images/rooms/{$room_image}" alt="{$room['room_type']}" class="w-full h-48 object-cover" onerror="this.src='../images/rooms/default.jpg'">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">{$room['room_type']}</h3>
                <p class="text-gray-600 mb-4">{$room['description']}</p>
                <ul class="space-y-2 mb-4">
                    {$facility_list}
                </ul>
                <div class="flex items-center justify-between">
                    <div class="text-xl font-bold text-blue-600">\${$room['price_per_night']}<span class="text-sm text-gray-500">/night</span></div>
                    <button 
                        onclick="bookRoom({$room_id}, '{$room_type}', {$price_per_night}, '{$room_number}')"
                        class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        Book Now
                    </button>
                </div>
            </div>
        </div>
    HTML;
}

$content .= '</div></div>';

// Add the booking modal
$content .= <<<HTML
<!-- Booking Modal -->
<div id="bookingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Book Room</h3>
            <form id="bookingForm" method="POST" action="process_booking.php">
                <input type="hidden" id="roomId" name="room_id">
                
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                    <input type="date" name="check_in" id="checkIn" required 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                    <input type="date" name="check_out" id="checkOut" required 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                    <textarea name="special_requests" rows="3" 
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Any special requests..."></textarea>
                </div>
                
                <div class="flex justify-between">
                    <button type="button" onclick="closeBookingModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Book Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function bookRoom(roomId, roomType, roomPrice, roomNumber) {
    document.getElementById('roomId').value = roomId;
    document.getElementById('bookingModal').classList.remove('hidden');
}

function closeBookingModal() {
    document.getElementById('bookingModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('bookingModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBookingModal();
    }
});

// Update check-out minimum date when check-in changes
document.getElementById('checkIn').addEventListener('change', function() {
    const checkInDate = new Date(this.value);
    const nextDay = new Date(checkInDate.getTime() + 86400000);
    const nextDayStr = nextDay.toISOString().split('T')[0];
    document.getElementById('checkOut').min = nextDayStr;
    
    // If current check-out is before new minimum, update it
    if (document.getElementById('checkOut').value <= this.value) {
        document.getElementById('checkOut').value = nextDayStr;
    }
});
</script>
HTML;

include 'layouts/app.php';
?>