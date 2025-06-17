<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$room_type = isset($_GET['type']) ? $_GET['type'] : '';
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';

// Get room details
$room_query = "SELECT r.*, GROUP_CONCAT(f.name) as facilities, GROUP_CONCAT(f.icon) as facility_icons
               FROM rooms r 
               LEFT JOIN room_facility_mapping rfm ON r.id = rfm.room_id 
               LEFT JOIN room_facilities f ON rfm.facility_id = f.id 
               WHERE r.room_type = ? AND r.status = 'available'
               GROUP BY r.id";
$stmt = $conn->prepare($room_query);
$stmt->bind_param("s", $room_type);
$stmt->execute();
$rooms = $stmt->get_result();

// Check availability if dates are provided
$available_rooms = [];
if ($check_in && $check_out) {
    while ($room = $rooms->fetch_assoc()) {
        // Check if room is available for the selected dates
        $availability_query = "SELECT COUNT(*) as count FROM reservations 
                             WHERE room_id = ? AND status != 'cancelled'
                             AND ((check_in_date <= ? AND check_out_date > ?) 
                             OR (check_in_date < ? AND check_out_date >= ?)
                             OR (check_in_date >= ? AND check_out_date <= ?))";
        $stmt = $conn->prepare($availability_query);
        $stmt->bind_param("issssss", $room['id'], $check_out, $check_in, $check_out, $check_in, $check_in, $check_out);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result['count'] == 0) {
            $available_rooms[] = $room;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room - The Royal Grand Colombo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .booking-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/booking-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }

        .room-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .room-image {
            height: 300px;
            object-fit: cover;
        }

        .booking-form {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
    </style>
</head>
<body>
    <!-- Include your navigation here -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Booking Header -->
    <div class="booking-header text-center">
        <div class="container">
            <h1 class="display-4">Book Your Stay</h1>
            <p class="lead">Experience luxury and comfort at The Royal Grand Colombo</p>
        </div>
    </div>

    <!-- Booking Content -->
    <div class="container">
        <div class="row">
            <!-- Room Details -->
            <div class="col-md-8">
                <?php if ($rooms->num_rows > 0): ?>
                    <?php 
                    $room = $rooms->fetch_assoc();
                    $facilities = explode(',', $room['facilities']);
                    $facility_icons = explode(',', $room['facility_icons']);
                    ?>
                    <div class="room-card">
                        <img src="images/<?php echo strtolower(str_replace(' ', '-', $room_type)); ?>.jpg" 
                             class="img-fluid room-image" alt="<?php echo htmlspecialchars($room_type); ?>">
                        <div class="p-4">
                            <h2><?php echo htmlspecialchars($room_type); ?></h2>
                            <p class="lead"><?php echo htmlspecialchars($room['description']); ?></p>
                            <div class="row mb-4">
                                <?php for ($i = 0; $i < count($facilities); $i++): ?>
                                    <div class="col-md-6 mb-2">
                                        <i class="<?php echo htmlspecialchars($facility_icons[$i]); ?> me-2"></i>
                                        <?php echo htmlspecialchars($facilities[$i]); ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <p class="h4 mb-0">$<?php echo number_format($room['price_per_night'], 2); ?> per night</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        No rooms of this type are currently available.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Booking Form -->
            <div class="col-md-4">
                <div class="booking-form">
                    <h3 class="mb-4">Check Availability</h3>
                    <form action="process_booking.php" method="POST">
                        <input type="hidden" name="room_type" value="<?php echo htmlspecialchars($room_type); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Check-in Date</label>
                            <input type="date" class="form-control" name="check_in" 
                                   value="<?php echo $check_in; ?>" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Check-out Date</label>
                            <input type="date" class="form-control" name="check_out" 
                                   value="<?php echo $check_out; ?>" required 
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>

                        <?php if ($check_in && $check_out): ?>
                            <?php if (count($available_rooms) > 0): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Rooms are available for your selected dates!
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Proceed to Book</button>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    Sorry, no rooms available for these dates.
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <button type="submit" class="btn btn-primary w-100">Check Availability</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set minimum dates for check-in and check-out
        const today = new Date().toISOString().split('T')[0];
        const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0];
        
        document.querySelector('input[name="check_in"]').min = today;
        document.querySelector('input[name="check_out"]').min = tomorrow;

        // Update check-out minimum date when check-in changes
        document.querySelector('input[name="check_in"]').addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            const nextDay = new Date(checkInDate.getTime() + 86400000).toISOString().split('T')[0];
            document.querySelector('input[name="check_out"]').min = nextDay;
        });
    </script>
</body>
</html> 