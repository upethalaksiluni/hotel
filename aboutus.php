<?php
session_start();
$current_page = 'about.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - The Royal Grand Colombo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .about-hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('images/about-bg.jpg');
            background-size: cover;
            background-position: center;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }
        
        .value-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .value-card:hover {
            transform: translateY(-5px);
        }

        .timeline-item {
            border-left: 2px solid #c8a97e;
            padding-left: 20px;
            margin-bottom: 30px;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #c8a97e;
        }

        .stats-section {
            background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('images/stats-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
        }

        .stat-item {
            text-align: center;
            padding: 2rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: #c8a97e;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Include Header/Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section with Background Video -->
    <section class="about-hero position-relative" style="height: 60vh; overflow: hidden;">
        <!-- Background Video -->
        <video autoplay loop muted playsinline class="w-100 h-100 position-absolute top-0 start-0 object-fit-cover" style="z-index:1; min-width:100%; min-height:100%; object-fit:cover;">
            <source src="./video/indexvideo.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <!-- Overlay -->
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.6); z-index:2;"></div>
        <!-- Content -->
        <div class="container position-relative" style="z-index:3;">
            <h1 class="display-3 fw-bold text-white">About Us</h1>
            <p class="lead text-white">Discover the story behind The Royal Grand Colombo</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <h2 class="display-4 mb-4">Our Story</h2>
                    <p class="lead mb-4">Founded in 1988, The Royal Grand Colombo stands as an icon of luxury and hospitality in the heart of Sri Lanka's capital.</p>
                    <p class="mb-4">With over three decades of excellence, we have been providing unforgettable experiences to guests from around the world, combining traditional Sri Lankan hospitality with modern luxury.</p>
                </div>
                <div class="col-lg-6">
                    <img src="images/about.webp" alt="Hotel History" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Our Values</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <i class="fas fa-star fa-2x text-warning mb-3"></i>
                        <h3>Excellence</h3>
                        <p>We strive for excellence in every detail of our service and facilities.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <i class="fas fa-heart fa-2x text-danger mb-3"></i>
                        <h3>Hospitality</h3>
                        <p>Warm, genuine care for our guests is at the heart of everything we do.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <i class="fas fa-leaf fa-2x text-success mb-3"></i>
                        <h3>Sustainability</h3>
                        <p>We are committed to sustainable practices and environmental responsibility.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">35+</div>
                        <div>Years of Excellence</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">250</div>
                        <div>Luxury Rooms</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">5</div>
                        <div>Restaurants</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">1M+</div>
                        <div>Happy Guests</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>