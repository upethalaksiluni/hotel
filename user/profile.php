<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle profile update
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    // Optional: password change
    $password = trim($_POST['password']);
    $update_query = '';
    if ($password !== '') {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $update_query = "UPDATE users SET name=?, email=?, password=? WHERE id=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('sssi', $name, $email, $hashed_password, $user_id);
    } else {
        $update_query = "UPDATE users SET name=?, email=? WHERE id=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('ssi', $name, $email, $user_id);
    }
    if ($stmt->execute()) {
        $success = 'Profile updated successfully!';
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
    } else {
        $error = 'Failed to update profile. Please try again.';
    }
}

// Fetch user info
$stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id=?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $role);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - The Royal Grand Colombo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; background: #f8f9fa; }
        .profile-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            padding: 40px 30px;
        }
        .profile-title {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: #c8a97e;
            margin-bottom: 30px;
        }
        .form-label { font-weight: 500; }
        .btn-primary { background: #c8a97e; border: none; }
        .btn-primary:hover { background: #a07c54; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container">
        <div class="profile-container">
            <h2 class="profile-title"><i class="fas fa-user-circle me-2"></i>User Profile</h2>
            <?php if ($success): ?>
                <div class="alert alert-success"> <?php echo $success; ?> </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"> <?php echo $error; ?> </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password <span class="text-muted" style="font-size:0.9em;">(leave blank to keep current)</span></label>
                    <input type="password" class="form-control" name="password" placeholder="••••••••">
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(ucfirst($role)); ?>" disabled>
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
                <a href="dashboard.php" class="btn btn-outline-secondary ms-2">Back to Dashboard</a>
            </form>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 