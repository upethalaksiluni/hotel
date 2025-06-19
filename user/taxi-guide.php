<?php
// taxi-guide.php - Main taxi/tour guide booking page
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'shangrila_db'; // Using existing database
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper functions
function getTaxiServices($pdo) {
    // Using rooms table as taxi services
    $stmt = $pdo->query("
        SELECT r.*, 
               CASE 
                   WHEN r.room_type = 'Deluxe Lake View' THEN 'City Tour'
                   WHEN r.room_type = 'Premier Ocean View' THEN 'Airport Transfer'
                   WHEN r.room_type = 'Executive Suite' THEN 'Premium Tour'
                   ELSE r.room_type
               END as service_name,
               CASE 
                   WHEN r.room_type = 'Deluxe Lake View' THEN 'Explore the beautiful city attractions with our experienced guides'
                   WHEN r.room_type = 'Premier Ocean View' THEN 'Comfortable and reliable airport transfer service'
                   WHEN r.room_type = 'Executive Suite' THEN 'Luxury tour experience with premium vehicles and expert guides'
                   ELSE r.description
               END as service_description,
               r.price_per_night as price_per_hour
        FROM rooms r 
        WHERE r.status = 'available' 
        ORDER BY r.room_type
    ");
    return $stmt->fetchAll();
}

function getTaxiGuides($pdo) {
    // Using users table as guides
    $stmt = $pdo->query("
        SELECT u.*, 
               CASE 
                   WHEN u.role = 'admin' THEN 'Senior Guide & Driver'
                   ELSE 'Professional Guide & Driver'
               END as specialty,
               CASE 
                   WHEN u.role = 'admin' THEN 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face'
                   ELSE 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face'
               END as photo_url,
               CASE 
                   WHEN u.role = 'admin' THEN 4.9
                   ELSE 4.7
               END as rating,
               CASE 
                   WHEN u.role = 'admin' THEN 8
                   ELSE 5
               END as experience
        FROM users u 
        ORDER BY u.role DESC, u.name
    ");
    return $stmt->fetchAll();
}

function getServiceFacilities($pdo, $serviceId) {
    // Using room facilities as service features
    $stmt = $pdo->prepare("
        SELECT rf.* 
        FROM room_facilities rf
        JOIN room_facility_mapping rfm ON rf.id = rfm.facility_id
        WHERE rfm.room_id = ?
    ");
    $stmt->execute([$serviceId]);
    return $stmt->fetchAll();
}

function processBooking($pdo, $data) {
    try {
        // Validate required fields
        $required = ['guest_name', 'guest_email', 'guest_phone', 'service_id', 'pickup_date', 'pickup_time'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => 'Please fill in all required fields.'];
            }
        }

        // Validate email
        if (!filter_var($data['guest_email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please enter a valid email address.'];
        }

        // Validate date
        $pickupDate = DateTime::createFromFormat('Y-m-d', $data['pickup_date']);
        if (!$pickupDate || $pickupDate < new DateTime()) {
            return ['success' => false, 'message' => 'Please select a valid future date.'];
        }

        // Get service details
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ? AND status = 'available'");
        $stmt->execute([$data['service_id']]);
        $service = $stmt->fetch();

        if (!$service) {
            return ['success' => false, 'message' => 'Invalid service selected.'];
        }

        // Calculate total price (assuming hourly rate)
        $hours = intval($data['duration_hours'] ?? 4);
        $totalPrice = $service['price_per_night'] * $hours;

        // Insert booking using reservations table
        $stmt = $pdo->prepare("
            INSERT INTO reservations (
                room_id, user_id, check_in_date, check_out_date, 
                guest_name, guest_email, guest_phone, total_price, 
                status, special_requests, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())
        ");

        $endDate = clone $pickupDate;
        $endDate->add(new DateInterval('PT' . $hours . 'H'));

        $stmt->execute([
            $data['service_id'],
            $_SESSION['user_id'] ?? null,
            $data['pickup_date'],
            $endDate->format('Y-m-d'),
            $data['guest_name'],
            $data['guest_email'],
            $data['guest_phone'],
            $totalPrice,
            $data['special_requests'] ?? ''
        ]);

        $bookingId = $pdo->lastInsertId();

        // Send confirmation email
        sendBookingConfirmationEmail($data, $service, $bookingId, $totalPrice);

        return [
            'success' => true, 
            'message' => "Thank you! Your booking has been received. Booking ID: {$bookingId}. We'll contact you within 24 hours to confirm your taxi/tour service."
        ];

    } catch (Exception $e) {
        error_log("Booking error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Sorry, there was an error processing your booking. Please try again.'];
    }
}

function sendBookingConfirmationEmail($data, $service, $bookingId, $totalPrice) {
    $to = $data['guest_email'];
    $subject = "Taxi/Tour Booking Confirmation - Booking #{$bookingId}";
    $message = "
        Dear {$data['guest_name']},
        
        Thank you for booking with our Taxi/Tour Service!
        
        Booking Details:
        - Booking ID: {$bookingId}
        - Service: {$service['room_type']}
        - Pickup Date: {$data['pickup_date']}
        - Pickup Time: {$data['pickup_time']}
        - Duration: {$data['duration_hours']} hours
        - Total Price: $" . number_format($totalPrice) . "
        
        We'll contact you within 24 hours to confirm your booking.
        
        Best regards,
        Taxi/Tour Service Team
    ";
    
    $headers = "From: noreply@taxitour.com\r\n";
    $headers .= "Reply-To: info@taxitour.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // mail($to, $subject, $message, $headers);
}

// Get services and guides
$services = getTaxiServices($pdo);
$guides = getTaxiGuides($pdo);

// Handle form submission
if ($_POST['action'] ?? '' === 'book_service') {
    $result = processBooking($pdo, $_POST);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
}

$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Taxi & Tour Guide Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f59e0b',
                        secondary: '#3b82f6',
                        accent: '#10b981'
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 50%, #dc2626 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .floating {
            animation: floating 4s ease-in-out infinite;
        }
        @keyframes floating {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
        }
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(245, 158, 11, 0.5); }
            50% { box-shadow: 0 0 40px rgba(245, 158, 11, 0.8); }
        }
        .alert-success {
            background-color: #d1fae5;
            border-color: #a7f3d0;
            color: #065f46;
        }
        .alert-error {
            background-color: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .service-icon {
            background: linear-gradient(135deg, #f59e0b, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="gradient-bg text-white shadow-2xl">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="text-3xl font-bold flex items-center">
                    <i class="fas fa-taxi mr-3 floating"></i>
                    TaxiGuide Pro
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#services" class="hover:text-yellow-200 transition-all duration-300 font-medium">Services</a>
                    <a href="#guides" class="hover:text-yellow-200 transition-all duration-300 font-medium">Our Guides</a>
                    <a href="#booking" class="hover:text-yellow-200 transition-all duration-300 font-medium">Book Now</a>
                    <a href="#contact" class="hover:text-yellow-200 transition-all duration-300 font-medium">Contact</a>
                </div>
                <button class="md:hidden text-2xl" id="mobile-menu-button">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <!-- Mobile menu -->
            <div class="md:hidden hidden mt-4" id="mobile-menu">
                <div class="flex flex-col space-y-2">
                    <a href="#services" class="block px-3 py-2 hover:text-yellow-200 transition">Services</a>
                    <a href="#guides" class="block px-3 py-2 hover:text-yellow-200 transition">Our Guides</a>
                    <a href="#booking" class="block px-3 py-2 hover:text-yellow-200 transition">Book Now</a>
                    <a href="#contact" class="block px-3 py-2 hover:text-yellow-200 transition">Contact</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Display messages -->
    <?php if (isset($message)): ?>
    <div class="container mx-auto px-6 mt-4">
        <div class="alert-<?php echo $messageType; ?> border px-6 py-4 rounded-lg relative">
            <span class="block sm:inline font-medium"><?php echo htmlspecialchars($message); ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <div class="gradient-bg text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="container mx-auto px-6 py-20 text-center relative z-10">
            <h1 class="text-6xl md:text-7xl font-bold mb-6 floating">
                Your Journey, <span class="text-yellow-300">Our Expertise</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto leading-relaxed">
                Experience Sri Lanka with professional taxi drivers and expert tour guides. 
                Safe, reliable, and unforgettable journeys await you.
            </p>
            <a href="#booking" class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-bold py-4 px-10 rounded-full text-xl transition transform hover:scale-105 pulse-glow">
                <i class="fas fa-calendar-check mr-2"></i>
                Book Your Ride Now
            </a>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-gray-50 to-transparent"></div>
    </div>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold mb-4 text-gray-800">Our Premium Services</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Choose from our range of professional transportation and tour services</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($services as $service): ?>
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover service-card border border-gray-100" data-service="<?php echo $service['id']; ?>">
                    <div class="p-8">
                        <div class="text-center mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-<?php echo $service['room_type'] === 'Deluxe Lake View' ? 'map-marked-alt' : ($service['room_type'] === 'Premier Ocean View' ? 'plane-departure' : 'crown'); ?> text-3xl text-white"></i>
                            </div>
                            <h3 class="text-2xl font-bold mb-2 text-gray-800">
                                <?php 
                                if ($service['room_type'] === 'Deluxe Lake View') echo 'City Tour';
                                elseif ($service['room_type'] === 'Premier Ocean View') echo 'Airport Transfer';
                                else echo 'Premium Tour';
                                ?>
                            </h3>
                        </div>
                        <p class="text-gray-600 mb-6 text-center leading-relaxed">
                            <?php 
                            if ($service['room_type'] === 'Deluxe Lake View') echo 'Explore the beautiful city attractions with our experienced guides';
                            elseif ($service['room_type'] === 'Premier Ocean View') echo 'Comfortable and reliable airport transfer service';
                            else echo 'Luxury tour experience with premium vehicles and expert guides';
                            ?>
                        </p>
                        
                        <!-- Service Features -->
                        <div class="mb-6">
                            <h4 class="font-semibold mb-3 text-gray-800">Included Features:</h4>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <?php 
                                $facilities = getServiceFacilities($pdo, $service['id']);
                                foreach (array_slice($facilities, 0, 4) as $facility): 
                                ?>
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span class="text-gray-600"><?php echo htmlspecialchars($facility['name']); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div class="text-left">
                                <span class="text-3xl font-bold text-primary">$<?php echo number_format($service['price_per_night']); ?></span>
                                <span class="text-gray-500">/hour</span>
                            </div>
                            <button class="bg-gradient-to-r from-primary to-orange-500 text-white px-6 py-3 rounded-full hover:from-orange-500 hover:to-red-500 transition-all duration-300 transform hover:scale-105 select-service font-semibold" 
                                    data-service-id="<?php echo $service['id']; ?>" 
                                    data-service-name="<?php echo $service['room_type']; ?>" 
                                    data-service-price="<?php echo $service['price_per_night']; ?>">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Select Service
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Guides Section -->
    <section id="guides" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold mb-4 text-gray-800">Meet Our Expert Team</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Professional drivers and knowledgeable guides ready to serve you</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-<?php echo min(count($guides), 3); ?> gap-8">
                <?php foreach ($guides as $guide): ?>
                <div class="text-center bg-white rounded-2xl shadow-lg p-8 card-hover">
                    <div class="relative mb-6">
                        <img src="<?php echo htmlspecialchars($guide['photo_url']); ?>" 
                             alt="<?php echo htmlspecialchars($guide['name']); ?>" 
                             class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-yellow-400 shadow-lg">
                        <div class="absolute -bottom-2 -right-2 bg-green-500 w-8 h-8 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold mb-2 text-gray-800"><?php echo htmlspecialchars($guide['name']); ?></h3>
                    <p class="text-gray-600 mb-4 font-medium"><?php echo htmlspecialchars($guide['specialty']); ?></p>
                    <div class="flex justify-center items-center mb-4">
                        <div class="flex text-yellow-500 text-lg">
                            <?php
                            $rating = $guide['rating'];
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $rating ? '★' : '☆';
                            }
                            ?>
                        </div>
                        <span class="ml-2 text-gray-600 font-medium">(<?php echo number_format($guide['rating'], 1); ?>/5)</span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-medal text-yellow-500 mr-2"></i>
                            <?php echo $guide['experience']; ?>+ years of experience
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Booking Form Section -->
    <section id="booking" class="py-20 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold mb-4 text-gray-800">Book Your Service</h2>
                <p class="text-xl text-gray-600">Fill out the form below to reserve your taxi or tour service</p>
            </div>
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl p-8 md:p-12">
                <form id="bookingForm" method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="book_service">
                    
                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-user mr-2 text-primary"></i>Full Name *
                            </label>
                            <input type="text" name="guest_name" id="guestName" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                                   required placeholder="Enter your full name">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-envelope mr-2 text-primary"></i>Email Address *
                            </label>
                            <input type="email" name="guest_email" id="guestEmail" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                                   required placeholder="Enter your email">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-phone mr-2 text-primary"></i>Phone Number *
                            </label>
                            <input type="tel" name="guest_phone" id="guestPhone" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                                   required placeholder="Enter your phone number">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-users mr-2 text-primary"></i>Number of Passengers
                            </label>
                            <select name="num_passengers" id="numPassengers" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                                <option value="1">1 Passenger</option>
                                <option value="2">2 Passengers</option>
                                <option value="3">3 Passengers</option>
                                <option value="4">4 Passengers</option>
                                <option value="5">5+ Passengers</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Service Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-car mr-2 text-primary"></i>Service Type *
                            </label>
                            <select name="service_id" id="serviceType" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                                    required>
                                <option value="">Select a service</option>
                                <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['id']; ?>" data-price="<?php echo $service['price_per_night']; ?>">
                                    <?php 
                                    if ($service['room_type'] === 'Deluxe Lake View') echo 'City Tour';
                                    elseif ($service['room_type'] === 'Premier Ocean View') echo 'Airport Transfer';
                                    else echo 'Premium Tour';
                                    ?> - $<?php echo number_format($service['price_per_night']); ?>/hour
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-clock mr-2 text-primary"></i>Duration (Hours)
                            </label>
                            <select name="duration_hours" id="durationHours" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                                <option value="2">2 Hours</option>
                                <option value="4" selected>4 Hours</option>
                                <option value="6">6 Hours</option>
                                <option value="8">8 Hours (Full Day)</option>
                                <option value="12">12 Hours</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-calendar mr-2 text-primary"></i>Pickup Date *
                            </label>
                            <input type="date" name="pickup_date" id="pickupDate" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                                   required min="<?php echo $today; ?>">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-clock mr-2 text-primary"></i>Pickup Time *
                            </label>
                            <select name="pickup_time" id="pickupTime" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                                    required>
                                <option value="">Select time</option>
                                <option value="06:00">6:00 AM</option>
                                <option value="07:00">7:00 AM</option>
                                <option value="08:00">8:00 AM</option>
                                <option value="09:00">9:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="13:00">1:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="17:00">5:00 PM</option>
                                <option value="18:00">6:00 PM</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-primary"></i>Pickup Location *
                        </label>
                        <input type="text" name="pickup_location" id="pickupLocation" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                               required placeholder="Enter pickup address or location">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-primary"></i>Destination (Optional)
                        </label>
                        <input type="text" name="destination" id="destination" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                               placeholder="Enter destination (if known)">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-comment mr-2 text-primary"></i>Special Requests
                        </label>
                        <textarea name="special_requests" id="specialRequests" rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                                  placeholder="Any special requirements, preferred route, or additional information..."></textarea>
                    </div>
                    
                    <!-- Price Display -->
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg p-6 border border-yellow-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800 mb-2">Estimated Total:</h4>
                                <p class="text-sm text-gray-600">Price may vary based on actual distance and time</p>
                            </div>
                            <div class="text-right">
                                <span class="text-3xl font-bold text-primary" id="totalPrice">$0</span>
                                <p class="text-sm text-gray-600">+ applicable taxes</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms and Conditions -->
                    <div class="flex items-start">
                        <input type="checkbox" id="terms" name="terms" required 
                               class="mt-1 mr-3 w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <label for="terms" class="text-sm text-gray-700">
                            I agree to the <a href="#" class="text-primary hover:underline">Terms and Conditions</a> 
                            and <a href="#" class="text-primary hover:underline">Privacy Policy</a>. 
                            I understand that booking confirmation will be sent via email and SMS.
                        </label>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" 
                                class="bg-gradient-to-r from-primary to-orange-500 text-white font-bold py-4 px-12 rounded-full text-lg hover:from-orange-500 hover:to-red-500 transition-all duration-300 transform hover:scale-105 pulse-glow">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold mb-4 text-gray-800">Why Choose Us</h2>
                <p class="text-xl text-gray-600">Experience the difference with our premium service</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Safe & Secure</h3>
                    <p class="text-gray-600">Licensed drivers with clean driving records and fully insured vehicles</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Always On Time</h3>
                    <p class="text-gray-600">Punctual service with real-time tracking and updates</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-star text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">5-Star Service</h3>
                    <p class="text-gray-600">Exceptional customer service with 98% satisfaction rate</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-r from-red-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dollar-sign text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Fair Pricing</h3>
                    <p class="text-gray-600">Transparent pricing with no hidden fees or surprises</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-800 text-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold mb-4">Get In Touch</h2>
                <p class="text-xl text-gray-300">Ready to book or have questions? Contact us now!</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-phone text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Call Us</h3>
                    <p class="text-gray-300">+94 77 123 4567</p>
                    <p class="text-gray-300">Available 24/7</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Email Us</h3>
                    <p class="text-gray-300">info@taxiguide.lk</p>
                    <p class="text-gray-300">Response within 2 hours</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Visit Us</h3>
                    <p class="text-gray-300">123 Main Street</p>
                    <p class="text-gray-300">Colombo, Sri Lanka</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="text-2xl font-bold mb-4 flex items-center">
                        <i class="fas fa-taxi mr-2 text-primary"></i>
                        TaxiGuide Pro
                    </div>
                    <p class="text-gray-400 mb-4">Your trusted partner for safe and comfortable transportation in Sri Lanka.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-primary hover:text-yellow-400 transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-primary hover:text-yellow-400 transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-primary hover:text-yellow-400 transition"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-primary hover:text-yellow-400 transition"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Services</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Airport Transfer</a></li>
                        <li><a href="#" class="hover:text-white transition">City Tours</a></li>
                        <li><a href="#" class="hover:text-white transition">Premium Tours</a></li>
                        <li><a href="#" class="hover:text-white transition">Corporate Travel</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition">Our Fleet</a></li>
                        <li><a href="#" class="hover:text-white transition">Safety Policy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Newsletter</h4>
                    <p class="text-gray-400 mb-4">Subscribe for special offers and updates</p>
                    <div class="flex">
                        <input type="email" placeholder="Your email" 
                               class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <button class="bg-primary px-4 py-2 rounded-r-lg hover:bg-yellow-600 transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 TaxiGuide Pro. All rights reserved. | Designed with ❤️ for Sri Lanka</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Service selection from cards
        document.querySelectorAll('.select-service').forEach(button => {
            button.addEventListener('click', function() {
                const serviceId = this.getAttribute('data-service-id');
                const serviceName = this.getAttribute('data-service-name');
                const servicePrice = this.getAttribute('data-service-price');
                
                // Update form
                document.getElementById('serviceType').value = serviceId;
                
                // Scroll to booking form
                document.getElementById('booking').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Update price calculation
                updateTotalPrice();
                
                // Highlight selected service
                document.querySelectorAll('.service-card').forEach(card => {
                    card.classList.remove('ring-2', 'ring-primary');
                });
                this.closest('.service-card').classList.add('ring-2', 'ring-primary');
            });
        });

        // Price calculation
        function updateTotalPrice() {
            const serviceSelect = document.getElementById('serviceType');
            const durationSelect = document.getElementById('durationHours');
            const totalPriceElement = document.getElementById('totalPrice');
            
            if (serviceSelect.value && durationSelect.value) {
                const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
                const pricePerHour = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                const hours = parseInt(durationSelect.value) || 0;
                const total = pricePerHour * hours;
                
                totalPriceElement.textContent = '$' + total.toLocaleString();
            } else {
                totalPriceElement.textContent = '$0';
            }
        }

        // Update price when service or duration changes
        document.getElementById('serviceType').addEventListener('change', updateTotalPrice);
        document.getElementById('durationHours').addEventListener('change', updateTotalPrice);

        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const requiredFields = ['guest_name', 'guest_email', 'guest_phone', 'service_id', 'pickup_date', 'pickup_time', 'pickup_location'];
            let isValid = true;
            
            requiredFields.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            // Check terms checkbox
            const termsCheckbox = document.getElementById('terms');
            if (!termsCheckbox.checked) {
                alert('Please accept the terms and conditions to proceed.');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });

        // Set minimum date to today
        document.getElementById('pickupDate').min = new Date().toISOString().split('T')[0];

        // Auto-format phone number
        document.getElementById('guestPhone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 10) {
                value = value.substring(0, 10);
                e.target.value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            }
        });

        // Email validation
        document.getElementById('guestEmail').addEventListener('blur', function(e) {
            const email = e.target.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                e.target.classList.add('border-red-500');
                e.target.setCustomValidity('Please enter a valid email address');
            } else {
                e.target.classList.remove('border-red-500');
                e.target.setCustomValidity('');
            }
        });

        // Initialize price calculation on page load
        updateTotalPrice();

        // Add loading state to form submission
        document.getElementById('bookingForm').addEventListener('submit', function() {
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            submitButton.disabled = true;
        });

        // Auto-hide messages after 5 seconds
        const alertMessages = document.querySelectorAll('[class*="alert-"]');
        alertMessages.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
    </script>
</body>
</html>