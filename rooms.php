<?php
session_start();
require_once 'config/database.php';

$room_type = isset($_GET['type']) ? $_GET['type'] : '';

// Get room details with facilities
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms & Suites - The Royal Grand Colombo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .room-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('images/rooms-bg.jpg');
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
            transition: transform 0.3s ease;
        }

        .room-card:hover {
            transform: translateY(-5px);
        }

        .room-image {
            height: 400px;
            object-fit: cover;
        }

        .facility-icon {
            font-size: 1.2rem;
            color: #c8a97e;
            margin-right: 10px;
        }

        .price-tag {
            font-size: 1.5rem;
            color: #c8a97e;
            font-weight: 600;
        }

        .booking-form {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            position: sticky;
            top: 100px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Room Header -->
    <div class="room-header text-center">
        <div class="container">
            <h1 class="display-4">Rooms & Suites</h1>
            <p class="lead">Experience luxury and comfort in our carefully designed accommodations</p>
        </div>
    </div>

    <!-- Room Content -->
    <div class="container">
        <div class="row">
            <!-- Room Details -->
            <div class="col-lg-8">
                <?php if ($rooms->num_rows > 0): ?>
                    <?php while ($room = $rooms->fetch_assoc()): 
                        $facilities = explode(',', $room['facilities']);
                        $facility_icons = explode(',', $room['facility_icons']);
                    ?>
                        <div class="room-card">
                            <img src="images/<?php echo strtolower(str_replace(' ', '-', $room['room_type'])); ?>.jpg" 
                                 class="img-fluid room-image" alt="<?php echo htmlspecialchars($room['room_type']); ?>">
                            <div class="p-4">
                                <h2 class="mb-3"><?php echo htmlspecialchars($room['room_type']); ?></h2>
                                <p class="lead mb-4"><?php echo htmlspecialchars($room['description']); ?></p>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Room Features</h5>
                                        <ul class="list-unstyled">
                                            <?php for ($i = 0; $i < count($facilities); $i++): ?>
                                                <li class="mb-2">
                                                    <i class="<?php echo htmlspecialchars($facility_icons[$i]); ?> facility-icon"></i>
                                                    <?php echo htmlspecialchars($facilities[$i]); ?>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Room Details</h5>
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="fas fa-bed facility-icon"></i>Room Number: <?php echo htmlspecialchars($room['room_number']); ?></li>
                                            <li class="mb-2"><i class="fas fa-building facility-icon"></i>Floor: <?php echo htmlspecialchars($room['floor_number']); ?></li>
                                            <li class="mb-2"><i class="fas fa-users facility-icon"></i>Capacity: <?php echo htmlspecialchars($room['capacity']); ?> persons</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price-tag">
                                        $<?php echo number_format($room['price_per_night'], 2); ?> per night
                                    </div>
                                    <a href="book.php?type=<?php echo urlencode($room['room_type']); ?>" class="btn btn-primary">Book Now</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        No rooms of this type are currently available.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Booking Form -->
            <div class="col-lg-4">
                <div class="booking-form">
                    <h3 class="mb-4">Quick Booking</h3>
                    <form action="book.php" method="GET">
                        <input type="hidden" name="type" value="<?php echo htmlspecialchars($room_type); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Check-in Date</label>
                            <input type="date" class="form-control" name="check_in" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Check-out Date</label>
                            <input type="date" class="form-control" name="check_out" required 
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Check Availability</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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