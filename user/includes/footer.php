<footer class="bg-gray-900 text-gray-300 mt-auto py-8 px-6">
    <div class="max-w-7xl mx-auto">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- About Section -->
            <div>
                <h3 class="text-lg font-bold font-playfair text-white mb-4">The Royal Grand</h3>
                <p class="text-sm leading-relaxed mb-4">
                    Experience luxury redefined at The Royal Grand. Our commitment to excellence ensures 
                    your stay is nothing short of extraordinary, with world-class amenities and impeccable service.
                </p>
                <!-- Social Links -->
                <div class="flex space-x-4">
                    <a href="#" class="hover:text-blue-500 transition-colors">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="hover:text-pink-500 transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="hover:text-blue-400 transition-colors">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="hover:text-blue-700 transition-colors">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="dashboard.php" class="hover:text-white transition-colors flex items-center">
                            <i class="fas fa-home w-5"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="room-booking.php" class="hover:text-white transition-colors flex items-center">
                            <i class="fas fa-bed w-5"></i>
                            <span>Room Booking</span>
                        </a>
                    </li>
                    <li>
                        <a href="food-order.php" class="hover:text-white transition-colors flex items-center">
                            <i class="fas fa-utensils w-5"></i>
                            <span>Food Order</span>
                        </a>
                    </li>
                    <li>
                        <a href="facilities.php" class="hover:text-white transition-colors flex items-center">
                            <i class="fas fa-concierge-bell w-5"></i>
                            <span>Facilities</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Additional Links -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">More Info</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="profile.php" class="hover:text-white transition-colors flex items-center">
                            <i class="fas fa-user w-5"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="about.php" class="hover:text-white transition-colors flex items-center">
                            <i class="fas fa-info-circle w-5"></i>
                            <span>About Us</span>
                        </a>
                    </li>
                    <li>
                        <a href="contact.php" class="hover:text-white transition-colors flex items-center">
                            <i class="fas fa-envelope w-5"></i>
                            <span>Contact</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Contact Us</h4>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt w-5 mt-1"></i>
                        <span class="ml-2">123 Royal Street, Colombo 03,<br>Sri Lanka</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-phone w-5"></i>
                        <span class="ml-2">+94 11 234 5678</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-envelope w-5"></i>
                        <span class="ml-2">info@royalgrand.com</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-clock w-5"></i>
                        <span class="ml-2">24/7 Support Available</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 pt-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm mb-4 md:mb-0">
                    &copy; <?php echo date('Y'); ?> The Royal Grand. All rights reserved.
                </p>
                <div class="flex space-x-4 text-sm">
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                    <a href="#" class="hover:text-white transition-colors">Cookie Policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
    // Add active state to current page link in footer
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = window.location.pathname.split('/').pop();
        const footerLinks = document.querySelectorAll('footer a');
        
        footerLinks.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('text-white');
                link.classList.add('font-medium');
            }
        });
    });
</script>