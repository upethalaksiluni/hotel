<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get all users except current admin
$users_query = "SELECT * FROM users WHERE id != ? ORDER BY created_at DESC";
$stmt = $conn->prepare($users_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$users = $stmt->get_result();

// Get user statistics
$total_users = $users->num_rows;
$admin_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'")->fetch_assoc()['count'];
$regular_users = $total_users - $admin_count + 1; // Adding 1 to include current admin
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - The Royal Grand Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                <h1 class="text-xl font-bold">Admin Dashboard</h1>
            </div>
            
            <ul class="mt-6">
                <li class="px-6 py-3">
                    <a href="index.php" class="flex items-center text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="rooms.php" class="flex items-center text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-bed w-5"></i>
                        <span class="ml-3">Rooms</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="facilities.php" class="flex items-center text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-concierge-bell w-5"></i>
                        <span class="ml-3">Facilities</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="users.php" class="flex items-center bg-gray-800 text-white transition-colors">
                        <i class="fas fa-users w-5"></i>
                        <span class="ml-3">Users</span>
                    </a>
                </li>
                <li class="px-6 py-3">
                    <a href="../logout.php" class="flex items-center text-red-400 hover:text-red-300 transition-colors">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3">Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-64 p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
                    <p class="text-gray-600 mt-1">Manage system users and their roles</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-700">Total Users</h3>
                                <p class="text-2xl font-bold text-gray-900"><?php echo $total_users; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-user text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-700">Regular Users</h3>
                                <p class="text-2xl font-bold text-gray-900"><?php echo $regular_users; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-user-shield text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-700">Admins</h3>
                                <p class="text-2xl font-bold text-gray-900"><?php echo $admin_count; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-800">Users List</h2>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Role</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Created At</th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php while ($user = $users->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td class="px-6 py-4">
                                        <button onclick="viewUser(<?php echo $user['id']; ?>)" 
                                                class="text-blue-600 hover:text-blue-800 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="deleteUser(<?php echo $user['id']; ?>)" 
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
    </div>

    <!-- View User Modal -->
    <div id="viewUserModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-md w-full">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-900">User Details</h3>
                        <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6" id="userDetails">
                    <!-- User details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // View user details
        function viewUser(userId) {
            fetch(`get_user.php?id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('userDetails').innerHTML = `
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <p class="mt-1 text-gray-900">${data.user.name}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-gray-900">${data.user.email}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Role</label>
                                    <p class="mt-1">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium ${
                                            data.user.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'
                                        }">
                                            ${data.user.role.charAt(0).toUpperCase() + data.user.role.slice(1)}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Joined Date</label>
                                    <p class="mt-1 text-gray-900">${new Date(data.user.created_at).toLocaleDateString()}</p>
                                </div>
                            </div>
                        `;
                        document.getElementById('viewUserModal').classList.remove('hidden');
                    } else {
                        alert('Error loading user details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading user details');
                });
        }

        function closeViewModal() {
            document.getElementById('viewUserModal').classList.add('hidden');
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                fetch('delete_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.error || 'Failed to delete user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete user');
                });
            }
        }

        // Close modal when clicking outside
        document.getElementById('viewUserModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeViewModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeViewModal();
            }
        });
    </script>
</body>
</html>