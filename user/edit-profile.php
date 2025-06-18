<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate inputs
    if (empty($name) || empty($email)) {
        $error_msg = "Name and email are required.";
    } elseif ($new_password !== $confirm_password) {
        $error_msg = "Passwords do not match.";
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            if (!empty($new_password)) {
                // Update with new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $email, $hashed_password, $user_id);
            } else {
                // Update without password
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $name, $email, $user_id);
            }
            
            if ($stmt->execute()) {
                $_SESSION['user_name'] = $name;
                $success_msg = "Profile updated successfully!";
                $conn->commit();
            } else {
                throw new Exception("Failed to update profile.");
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error_msg = $e->getMessage();
        }
    }
}

// Get user data
$stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - The Royal Grand</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Back to Dashboard Link -->
        <a href="dashboard.php" class="inline-flex items-center text-gray-600 hover:text-gray-800 mb-6">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Dashboard
        </a>

        <!-- Profile Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-gray-900 text-white p-6">
                <h1 class="text-2xl font-bold">Edit Profile</h1>
                <p class="text-gray-300 mt-1">Update your personal information</p>
            </div>

            <!-- Form -->
            <form action="" method="POST" class="p-6 space-y-6">
                <?php if ($success_msg): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        <?php echo $success_msg; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error_msg): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo htmlspecialchars($user['name']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>

                <!-- New Password Field -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" id="new_password" name="new_password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Leave blank to keep current password">
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Leave blank to keep current password">
                </div>

                <!-- Role Field (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <input type="text" value="<?php echo ucfirst($user['role']); ?>" 
                           class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-md" 
                           readonly>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-4">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password matching validation
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const form = document.querySelector('form');

        form.addEventListener('submit', function(e) {
            if (newPasswordInput.value !== confirmPasswordInput.value) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });

        // Success message auto-hide
        const successMessage = document.querySelector('.bg-green-100');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.opacity = '0';
                setTimeout(() => {
                    successMessage.remove();
                }, 300);
            }, 3000);
        }
    </script>
</body>
</html>