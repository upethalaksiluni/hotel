<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Royal Grand Colombo Colombo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* Custom CSS for Mobile Responsiveness */
        body {
            font-family: 'Montserrat', sans-serif;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
        }
        
        /* Top Bar Mobile Adjustments */
        .top-bar {
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            .top-bar .col-md-6:first-child {
                text-align: center !important;
                margin-bottom: 0.5rem;
            }
            
            .top-bar .col-md-6:last-child {
                text-align: center !important;
            }
            
            .top-bar .row {
                flex-direction: column;
            }
        }
        
        /* Navigation Mobile Improvements */
        .navbar-brand img {
            height: 40px;
        }
        
        @media (min-width: 992px) {
            .navbar-brand img {
                height: 50px;
            }
        }
        
        .navbar-nav .nav-link {
            padding: 0.5rem 1rem;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        @media (max-width: 991px) {
            .navbar-nav .nav-link {
                padding: 0.75rem 1rem;
                border-bottom: 1px solid #f8f9fa;
            }
            
            .navbar-nav .btn {
                margin: 0.5rem 1rem;
                text-align: center;
            }
        }
        
        /* Hero Section Responsive */
        .hero-section {
            position: relative;
            height: 60vh;
            min-height: 400px;
            overflow: hidden;
        }
        
        @media (min-width: 768px) {
            .hero-section {
                height: 80vh;
                min-height: 600px;
            }
        }
        
        .video-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .video-background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
        }
        
        /* Booking Bar Mobile */
        .booking-bar {
            padding: 1rem 0;
        }
        
        @media (max-width: 768px) {
            .booking-bar .row {
                --bs-gutter-x: 0.5rem;
            }
            
            .booking-bar .col-md-3,
            .booking-bar .col-md-2 {
                margin-bottom: 0.75rem;
            }
            
            .booking-bar .form-control,
            .booking-bar .form-select {
                font-size: 0.9rem;
                padding: 0.5rem 0.75rem;
            }
        }
        
        /* Card Improvements */
        .offer-card,
        .room-card,
        .restaurant-card,
        .experience-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .offer-card:hover,
        .room-card:hover,
        .restaurant-card:hover,
        .experience-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        
        @media (min-width: 768px) {
            .card-img-top {
                height: 250px;
            }
        }
        
        /* Room and Experience Cards */
        .room-content,
        .restaurant-content,
        .experience-content {
            background: white;
        }
        
        .room-card img,
        .restaurant-card img,
        .experience-card img {
            height: 200px;
            object-fit: cover;
        }
        
        @media (min-width: 768px) {
            .room-card img,
            .restaurant-card img,
            .experience-card img {
                height: 250px;
            }
        }
        
        /* Price Styling */
        .price {
            color: #007bff;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .cuisine {
            color: #6c757d;
            font-style: italic;
            margin-bottom: 0.5rem;
        }
        
        /* Button Improvements */
        .btn {
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #004085);
            transform: translateY(-2px);
        }
        
        .btn-outline-primary {
            border: 2px solid #007bff;
            color: #007bff;
        }
        
        .btn-outline-primary:hover {
            background: #007bff;
            border-color: #007bff;
            transform: translateY(-2px);
        }
        
        /* Section Spacing */
        .py-5 {
            padding: 3rem 0;
        }
        
        @media (max-width: 768px) {
            .py-5 {
                padding: 2rem 0;
            }
            
            .mb-5 {
                margin-bottom: 2rem !important;
            }
        }
        
        /* About Section Image */
        .about-section img {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Footer Mobile */
        footer {
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            footer .col-md-3 {
                margin-bottom: 2rem;
                text-align: center;
            }
            
            footer .row:last-child .col-md-6 {
                text-align: center !important;
                margin-bottom: 1rem;
            }
        }
        
        /* List Styling */
        .list-unstyled li {
            margin-bottom: 0.5rem;
        }
        
        .fas.fa-check,
        .fas.fa-check-circle {
            color: #28a745;
        }
        
        /* Restaurant Info */
        .restaurant-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        
        .restaurant-info p {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .restaurant-info p:last-child {
            margin-bottom: 0;
        }
        
        /* Responsive Text */
        @media (max-width: 576px) {
            h2 {
                font-size: 1.75rem;
            }
            
            h4 {
                font-size: 1.25rem;
            }
            
            .card-title {
                font-size: 1.1rem;
            }
            
            .btn {
                font-size: 0.9rem;
                padding: 0.4rem 1.2rem;
            }
        }
        
        /* Dropdown Menu Mobile */
        @media (max-width: 991px) {
            .dropdown-menu {
                border: none;
                box-shadow: none;
                background: #f8f9fa;
                margin-left: 1rem;
            }
            
            .dropdown-item {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }
        
        /* Sticky Navigation */
        .sticky-top {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Loading Animation */
        .card,
        .room-card,
        .restaurant-card,
        .experience-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Form Controls Mobile */
        @media (max-width: 576px) {
            .form-control,
            .form-select {
                font-size: 16px; /* Prevents zoom on iOS */
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar bg-dark text-white py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-12">
                    <small><i class="fas fa-phone me-2"></i>+94 11 788 8888</small>
                </div>
                <div class="col-md-6 col-12 text-md-end text-center">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="./images/logo.webp" alt="The Royal Grand Colombo Logo" class="img-fluid">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">ABOUT</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Overview</a></li>
                            <li><a class="dropdown-item" href="#">Services & Facilities</a></li>
                            <li><a class="dropdown-item" href="#">Map & Directions</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">ROOMS & SUITES</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Deluxe Lake View</a></li>
                            <li><a class="dropdown-item" href="#">Deluxe Ocean View</a></li>
                            <li><a class="dropdown-item" href="#">Premier Balcony</a></li>
                            <li><a class="dropdown-item" href="#">Executive Suites</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">DINING</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Shang Palace</a></li>
                            <li><a class="dropdown-item" href="#">Capital Bar & Grill</a></li>
                            <li><a class="dropdown-item" href="#">Central</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">EXPERIENCE</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">For Kids</a></li>
                            <li><a class="dropdown-item" href="#">Health & Leisure</a></li>
                            <li><a class="dropdown-item" href="#">Chi, The Spa</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">OFFERS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white ms-lg-2 mt-2 mt-lg-0" href="./user/room-booking.php">BOOK NOW</a>
                    </li>
                    <li class="nav-item">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <div class="dropdown">
                                <a class="nav-link btn btn-outline-primary ms-lg-2 mt-2 mt-lg-0 dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if($_SESSION['user_role'] === 'admin'): ?>
                                        <li><a class="dropdown-item" href="admin/dashboard.php">Admin Dashboard</a></li>
                                    <?php else: ?>
                                        <li><a class="dropdown-item" href="user/dashboard.php">My Dashboard</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a class="nav-link btn btn-outline-primary ms-lg-2 mt-2 mt-lg-0" href="login.php">LOGIN</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="video-background">
            <video autoplay muted loop playsinline id="heroVideo">
                <source src="./video/indexvideo.mp4" type="video/mp4">
            </video>
            <div class="video-overlay"></div>
        </div>
    </section>

    <!-- Quick Booking -->
    <section class="booking-bar py-4 bg-white shadow-sm">
        <div class="container">
            <form class="row g-3">
                <div class="col-lg-3 col-md-6 col-12">
                    <input type="text" class="form-control" placeholder="Hotel, City or Destination">
                </div>
                <div class="col-lg-2 col-md-6 col-12">
                    <select class="form-select">
                        <option>Corporate / Special Rate</option>
                        <option>Corporate</option>
                        <option>Group</option>
                        <option>Travel Agency</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 col-6">
                    <input type="date" class="form-control">
                </div>
                <div class="col-lg-2 col-md-6 col-6">
                    <input type="date" class="form-control">
                </div>
                <div class="col-lg-3 col-12">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Offers Section -->
    <section id="offers" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="card offer-card h-100">
                        <img src="images/room1.webp" class="card-img-top" alt="Eat Play Love">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Eat Play Love with The Royal Grand Colombo</h5>
                            <p class="card-text flex-grow-1">One stay. Three paths. Pick your pleasure in Colombo this summer.</p>
                            <p class="price">From USD 227 Average Per Night</p>
                            <a href="#" class="btn btn-outline-primary mt-auto">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="card offer-card h-100">
                        <img src="images/room2.webp" class="card-img-top" alt="Member Exclusive">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Members Online Exclusive Rate</h5>
                            <p class="card-text flex-grow-1">Unlock exclusive member rates at The Royal Grand Colombo Colombo.</p>
                            <p class="price">From USD 160 Average Per Night</p>
                            <a href="#" class="btn btn-outline-primary mt-auto">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="card offer-card h-100">
                        <img src="images/room3.webp" class="card-img-top" alt="Dine & Save">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Dine & Save with The Royal Grand Colombo Circle</h5>
                            <p class="card-text flex-grow-1">Savour 20% savings at our signature dining venues.</p>
                            <a href="#" class="btn btn-outline-primary mt-auto">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="text-center mb-5 mt-4">Special Offers</h2>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-12 mb-4 mb-lg-0">
                    <h2>About The Hotel</h2>
                    <p>A precious jewel strung along the Indian Ocean overlooking the historic Galle Face Green, The Royal Grand Colombo Colombo celebrates the best of the city. Find sanctuary in our expansive rooms that blend modern sophistication with stunning views.</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle me-2"></i>541 rooms, suites and serviced apartments</li>
                        <li><i class="fas fa-check-circle me-2"></i>7 restaurants & bars</li>
                        <li><i class="fas fa-check-circle me-2"></i>Direct access to One Galle Face Mall</li>
                    </ul>
                    <a href="#" class="btn btn-primary mt-3">Learn More</a>
                </div>
                <div class="col-lg-6 col-12">
                    <img src="images/about.webp" class="img-fluid rounded" alt="Hotel Exterior">
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms & Suites Section -->
    <section class="rooms-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Rooms & Suites</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="room-card h-100">
                        <img src="images/room1.webp" class="img-fluid rounded-top" alt="Deluxe Lake View">
                        <div class="room-content p-4">
                            <h4>Deluxe Lake View</h4>
                            <p>Spacious rooms with stunning views of Beira Lake and city skyline.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check me-2"></i>48 sqm / 517 sqft</li>
                                <li><i class="fas fa-check me-2"></i>King or Twin beds</li>
                                <li><i class="fas fa-check me-2"></i>Marble bathroom</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary mt-3">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="room-card h-100">
                        <img src="images/room2.webp" class="img-fluid rounded-top" alt="Premier Ocean View">
                        <div class="room-content p-4">
                            <h4>Premier Ocean View</h4>
                            <p>Luxurious rooms with panoramic views of the Indian Ocean.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check me-2"></i>55 sqm / 592 sqft</li>
                                <li><i class="fas fa-check me-2"></i>Private balcony</li>
                                <li><i class="fas fa-check me-2"></i>Separate shower</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary mt-3">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-4 mx-auto">
                    <div class="room-card h-100">
                        <img src="images/room3.webp" class="img-fluid rounded-top" alt="Executive Suite">
                        <div class="room-content p-4">
                            <h4>Executive Suite</h4>
                            <p>Elegant suite with separate living area and premium services.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check me-2"></i>85 sqm / 915 sqft</li>
                                <li><i class="fas fa-check me-2"></i>Horizon Club benefits</li>
                                <li><i class="fas fa-check me-2"></i>Butler service</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary mt-3">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dining Section -->
    <section class="dining-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Dining Experiences</h2>
            <div class="row">
                <div class="col-lg-6 col-12 mb-4">
                    <div class="restaurant-card h-100">
                        <img src="images/room4.webp" class="img-fluid rounded-top" alt="Shang Palace">
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
                <div class="col-lg-6 col-12 mb-4">
                    <div class="restaurant-card h-100">
                        <img src="images/room5.webp" class="img-fluid rounded-top" alt="Capital Bar & Grill">
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
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="experience-card h-100">
                        <img src="images/room3.webp" class="img-fluid rounded-top" alt="Chi, The Spa">
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
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="experience-card h-100">
                        <img src="images/gym.webp" class="img-fluid rounded-top" alt="Health Club">
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
                <div class="col-lg-4 col-md-6 col-12 mb-4 mx-auto">
                    <div class="experience-card h-100">
                        <img src="images/Adventure.webp" class="img-fluid rounded-top" alt="Adventure Zone">
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
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <h5>Find & Book</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">Our Destinations</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Find a Reservation</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Meetings & Events</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Restaurant & Bars</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <h5>The Royal Grand Colombo Circle</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">Programme Overview</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Join The Royal Grand Colombo Circle</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Account Overview</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <h5>About The Royal Grand Colombo</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">About Us</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Our Hotel Brands</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Careers</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">News</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <h5>Contact Us</h5>
                    <address class="mb-3">
                        <strong>The Royal Grand Colombo Colombo</strong><br>
                        1 Galle Face, Colombo 2<br>
                        Sri Lanka
                    </address>
                    <p class="mb-1">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:+94117888888" class="text-white-50 text-decoration-none">+94 11 788 8888</a>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:slcm@TheRoyalGrandColombo.com" class="text-white-50 text-decoration-none">slcm@TheRoyalGrandColombo.com</a>
                    </p>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="row align-items-center">
                <div class="col-lg-6 col-12 mb-3 mb-lg-0 text-center text-lg-start">
                    <p class="mb-0 small">&copy; 2024 The Royal Grand Colombo International Hotel Management Ltd. All Rights Reserved.</p>
                </div>
                <div class="col-lg-6 col-12 text-center text-lg-end">
                    <a href="#" class="text-white-50 text-decoration-none me-3 small">Privacy Policy</a>
                    <a href="#" class="text-white-50 text-decoration-none me-3 small">Terms & Conditions</a>
                    <a href="#" class="text-white-50 text-decoration-none small">Safety & Security</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript for Enhanced Mobile Experience -->
    <script>
        // Smooth scrolling for anchor links
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

        // Auto-collapse navbar on mobile after clicking
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                        toggle: false
                    });
                    bsCollapse.hide();
                }
            });
        });

        // Video optimization for mobile
        const video = document.getElementById('heroVideo');
        if (video) {
            // Pause video on mobile to save bandwidth
            if (window.innerWidth < 768 || 'ontouchstart' in window) {
                video.pause();
                video.style.display = 'none';
                // Add a fallback background image
                document.querySelector('.video-background').style.background = 
                    'linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url("images/room1.webp") center/cover';
            }
        }

        // Lazy loading for images (basic implementation)
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));

        // Add touch-friendly interactions
        const cards = document.querySelectorAll('.offer-card, .room-card, .restaurant-card, .experience-card');
        cards.forEach(card => {
            card.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            card.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });

        // Handle form inputs on mobile (prevent zoom on iOS)
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                if (window.innerWidth < 768) {
                    // Scroll input into view on mobile
                    setTimeout(() => {
                        this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 300);
                }
            });
        });

        // Performance optimization: Preload critical resources
        const preloadLinks = [
            './images/logo.webp',
            './images/room1.webp',
            './images/about.webp'
        ];

        preloadLinks.forEach(href => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = href;
            document.head.appendChild(link);
        });
    </script>
</body>
</html>