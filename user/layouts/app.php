<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'User Dashboard'; ?> - The Royal Grand</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
    </style>
    <?php echo $styles ?? ''; ?>
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
                    <a href="dashboard.php" class="flex items-center text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-home w-5"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="room-booking.php" class="flex items-center text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-bed w-5"></i>
                        <span class="ml-3">Room Booking</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="food-order.php" class="flex items-center text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-utensils w-5"></i>
                        <span class="ml-3">Food Order</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="facilities.php" class="flex items-center text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-concierge-bell w-5"></i>
                        <span class="ml-3">Facilities</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="profile.php" class="flex items-center text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-user w-5"></i>
                        <span class="ml-3">My Profile</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="about.php" class="flex items-center text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-info-circle w-5"></i>
                        <span class="ml-3">About Us</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="contact.php" class="flex items-center text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-envelope w-5"></i>
                        <span class="ml-3">Contact</span>
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
            <header class="bg-white shadow-sm sticky top-0 z-40">
                <div class="flex items-center justify-between px-6 py-4">
                    <h1 class="text-xl font-bold text-gray-900 font-playfair">
                        <?php echo $header ?? 'Dashboard'; ?>
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
                <?php echo $content ?? ''; ?>
            </main>

            <!-- Footer -->
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script>
        // Mobile sidebar functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        });

        <?php echo $scripts ?? ''; ?>
    </script>
</body>
</html>