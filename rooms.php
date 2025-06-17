<?php
session_start();
require_once 'config/database.php';

$room_type = isset($_GET['type']) ? $_GET['type'] : '';

// Update the room query to properly handle room types
$room_query = "SELECT r.*, GROUP_CONCAT(f.name) as facilities, GROUP_CONCAT(f.icon) as facility_icons
               FROM rooms r 
               LEFT JOIN room_facility_mapping rfm ON r.id = rfm.room_id 
               LEFT JOIN room_facilities f ON rfm.facility_id = f.id";
               
if (!empty($room_type)) {
    $room_query .= " WHERE r.room_type = ? AND r.status = 'available'";
} else {
    $room_query .= " WHERE r.status = 'available'";
}

$room_query .= " GROUP BY r.id";

$stmt = $conn->prepare($room_query);
if (!empty($room_type)) {
    $stmt->bind_param("s", $room_type);
}
$stmt->execute();
$rooms = $stmt->get_result();

function uploadRoomImage($file, $room_type) {
    $target_dir = "../images/";
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = strtolower(str_replace(' ', '-', $room_type)) . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if file is an actual image
    if (getimagesize($file["tmp_name"])) {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $new_filename;
        }
    }
    return false;
}
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
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

        .hero-slide {
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        .slide-content {
            transition: all 0.6s ease 0.3s;
        }
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Slider -->
    <div x-data="heroSlider()" class="relative overflow-hidden" @mouseenter="autoplay = false" @mouseleave="autoplay = true">
        <div class="relative h-[80vh] min-h-[500px]">
            <template x-for="(slide, index) in slides" :key="index">
                <div 
                    x-show="currentSlide === index"
                    x-transition.opacity.duration.800ms
                    class="absolute inset-0"
                >
                    <div class="absolute inset-0 bg-gray-800">
                        <img 
                            :src="slide.image" 
                            :alt="slide.title"
                            class="w-full h-full object-cover opacity-80"
                            @error="replaceBrokenImage($event)"
                            loading="lazy"
                        >
                    </div>
                    
                    <div class="container mx-auto px-6 h-full flex items-center">
                        <div 
                            class="max-w-2xl text-white slide-content"
                            :class="{
                                'translate-x-0 opacity-100': currentSlide === index,
                                'translate-x-10 opacity-0': currentSlide !== index
                            }"
                        >
                            <h2 x-text="slide.title" class="text-4xl md:text-5xl font-bold mb-4"></h2>
                            <p x-text="slide.description" class="text-xl md:text-2xl mb-8"></p>
                            <a 
                                :href="slide.buttonUrl" 
                                class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg text-lg font-semibold transition-colors fade-in"
                                x-text="slide.buttonText"
                            ></a>
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Navigation buttons -->
            <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white rounded-full w-10 h-10 md:w-12 md:h-12 flex items-center justify-center z-10 transition-all">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white rounded-full w-10 h-10 md:w-12 md:h-12 flex items-center justify-center z-10 transition-all">
                <i class="fas fa-chevron-right"></i>
            </button>
            
            <!-- Indicators -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex space-x-2 z-10">
                <template x-for="(slide, index) in slides" :key="index">
                    <button
                        @click="goTo(index)"
                        class="w-2 h-2 md:w-3 md:h-3 rounded-full transition-all"
                        :class="{
                            'bg-white w-4 md:w-6': currentSlide === index,
                            'bg-white/50': currentSlide !== index
                        }"
                    ></button>
                </template>
            </div>
        </div>
    </div>

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