<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get filter parameters
$check_in_filter = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out_filter = isset($_GET['check_out']) ? $_GET['check_out'] : '';
$room_type_filter = isset($_GET['room_type']) ? $_GET['room_type'] : '';
$guests_filter = isset($_GET['guests']) ? intval($_GET['guests']) : 1;

// Build the rooms query with availability check
$rooms_query = "SELECT r.*, 
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM reservations res 
                        WHERE res.room_id = r.id 
                        AND res.status IN ('pending', 'confirmed')";

// Add date filter if both dates are provided
if (!empty($check_in_filter) && !empty($check_out_filter)) {
    $rooms_query .= " AND NOT (res.check_out_date <= '$check_in_filter' OR res.check_in_date >= '$check_out_filter')";
}

$rooms_query .= ") THEN 0 
                    ELSE 1 
                END as is_available
                FROM rooms r 
                WHERE r.status = 'available'";

// Add room type filter if specified
if (!empty($room_type_filter)) {
    $rooms_query .= " AND r.room_type LIKE '%" . $conn->real_escape_string($room_type_filter) . "%'";
}

$rooms_query .= " ORDER BY r.room_type, r.room_number";

$rooms_result = $conn->query($rooms_query);

// Get room facilities
$facilities_query = "SELECT rf.*, rfm.room_id 
                    FROM room_facilities rf 
                    JOIN room_facility_mapping rfm ON rf.id = rfm.facility_id";
$facilities_result = $conn->query($facilities_query);
$room_facilities = [];
while ($facility = $facilities_result->fetch_assoc()) {
    if (!isset($room_facilities[$facility['room_id']])) {
        $room_facilities[$facility['room_id']] = [];
    }
    $room_facilities[$facility['room_id']][] = $facility['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking - Shangri-La Hotel</title>
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
    <nav class="bg-white shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="dashboard.php" class="flex-shrink-0 flex items-center">
                        <i class="fas fa-hotel text-hotel-gold text-2xl mr-2"></i>
                        <span class="font-bold text-xl text-hotel-dark">Shangri-La</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-gray-700 hover:text-hotel-gold px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                    <a href="./user_booking.php" class="text-gray-700 hover:text-hotel-gold px-3 py-2 rounded-md text-sm font-medium transition-colors">
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
    <?php if (isset($_SESSION['booking_error'])): ?>
        <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-4 mt-4 relative">
            <span class="block sm:inline"><?php echo $_SESSION['booking_error']; unset($_SESSION['booking_error']); ?></span>
            <button onclick="document.getElementById('error-alert').remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="bg-gradient-to-r from-hotel-dark to-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold mb-2">Book Your Room</h1>
            <p class="text-lg opacity-90">Find the perfect accommodation for your stay</p>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4" method="GET" action="">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                    <input type="date" name="check_in" value="<?php echo $check_in_filter; ?>" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-hotel-gold focus:ring-hotel-gold" 
                           min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                    <input type="date" name="check_out" value="<?php echo $check_out_filter; ?>" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-hotel-gold focus:ring-hotel-gold" 
                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Room Type</label>
                    <select name="room_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-hotel-gold focus:ring-hotel-gold">
                        <option value="">All Types</option>
                        <option value="deluxe" <?php echo $room_type_filter == 'deluxe' ? 'selected' : ''; ?>>Deluxe</option>
                        <option value="premier" <?php echo $room_type_filter == 'premier' ? 'selected' : ''; ?>>Premier</option>
                        <option value="suite" <?php echo $room_type_filter == 'suite' ? 'selected' : ''; ?>>Suite</option>
                        <option value="executive" <?php echo $room_type_filter == 'executive' ? 'selected' : ''; ?>>Executive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guests</label>
                    <select name="guests" class="w-full rounded-md border-gray-300 shadow-sm focus:border-hotel-gold focus:ring-hotel-gold">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $guests_filter == $i ? 'selected' : ''; ?>><?php echo $i; ?> Guest<?php echo $i > 1 ? 's' : ''; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-hotel-gold text-white py-2 px-4 rounded-md hover:bg-yellow-600 transition-colors font-medium">
                        <i class="fas fa-search mr-2"></i>Search Rooms
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Available Rooms -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <?php if ($rooms_result && $rooms_result->num_rows > 0): ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                <?php 
                $available_rooms = 0;
                while ($room = $rooms_result->fetch_assoc()): 
                    $facilities = isset($room_facilities[$room['id']]) ? $room_facilities[$room['id']] : [];
                    $is_available = (int)$room['is_available'];
                    
                    if ($is_available) {
                        $available_rooms++;
                    }
                    
                    // Clean room type for JavaScript
                    $room_type_js = addslashes($room['room_type']);
                    $room_description_js = addslashes($room['description'] ?? '');
                    
                    $availability_class = $is_available ? '' : 'opacity-60';
                    $availability_text = $is_available ? 'Available' : 'Not Available';
                    $availability_color = $is_available ? 'text-green-600' : 'text-red-600';
                    
                    // Room image path
                    $room_image_name = strtolower(str_replace([' ', '_'], '-', $room['room_type'])) . '.jpg';
                    $image_path = "images/rooms/{$room_image_name}";
                    if (!file_exists($image_path)) {
                        $image_path = "images/rooms/default.jpg";
                    }
                ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 <?php echo $availability_class; ?>">
                        <div class="relative">
                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($room['room_type']); ?>" 
                                 class="w-full h-64 object-cover" 
                                 onerror="this.src='images/rooms/default.jpg'">
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $availability_text; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($room['room_type']); ?></h3>
                                <span class="text-sm text-gray-500">Room #<?php echo htmlspecialchars($room['room_number']); ?></span>
                            </div>
                            
                            <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                                <?php echo htmlspecialchars($room['description'] ?? 'Comfortable and well-appointed room with modern amenities.'); ?>
                            </p>
                            
                            <?php if (!empty($facilities)): ?>
                                <div class="mb-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Facilities:</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach (array_slice($facilities, 0, 4) as $facility): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-hotel-gold bg-opacity-10 text-hotel-gold">
                                                <i class="fas fa-check text-xs mr-1"></i>
                                                <?php echo htmlspecialchars($facility); ?>
                                            </span>
                                        <?php endforeach; ?>
                                        <?php if (count($facilities) > 4): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                +<?php echo count($facilities) - 4; ?> more
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div>
                                    <div class="text-2xl font-bold text-hotel-gold">
                                        $<?php echo number_format($room['price_per_night'], 2); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">per night</div>
                                </div>
                                
                                <?php if ($is_available): ?>
                                    <button 
                                        onclick="bookRoom(<?php echo $room['id']; ?>, '<?php echo $room_type_js; ?>', <?php echo $room['price_per_night']; ?>, '<?php echo $room['room_number']; ?>', '<?php echo $room_description_js; ?>')"
                                        class="bg-hotel-gold text-white px-6 py-2 rounded-md hover:bg-yellow-600 transition-colors font-medium">
                                        <i class="fas fa-calendar-plus mr-1"></i>Book Now
                                    </button>
                                <?php else: ?>
                                    <button class="bg-gray-400 text-white px-6 py-2 rounded-md cursor-not-allowed" disabled>
                                        <i class="fas fa-ban mr-1"></i>Unavailable
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <?php if ($available_rooms == 0 && !empty($check_in_filter) && !empty($check_out_filter)): ?>
                <div class="text-center py-12">
                    <div class="bg-white rounded-lg shadow-md p-8">
                        <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-2xl font-semibold text-gray-600 mb-2">No Available Rooms</h3>
                        <p class="text-gray-500 mb-6">Sorry, no rooms are available for the selected dates. Please try different dates or contact us directly.</p>
                        <button onclick="clearDates()" class="bg-hotel-gold text-white px-6 py-3 rounded-md hover:bg-yellow-600 transition-colors">
                            <i class="fas fa-search mr-2"></i>Try Different Dates
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="text-center py-12">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <i class="fas fa-bed text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-2xl font-semibold text-gray-600 mb-2">No Rooms Found</h3>
                    <p class="text-gray-500 mb-6">Please adjust your search criteria to find available rooms.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 xl:w-1/3 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Complete Your Booking</h3>
                    <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="roomDetails" class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-lg font-semibold" id="selectedRoomType"></p>
                            <p class="text-sm text-gray-600" id="selectedRoomNumber"></p>
                            <p class="text-sm text-gray-600 mt-1" id="selectedRoomDescription"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-hotel-gold" id="selectedRoomPrice"></p>
                        </div>
                    </div>
                </div>
                
                <form id="bookingForm" method="POST" action="process_booking.php" class="space-y-4">
                    <input type="hidden" id="roomId" name="room_id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date *</label>
                            <input type="date" name="check_in" id="checkIn" required 
                                   value="<?php echo $check_in_filter; ?>"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-hotel-gold focus:ring-hotel-gold"
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date *</label>
                            <input type="date" name="check_out" id="checkOut" required 
                                   value="<?php echo $check_out_filter; ?>"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-hotel-gold focus:ring-hotel-gold">
                        </div>
                    </div>
                    
                    <div id="totalPriceSection" class="bg-hotel-gold bg-opacity-10 p-4 rounded-lg" style="display: none;">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Total for <span id="nightsCount">0</span> night(s):</p>
                                <p class="text-xs text-gray-500">Taxes and fees included</p>
                            </div>
                            <p class="text-2xl font-bold text-hotel-gold" id="totalPrice">$0</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                        <textarea name="special_requests" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-hotel-gold focus:ring-hotel-gold"
                                  placeholder="Any special requests or preferences..."></textarea>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button type="button" onclick="closeBookingModal()" 
                                class="flex-1 px-4 py-3 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors font-medium">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-3 bg-hotel-gold text-white rounded-md hover:bg-yellow-600 transition-colors font-medium">
                            <i class="fas fa-check mr-2"></i>Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Manually added room cards (HTML only) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
            <!-- Royal Suite -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="relative">
                    <img src="images/rooms/royal-suite.jpg" alt="Royal Suite" class="w-full h-64 object-cover" onerror="this.src='images/rooms/default.jpg'">
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Available
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-bold text-gray-900">Royal Suite</h3>
                        <span class="text-sm text-gray-500">Room #501</span>
                    </div>
                    <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                        Experience ultimate luxury in our Royal Suite with panoramic city views, king bed, and private lounge.
                    </p>
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Facilities:</h4>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-hotel-gold bg-opacity-10 text-hotel-gold">
                                <i class="fas fa-check text-xs mr-1"></i> Free WiFi
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-hotel-gold bg-opacity-10 text-hotel-gold">
                                <i class="fas fa-check text-xs mr-1"></i> Jacuzzi
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-hotel-gold bg-opacity-10 text-hotel-gold">
                                <i class="fas fa-check text-xs mr-1"></i> Private Lounge
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div>
                            <div class="text-2xl font-bold text-hotel-gold">$599.00</div>
                            <div class="text-sm text-gray-500">per night</div>
                        </div>
                        <button 
                            onclick="bookRoom(999, 'Royal Suite', 599, '501', 'Experience ultimate luxury in our Royal Suite with panoramic city views, king bed, and private lounge.')"
                            class="bg-hotel-gold text-white px-6 py-2 rounded-md hover:bg-yellow-600 transition-colors font-medium">
                            <i class="fas fa-calendar-plus mr-1"></i>Book Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- Family Deluxe -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="relative">
                    <img src="images/rooms/family-deluxe.jpg" alt="Family Deluxe" class="w-full h-64 object-cover" onerror="this.src='images/rooms/default.jpg'">
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Available
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-xl font-bold text-gray-900">Family Deluxe</h3>
                        <span class="text-sm text-gray-500">Room #302</span>
                    </div>
                    <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                        Spacious room for families, featuring two queen beds, a sofa, and a kids' play area.
                    </p>
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Facilities:</h4>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-hotel-gold bg-opacity-10 text-hotel-gold">
                                <i class="fas fa-check text-xs mr-1"></i> Free WiFi
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-hotel-gold bg-opacity-10 text-hotel-gold">
                                <i class="fas fa-check text-xs mr-1"></i> Kids Play Area
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-hotel-gold bg-opacity-10 text-hotel-gold">
                                <i class="fas fa-check text-xs mr-1"></i> Sofa Bed
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div>
                            <div class="text-2xl font-bold text-hotel-gold">$299.00</div>
                            <div class="text-sm text-gray-500">per night</div>
                        </div>
                        <button 
                            onclick="bookRoom(1000, 'Family Deluxe', 299, '302', 'Spacious room for families, featuring two queen beds, a sofa, and a kids play area.')"
                            class="bg-hotel-gold text-white px-6 py-2 rounded-md hover:bg-yellow-600 transition-colors font-medium">
                            <i class="fas fa-calendar-plus mr-1"></i>Book Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentRoomPrice = 0;

        function bookRoom(roomId, roomType, roomPrice, roomNumber, roomDescription) {
            currentRoomPrice = roomPrice;
            document.getElementById('roomId').value = roomId;
            document.getElementById('selectedRoomType').textContent = roomType;
            document.getElementById('selectedRoomNumber').textContent = 'Room #' + roomNumber;
            document.getElementById('selectedRoomDescription').textContent = roomDescription;
            document.getElementById('selectedRoomPrice').textContent = '$' + roomPrice + '/night';
            document.getElementById('bookingModal').classList.remove('hidden');
            calculateTotal();
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.add('hidden');
        }

        function clearDates() {
            const url = new URL(window.location);
            url.searchParams.delete('check_in');
            url.searchParams.delete('check_out');
            window.location.href = url.toString();
        }

        function calculateTotal() {
            const checkIn = document.getElementById('checkIn').value;
            const checkOut = document.getElementById('checkOut').value;
            
            if (checkIn && checkOut) {
                const checkInDate = new Date(checkIn);
                const checkOutDate = new Date(checkOut);
                const timeDiff = checkOutDate.getTime() - checkInDate.getTime();
                const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
                
                if (nights > 0) {
                    const total = nights * currentRoomPrice;
                    document.getElementById('nightsCount').textContent = nights;
                    document.getElementById('totalPrice').textContent = '$' + total.toFixed(2);
                    document.getElementById('totalPriceSection').style.display = 'block';
                } else {
                    document.getElementById('totalPriceSection').style.display = 'none';
                }
            } else {
                document.getElementById('totalPriceSection').style.display = 'none';
            }
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
            calculateTotal();
        });

        document.getElementById('checkOut').addEventListener('change', calculateTotal);

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[id$="-alert"]');
            alerts.forEach(alert => alert.remove());
        }, 5000);

        // Initialize date validation on page load
        document.addEventListener('DOMContentLoaded', function() {
            const checkInInput = document.querySelector('input[name="check_in"]');
            const checkOutInput = document.querySelector('input[name="check_out"]');
            
            if (checkInInput) {
                checkInInput.addEventListener('change', function() {
                    const checkInDate = new Date(this.value);
                    const nextDay = new Date(checkInDate.getTime() + 86400000);
                    const nextDayStr = nextDay.toISOString().split('T')[0];
                    checkOutInput.min = nextDayStr;
                    
                    if (checkOutInput.value <= this.value) {
                        checkOutInput.value = nextDayStr;
                    }
                });
            }
        });
    </script>
</body>
</html>