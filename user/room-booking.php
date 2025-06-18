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
        <form class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Room Type</label>
                <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
    $facility_list = implode('</li><li class="flex items-center space-x-2">', array_map(function($facility) {
        return '<i class="fas fa-check text-green-500"></i><span>' . htmlspecialchars($facility) . '</span>';
    }, $facilities));

    $content .= <<<HTML
        <div class="room-card bg-white rounded-lg shadow overflow-hidden">
            <img src="../images/rooms/{$room['room_type']}.jpg" alt="{$room['room_type']}" class="w-full h-48 object-cover">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">{$room['room_type']}</h3>
                <p class="text-gray-600 mb-4">{$room['description']}</p>
                <ul class="space-y-2 mb-4">
                    <li class="flex items-center space-x-2">{$facility_list}</li>
                </ul>
                <div class="flex items-center justify-between">
                    <div class="text-xl font-bold text-blue-600">\${$room['price_per_night']}<span class="text-sm text-gray-500">/night</span></div>
                    <a href="book.php?room_id={$room['id']}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
    HTML;
}

$content .= '</div>';

include 'layouts/app.php';
?>