<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facility Management - The Royal Grand</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .font-playfair {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Mobile Header -->
    <header class="lg:hidden bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="flex items-center justify-between px-4 py-3">
            <button id="sidebarToggle" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <h1 class="text-lg font-semibold text-gray-900 font-playfair">The Royal Grand</h1>
            <div class="w-10"></div> <!-- Spacer for centering -->
        </div>
    </header>

    <!-- Sidebar Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <nav id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <h3 class="text-xl font-bold font-playfair">Royal Grand Admin</h3>
                <button id="closeSidebar" class="lg:hidden p-1 rounded text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Navigation Menu -->
            <div class="flex flex-col h-full">
                <ul class="flex-1 py-4 space-y-1">
                    <li>
                        <a href="index.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors duration-200">
                            <i class="fas fa-home w-5 text-center mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="rooms.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors duration-200">
                            <i class="fas fa-bed w-5 text-center mr-3"></i>
                            <span>Rooms</span>
                        </a>
                    </li>
                    <li>
                        <a href="facilities.php" class="flex items-center px-6 py-3 bg-gray-800 text-white transition-colors">
                            <i class="fas fa-concierge-bell w-5 text-center mr-3"></i>
                            <span>Facilities</span>
                        </a>
                    </li>
                    <li>
                        <a href="users.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors duration-200">
                            <i class="fas fa-users w-5 text-center mr-3"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="../logout.php" class="flex items-center px-6 py-3 text-red-400 hover:text-red-300 transition-colors mt-auto">
                            <i class="fas fa-sign-out-alt w-5 text-center mr-3"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-6">
            <div class="px-4 sm:px-6 lg:px-8 py-2 lg:py-6">
                <!-- Page Header -->
                <div class="mb-6">
                    <div class="sm:flex sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 font-playfair">Facility Management</h1>
                            <p class="mt-1 text-sm text-gray-600">Manage room facilities and amenities for your hotel</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <button onclick="showAddModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                <span class="hidden sm:inline">Add New Facility</span>
                                <span class="sm:hidden">Add Facility</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-concierge-bell text-blue-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-600">Total Facilities</h3>
                                <p class="text-2xl font-bold text-gray-900">8</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-600">Active Facilities</h3>
                                <p class="text-2xl font-bold text-gray-900">8</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-600">Premium Facilities</h3>
                                <p class="text-2xl font-bold text-gray-900">3</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-600">Usage Rate</h3>
                                <p class="text-2xl font-bold text-gray-900">94%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Facilities Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <h2 class="text-lg font-semibold text-gray-900">Facilities List</h2>
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <input type="text" placeholder="Search facilities..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>
                                <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <option>All Categories</option>
                                    <option>Amenities</option>
                                    <option>Technology</option>
                                    <option>Services</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facility</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Icon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">Free WiFi</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-wifi text-blue-500 text-lg mr-2"></i>
                                            <span class="text-xs text-gray-500">fas fa-wifi</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">High-speed wireless internet access</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="editFacility(1)" class="text-blue-600 hover:text-blue-900 mr-3 transition-colors duration-200">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteFacility(1)" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">Air Conditioning</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-snowflake text-blue-500 text-lg mr-2"></i>
                                            <span class="text-xs text-gray-500">fas fa-snowflake</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">Climate controlled environment</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="editFacility(2)" class="text-blue-600 hover:text-blue-900 mr-3 transition-colors duration-200">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteFacility(2)" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">Room Service</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-concierge-bell text-yellow-500 text-lg mr-2"></i>
                                            <span class="text-xs text-gray-500">fas fa-concierge-bell</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">24/7 room service available</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="editFacility(3)" class="text-blue-600 hover:text-blue-900 mr-3 transition-colors duration-200">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteFacility(3)" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden divide-y divide-gray-200">
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-wifi text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Free WiFi</p>
                                        <p class="text-sm text-gray-500 truncate">High-speed wireless internet access</p>
                                        <div class="mt-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editFacility(1)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button onclick="deleteFacility(1)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-snowflake text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Air Conditioning</p>
                                        <p class="text-sm text-gray-500 truncate">Climate controlled environment</p>
                                        <div class="mt-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editFacility(2)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button onclick="deleteFacility(2)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-concierge-bell text-yellow-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Room Service</p>
                                        <p class="text-sm text-gray-500 truncate">24/7 room service available</p>
                                        <div class="mt-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editFacility(3)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button onclick="deleteFacility(3)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Showing <span class="font-medium">1</span> to <span class="font-medium">3</span> of <span class="font-medium">8</span> results
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 text-sm border border-gray-300 rounded-md text-gray-500 hover:bg-gray-50 disabled:opacity-50" disabled>
                                    Previous
                                </button>
                                <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    1
                                </button>
                                <button class="px-3 py-1 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                    2
                                </button>
                                <button class="px-3 py-1 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Modal -->
    <div id="facilityModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-md mx-auto max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 font-playfair">Add New Facility</h3>
                    <button onclick="closeModal()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                        <i class="fas fa-times text-gray-400"></i>
                    </button>
                </div>
            </div>
            
            <form id="facilityForm" class="p-6 space-y-6">
                <input type="hidden" name="action" id="formAction">
                <input type="hidden" name="facility_id" id="facilityId">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Facility Name</label>
                    <input type="text" name="name" id="facilityName" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                           placeholder="Enter facility name">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Icon Class</label>
                    <div class="relative">
                        <input type="text" name="icon" id="facilityIcon" required 
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="e.g., fas fa-wifi">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                            <i id="iconPreview" class="text-gray-400"></i>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Use FontAwesome icon classes</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="facilityDescription" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                              placeholder="Enter facility description"></textarea>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button type="button" onclick="closeModal()" 
                            class="flex-1 px-4 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mobile sidebar functionality
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const closeSidebar = document.getElementById('closeSidebar');

        function toggleSidebar() {
            sidebar.classList.toggle('translate-x-0');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }

        sidebarToggle?.addEventListener('click', toggleSidebar);
        closeSidebar?.addEventListener('click', toggleSidebar);
        overlay?.addEventListener('click', toggleSidebar);

        // Close sidebar on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });

        // Modal functionality
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Facility';
            document.getElementById('formAction').value = 'add';
            document.getElementById('facilityId').value = '';
            document.getElementById('facilityName').value = '';
            document.getElementById('facilityIcon').value = '';
            document.getElementById('facilityDescription').value = '';
            document.getElementById('iconPreview').className = 'text-gray-400';
            document.getElementById('facilityModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            document.getElementById('facilityModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function editFacility(id) {
            // Simulate fetching facility data
            const facilityData = {
                1: { name: 'Free WiFi', icon: 'fas fa-wifi', description: 'High-speed wireless internet access' },
                2: { name: 'Air Conditioning', icon: 'fas fa-snowflake', description: 'Climate controlled environment' },
                3: { name: 'Room Service', icon: 'fas fa-concierge-bell', description: '24/7 room service available' }
            };

            const facility = facilityData[id];
            if (facility) {
                document.getElementById('modalTitle').textContent = 'Edit Facility';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('facilityId').value = id;
                document.getElementById('facilityName').value = facility.name;
                document.getElementById('facilityIcon').value = facility.icon;
                document.getElementById('facilityDescription').value = facility.description;
                document.getElementById('iconPreview').className = facility.icon;
                document.getElementById('facilityModal').classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                alert('Failed to load facility data');
            }
        }

        function deleteFacility(id) {
            if (confirm('Are you sure you want to delete this facility?')) {
                // Simulate deletion
                alert('Facility deleted successfully');
                // In real implementation: window.location.href = `process_facility.php?action=delete&id=${id}`;
            }
        }

        // Icon preview functionality
        document.getElementById('facilityIcon').addEventListener('input', function(e) {
            const iconClass = e.target.value.trim();
            const preview = document.getElementById('iconPreview');
            if (iconClass) {
                preview.className = iconClass;
            } else {
                preview.className = 'text-gray-400';
            }
        });

        // Close modal when clicking outside
        document.getElementById('facilityModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Handle form submission
        document.getElementById('facilityForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = formData.get('action');
            
            // Simulate form submission
            if (action === 'add') {
                alert('Facility added successfully');
            } else {
                alert('Facility updated successfully');
            }
            
            closeModal();
            // In real implementation, submit to server
            // fetch('process_facility.php', { method: 'POST', body: formData })
        });

        // Search functionality
        const searchInput = document.querySelector('input[placeholder="Search facilities..."]');
        searchInput?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            // Implement search logic here
            console.log('Searching for:', searchTerm);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // ESC to close modal
            if (e.key === 'Escape') {
                closeModal();
            }
            
            // Ctrl/Cmd + N to add new facility
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                showAddModal();
            }
        });

        // Initialize tooltips or other components if needed
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Facility Management page loaded');
        });
    </script>
</body>
</html>