<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Fetch all facilities
$facilities_query = "SELECT * FROM room_facilities ORDER BY name";
$facilities_result = $conn->query($facilities_query);

// Get facility count
$count_query = "SELECT COUNT(*) as total FROM room_facilities";
$count_result = $conn->query($count_query);
$total_facilities = $count_result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facility Management - The Royal Grand</title>
    
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* Custom styles that complement Tailwind */
        .loading {
            display: none;
        }
        
        .loading.show {
            display: inline-block;
        }
        
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
            }
            
            #sidebar.show {
                transform: translateX(0);
            }
            
            .overlay {
                display: none;
                position: fixed;
                inset: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 40;
            }
            
            .overlay.show {
                display: block;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Mobile Toggle Button -->
    <button id="sidebarToggle" class="lg:hidden fixed top-4 left-4 z-50 bg-gray-900 text-white p-2 rounded-md">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-gray-900 text-white w-64 min-h-screen transition-transform duration-300 ease-in-out">
            <div class="p-4 border-b border-gray-800">
                <h3 class="text-xl font-bold text-center">The Royal Grand Admin</h3>
            </div>

            <ul class="py-4">
                <li>
                    <a href="index.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                        <i class="fas fa-home w-6"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="rooms.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                        <i class="fas fa-bed w-6"></i>
                        <span>Rooms</span>
                    </a>
                </li>
                <li>
                    <a href="facilities.php" class="flex items-center px-6 py-3 bg-gray-800 text-white transition-colors">
                        <i class="fas fa-concierge-bell w-6"></i>
                        <span>Facilities</span>
                    </a>
                </li>
                <li>
                    <a href="../logout.php" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors mt-auto">
                        <i class="fas fa-sign-out-alt w-6"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="flex-1 p-8 lg:ml-64">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Facility Management</h1>
                <p class="text-gray-600">Manage room facilities and amenities</p>
            </div>

            <!-- Stats Card -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-concierge-bell text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-700">Total Facilities</h3>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $total_facilities; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Facilities Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800">Facilities List</h2>
                        <button onclick="showAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Facility
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Icon</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php while ($facility = $facilities_result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <?php echo htmlspecialchars($facility['name']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <i class="<?php echo htmlspecialchars($facility['icon']); ?>"></i>
                                    (<?php echo htmlspecialchars($facility['icon']); ?>)
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo htmlspecialchars($facility['description']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="editFacility(<?php echo $facility['id']; ?>)" 
                                            class="text-blue-600 hover:text-blue-800 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteFacility(<?php echo $facility['id']; ?>)" 
                                            class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="facilityModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="p-6">
                <h3 id="modalTitle" class="text-xl font-semibold text-gray-900 mb-4"></h3>
                <form id="facilityForm" action="process_facility.php" method="POST">
                    <input type="hidden" name="action" id="formAction">
                    <input type="hidden" name="facility_id" id="facilityId">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Facility Name</label>
                        <input type="text" name="name" id="facilityName" required
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Icon Class</label>
                        <input type="text" name="icon" id="facilityIcon" required
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" id="facilityDescription" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()"
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Modal functions
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Facility';
            document.getElementById('formAction').value = 'add';
            document.getElementById('facilityId').value = '';
            document.getElementById('facilityName').value = '';
            document.getElementById('facilityIcon').value = '';
            document.getElementById('facilityDescription').value = '';
            document.getElementById('facilityModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('facilityModal').classList.add('hidden');
        }

        function editFacility(id) {
            fetch(`get_facility.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalTitle').textContent = 'Edit Facility';
                        document.getElementById('formAction').value = 'edit';
                        document.getElementById('facilityId').value = data.facility.id;
                        document.getElementById('facilityName').value = data.facility.name;
                        document.getElementById('facilityIcon').value = data.facility.icon;
                        document.getElementById('facilityDescription').value = data.facility.description;
                        document.getElementById('facilityModal').classList.remove('hidden');
                    } else {
                        alert('Failed to load facility data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load facility data');
                });
        }

        function deleteFacility(id) {
            if (confirm('Are you sure you want to delete this facility?')) {
                window.location.href = `process_facility.php?action=delete&id=${id}`;
            }
        }

        // Close modal when clicking outside
        document.getElementById('facilityModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Handle window resize for sidebar
        window.addEventListener('resize', function() {
            if (window.innerWidth > 1024) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    </script>
</body>
</html>