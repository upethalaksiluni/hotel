<?php
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = isset($is_admin) ? $is_admin : false;
?>
<!-- Top Bar -->
<div class="top-bar bg-dark text-white py-2">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span class="me-3"><i class="fas fa-phone me-2"></i>+1 234 567 8900</span>
                <span><i class="fas fa-envelope me-2"></i>info@shangrila.com</span>
            </div>
            <div class="col-md-6 text-end">
                <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="<?php echo $is_admin ? '../' : ''; ?>./images/logo.webp" alt="The Royal Grand Colombo" height="50">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" 
                       href="<?php echo $is_admin ? '../' : ''; ?>index.php">HOME</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'about.php' ? 'active' : ''; ?>" href="about.php">ABOUT</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo $current_page === 'rooms.php' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        ROOMS & SUITES
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="rooms.php?type=deluxe">Deluxe Lake View</a></li>
                        <li><a class="dropdown-item" href="rooms.php?type=premier">Premier Ocean View</a></li>
                        <li><a class="dropdown-item" href="rooms.php?type=executive">Executive Suite</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo $current_page === 'dining.php' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        DINING
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="dining.php#restaurant1">Lakeside Restaurant</a></li>
                        <li><a class="dropdown-item" href="dining.php#restaurant2">Ocean View Cafe</a></li>
                        <li><a class="dropdown-item" href="dining.php#restaurant3">Garden Terrace</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo $current_page === 'experience.php' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        EXPERIENCE
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="experience.php#spa">Spa & Wellness</a></li>
                        <li><a class="dropdown-item" href="experience.php#pool">Swimming Pool</a></li>
                        <li><a class="dropdown-item" href="experience.php#fitness">Fitness Center</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'contact.php' ? 'active' : ''; ?>" href="contact.php">CONTACT</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="admin/index.php">Admin Dashboard</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="./dashboard.php">My Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">LOGIN</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
.top-bar {
    font-size: 0.9rem;
}
.navbar {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.navbar-brand img {
    max-height: 50px;
}
.nav-link {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.9rem;
    padding: 1rem 1.2rem !important;
    color: #333 !important;
}
.nav-link:hover, .nav-link.active {
    color: #c8a97e !important;
}
.dropdown-menu {
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}
.dropdown-item {
    font-family: 'Montserrat', sans-serif;
    font-size: 0.9rem;
    padding: 0.7rem 1.5rem;
}
.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #c8a97e;
}
</style>