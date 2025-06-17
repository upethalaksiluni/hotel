<?php
session_start();
require_once 'config/database.php';

// Get unique room types with their details
$rooms_query = "SELECT DISTINCT r.room_type, r.price_per_night, r.description, 
                GROUP_CONCAT(DISTINCT f.name) as facilities,
                GROUP_CONCAT(DISTINCT f.icon) as facility_icons
                FROM rooms r 
                LEFT JOIN room_facility_mapping rfm ON r.id = rfm.room_id 
                LEFT JOIN room_facilities f ON rfm.facility_id = f.id 
                WHERE r.status = 'available'
                GROUP BY r.room_type";
$rooms_result = $conn->query($rooms_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Royal Grand Colombo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <span><i class="fas fa-phone me-2"></i>+94 11 7 888 288</span>
                    <span class="ms-3"><i class="fas fa-envelope me-2"></i>info@The Royal Grand.com</span>
                </div>
                <div class="col-md-6 text-end">
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="The Royal Grand Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">HOME</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">ABOUT</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">ROOMS & SUITES</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="rooms.php">Deluxe Lake View</a></li>
                            <li><a class="dropdown-item" href="rooms.php">Premier Ocean View</a></li>
                            <li><a class="dropdown-item" href="rooms.php">Executive Suite</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">DINING</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="dining.php">Café on the Water</a></li>
                            <li><a class="dropdown-item" href="dining.php">Shang Palace</a></li>
                            <li><a class="dropdown-item" href="dining.php">Capital Bar & Grill</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">EXPERIENCE</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="experience.php">Spa & Wellness</a></li>
                            <li><a class="dropdown-item" href="experience.php">Fitness Center</a></li>
                            <li><a class="dropdown-item" href="experience.php">Swimming Pool</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">CONTACT</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if ($_SESSION['user_role'] == 'admin'): ?>
                                    <li><a class="dropdown-item" href="admin/index.php">Admin Dashboard</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="./user/profile.php">My Profile</a></li>
                                <li><a class="dropdown-item" href="my-reservations.php">My Reservations</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-primary ms-2" href="login.php">LOGIN</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

     <!-- Hero Section -->
     <section class="hero-section">
        <div class="hero-content text-center text-white">
            <h1 class="display-1 fw-bold">The Royal Grand Colombo</h1>
            <p class="lead">A personal tropical sanctuary set within the heart of the city</p>
            <a href="#offers" class="btn btn-light btn-lg mt-3">View Offers</a>
        </div>
    </section>

     <!-- Quick Booking -->
     <section class="booking-bar py-4 bg-white shadow-sm">
        <div class="container">
            <form class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Hotel, City or Destination">
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option>Corporate / Special Rate</option>
                        <option>Corporate</option>
                        <option>Group</option>
                        <option>Travel Agency</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Offers Section -->
    <section id="offers" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Special Offers</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card offer-card">
                        <img src="images/offer1.jpg" class="card-img-top" alt="Eat Play Love">
                        <div class="card-body">
                            <h5 class="card-title">Eat Play Love with The Royal Grand</h5>
                            <p class="card-text">One stay. Three paths. Pick your pleasure in Colombo this summer.</p>
                            <p class="price">From USD 227 Average Per Night</p>
                            <a href="#" class="btn btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card offer-card">
                        <img src="images/offer2.jpg" class="card-img-top" alt="Member Exclusive">
                        <div class="card-body">
                            <h5 class="card-title">Members Online Exclusive Rate</h5>
                            <p class="card-text">Unlock exclusive member rates at The Royal Grand Colombo.</p>
                            <p class="price">From USD 160 Average Per Night</p>
                            <a href="#" class="btn btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card offer-card">
                        <img src="images/offer3.jpg" class="card-img-top" alt="Dine & Save">
                        <div class="card-body">
                            <h5 class="card-title">Dine & Save with The Royal Grand Circle</h5>
                            <p class="card-text">Savour 20% savings at our signature dining venues.</p>
                            <a href="#" class="btn btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>About The Hotel</h2>
                    <p>A precious jewel strung along the Indian Ocean overlooking the historic Galle Face Green, The Royal Grand Colombo celebrates the best of the city. Find sanctuary in our expansive rooms that blend modern sophistication with stunning views.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle me-2"></i>541 rooms, suites and serviced apartments</li>
                        <li><i class="fas fa-check-circle me-2"></i>7 restaurants & bars</li>
                        <li><i class="fas fa-check-circle me-2"></i>Direct access to One Galle Face Mall</li>
                    </ul>
                    <a href="#" class="btn btn-primary mt-3">Learn More</a>
                </div>
                <div class="col-md-6">
                    <img src="images/about.jpg" class="img-fluid rounded" alt="Hotel Exterior">
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms & Suites Section -->
    <section class="rooms-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Rooms & Suites</h2>
            <div class="row">
                <?php while ($room = $rooms_result->fetch_assoc()): 
                    $facilities = explode(',', $room['facilities']);
                    $facility_icons = explode(',', $room['facility_icons']);
                ?>
                <div class="col-md-4 mb-4">
                    <div class="room-card">
                        <img src="images/<?php echo strtolower(str_replace(' ', '-', $room['room_type'])); ?>.jpg" class="img-fluid rounded" alt="<?php echo htmlspecialchars($room['room_type']); ?>">
                        <div class="room-content p-4">
                            <h4><?php echo htmlspecialchars($room['room_type']); ?></h4>
                            <p><?php echo htmlspecialchars($room['description']); ?></p>
                            <ul class="list-unstyled">
                                <?php for ($i = 0; $i < min(3, count($facilities)); $i++): ?>
                                <li><i class="<?php echo htmlspecialchars($facility_icons[$i]); ?> me-2"></i><?php echo htmlspecialchars($facilities[$i]); ?></li>
                                <?php endfor; ?>
                            </ul>
                            <p class="price mt-3">From USD <?php echo number_format($room['price_per_night'], 2); ?> per night</p>
                            <a href="rooms.php?type=<?php echo urlencode($room['room_type']); ?>" class="btn btn-outline-primary mt-3">Book Now</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Dining Section -->
    <section class="dining-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Dining Experiences</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="restaurant-card">
                        <img src="images/shang-palace.jpg" class="img-fluid rounded" alt="Shang Palace">
                        <div class="restaurant-content p-4">
                            <h4>Shang Palace</h4>
                            <p class="cuisine">Authentic Cantonese Cuisine</p>
                            <p>Experience the finest Cantonese cuisine in an elegant setting with stunning views of the Indian Ocean.</p>
                            <div class="restaurant-info">
                                <p><i class="fas fa-clock me-2"></i>Daily: 12:00 PM - 2:30 PM, 6:30 PM - 10:30 PM</p>
                                <p><i class="fas fa-phone me-2"></i>+94 11 788 8888</p>
                            </div>
                            <a href="#" class="btn btn-outline-primary mt-3">View Menu</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="restaurant-card">
                        <img src="images/capital-bar.jpg" class="img-fluid rounded" alt="Capital Bar & Grill">
                        <div class="restaurant-content p-4">
                            <h4>Capital Bar & Grill</h4>
                            <p class="cuisine">Steakhouse & Seafood</p>
                            <p>Indulge in premium cuts of meat and fresh seafood in a sophisticated atmosphere.</p>
                            <div class="restaurant-info">
                                <p><i class="fas fa-clock me-2"></i>Daily: 6:30 PM - 10:30 PM</p>
                                <p><i class="fas fa-phone me-2"></i>+94 11 788 8888</p>
                            </div>
                            <a href="#" class="btn btn-outline-primary mt-3">View Menu</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Experience Section -->
    <section class="experience-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Experience</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="experience-card">
                        <img src="images/spa.jpg" class="img-fluid rounded" alt="Chi, The Spa">
                        <div class="experience-content p-4">
                            <h4>Chi, The Spa</h4>
                            <p>Rejuvenate your body and mind with our signature treatments inspired by traditional Asian healing philosophies.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check me-2"></i>Massage therapies</li>
                                <li><i class="fas fa-check me-2"></i>Facial treatments</li>
                                <li><i class="fas fa-check me-2"></i>Body rituals</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary mt-3">View Treatments</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="experience-card">
                        <img src="images/fitness.jpg" class="img-fluid rounded" alt="Health Club">
                        <div class="experience-content p-4">
                            <h4>Health Club</h4>
                            <p>Stay active during your stay with our state-of-the-art fitness facilities and professional trainers.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check me-2"></i>24-hour gym</li>
                                <li><i class="fas fa-check me-2"></i>Swimming pool</li>
                                <li><i class="fas fa-check me-2"></i>Personal training</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary mt-3">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="experience-card">
                        <img src="images/kids.jpg" class="img-fluid rounded" alt="Adventure Zone">
                        <div class="experience-content p-4">
                            <h4>Adventure Zone</h4>
                            <p>Keep your little ones entertained with our exciting kids' club activities and facilities.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check me-2"></i>Indoor playground</li>
                                <li><i class="fas fa-check me-2"></i>Supervised activities</li>
                                <li><i class="fas fa-check me-2"></i>Kids' menu</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary mt-3">View Activities</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>Find & Book</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Our Destinations</a></li>
                        <li><a href="#" class="text-white">Find a Reservation</a></li>
                        <li><a href="#" class="text-white">Meetings & Events</a></li>
                        <li><a href="#" class="text-white">Restaurant & Bars</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>The Royal Grand Circle</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Programme Overview</a></li>
                        <li><a href="#" class="text-white">Join The Royal Grand Circle</a></li>
                        <li><a href="#" class="text-white">Account Overview</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>About The Royal Grand</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">About Us</a></li>
                        <li><a href="#" class="text-white">Our Hotel Brands</a></li>
                        <li><a href="#" class="text-white">Careers</a></li>
                        <li><a href="#" class="text-white">News</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact Us</h5>
                    <p>The Royal Grand Colombo<br>
                    1 Galle Face, Colombo 2<br>
                    Sri Lanka</p>
                    <p>Tel: +94 11 788 8888<br>
                    Email: slcm@The Royal Grand.com</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 The Royal Grand International Hotel Management Ltd. All Rights Reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-white me-3">Privacy Policy</a>
                    <a href="#" class="text-white me-3">Terms & Conditions</a>
                    <a href="#" class="text-white">Safety & Security</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
</html> 