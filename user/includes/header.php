<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config/database.php';
include 'includes/header.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - The Royal Grand</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap');

        body {
            font-family: 'Montserrat', sans-serif;
        }
        .font-playfair {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Mobile Toggle -->
    <button id="sidebarToggle" class="fixed top-4 left-4 z-50 lg:hidden bg-gray-900 text-white p-2 rounded-md">
        <i class="fas fa-bars"></i>
    </button>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-gray-900 text-white w-64 fixed inset-y-0 left-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto">
            <div class="p-6 border-b border-gray-800">
                <h3 class="text-xl font-bold font-playfair">User Dashboard</h3>
            </div>
            
            <ul class="mt-6 space-y-2">
                <li class="px-6 py-3">
                    <a href="dashboard.php" class="flex items-center <?php echo $current_page === 'dashboard.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white'; ?> transition-colors">
                        <i class="fas fa-home w-5"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="room-booking.php" class="flex items-center <?php echo $current_page === 'room-booking.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white'; ?> transition-colors">
                        <i class="fas fa-bed w-5"></i>
                        <span class="ml-3">Room Booking</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="food-order.php" class="flex items-center <?php echo $current_page === 'food-order.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white'; ?> transition-colors">
                        <i class="fas fa-utensils w-5"></i>
                        <span class="ml-3">Food Order</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="facilities.php" class="flex items-center <?php echo $current_page === 'facilities.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white'; ?> transition-colors">
                        <i class="fas fa-concierge-bell w-5"></i>
                        <span class="ml-3">Facilities</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="profile.php" class="flex items-center <?php echo $current_page === 'profile.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white'; ?> transition-colors">
                        <i class="fas fa-user w-5"></i>
                        <span class="ml-3">My Profile</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="about.php" class="flex items-center <?php echo $current_page === 'about.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white'; ?> transition-colors">
                        <i class="fas fa-info-circle w-5"></i>
                        <span class="ml-3">About Us</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="contact.php" class="flex items-center <?php echo $current_page === 'contact.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white'; ?> transition-colors">
                        <i class="fas fa-envelope w-5"></i>
                        <span class="ml-3">Contact</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="taxi-guide.php" class="flex items-center <?php echo $current_page === 'taxi-guide.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white'; ?> transition-colors">
                        <i class="fas fa-taxi w-5"></i>
                        <span class="ml-3">Taxi & Guide</span>
                    </a>
                </li>
                <li class="px-6 py-3 mt-auto">
                    <a href="../logout.php" class="flex items-center text-red-400 hover:text-red-300 transition-colors">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3">Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-64">
            <!-- Top Header -->
            <header class="bg-white shadow-sm sticky top-0 z-40 lg:z-50">
                <div class="flex items-center justify-between px-6 py-4">
                    <h1 class="text-xl font-bold text-gray-900 font-playfair">
                        <?php
                        switch($current_page) {
                            case 'dashboard.php': echo 'Dashboard'; break;
                            case 'room-booking.php': echo 'Room Booking'; break;
                            case 'food-order.php': echo 'Food Order'; break;
                            case 'facilities.php': echo 'Facilities'; break;
                            case 'profile.php': echo 'My Profile'; break;
                            case 'about.php': echo 'About Us'; break;
                            case 'contact.php': echo 'Contact'; break;
                            case 'taxi-guide.php': echo 'Taxi & Guide'; break;
                            default: echo 'Dashboard'; break;
                        }
                        ?>
                    </h1>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-500">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=random" 
                             alt="Profile" 
                             class="w-8 h-8 rounded-full">
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <!-- Your page content goes here -->
            </main>
        </div>
    </div>

    <script>
        // Mobile sidebar functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Close sidebar on window resize if screen is large
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        });
    </script>
</body>
</html>