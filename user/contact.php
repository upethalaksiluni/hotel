<?php
session_start();
require_once '../config/database.php';

$title = 'Contact Us';
$header = 'Contact Us';

// Handle form submission
$message_sent = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Here you would typically send the email or save to database
        $message_sent = true;
        // Example: mail($to, $subject, $message, $headers);
    }
}

// Before the heredoc, set the variables safely:
$name_value = isset($name) ? $name : '';
$email_value = isset($email) ? $email : '';
$subject_value = isset($subject) ? $subject : '';
$message_value = isset($message) ? $message : '';

$content = <<<HTML
<div class="min-h-screen bg-gray-50 py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Main Contact Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 sm:mb-8">
            <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-6 sm:mb-8">Contact Us</h2>
                
                <!-- Display PHP Messages -->
HTML;

if ($message_sent) {
    $content .= '<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2 text-green-500"></i>
            <span class="text-sm sm:text-base">Thank you! Your message has been sent successfully.</span>
        </div>
    </div>';
}
if ($error_message) {
    $content .= '<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
            <span class="text-sm sm:text-base">' . $error_message . '</span>
        </div>
    </div>';
}

$content .= <<<HTML
                <form method="POST" class="space-y-6">
                    <!-- Name and Email Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" for="name">
                                Your Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name"
                                class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:px-4 sm:py-3 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="Enter your full name"
                                required value="{$name_value}">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" for="email">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email"
                                class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:px-4 sm:py-3 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="your.email@example.com"
                                required value="{$email_value}">
                        </div>
                    </div>
                    
                    <!-- Subject -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="subject">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="subject" name="subject"
                            class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:px-4 sm:py-3 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                            placeholder="What is this message about?"
                            required value="{$subject_value}">
                    </div>
                    
                    <!-- Message -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="message">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" name="message" rows="5"
                            class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:px-4 sm:py-3 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 resize-none"
                            placeholder="Write your message here..."
                            required>{$message_value}</textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="text-center pt-4">
                        <button type="submit"
                            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 sm:px-8 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contact Information Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Phone -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition duration-200">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-phone text-blue-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Phone</h3>
                <p class="text-gray-600 text-sm">+94 11 234 5678</p>
                <p class="text-gray-600 text-sm">+94 77 123 4567</p>
            </div>
            
            <!-- Email -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition duration-200">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-green-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Email</h3>
                <p class="text-gray-600 text-sm">info@company.com</p>
                <p class="text-gray-600 text-sm">support@company.com</p>
            </div>
            
            <!-- Address -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition duration-200 sm:col-span-2 lg:col-span-1">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-red-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Address</h3>
                <p class="text-gray-600 text-sm">123 Main Street</p>
                <p class="text-gray-600 text-sm">Colombo 03, Sri Lanka</p>
            </div>
        </div>

        <!-- Google Map Section -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">
                    <i class="fas fa-map-marked-alt mr-2 text-blue-600"></i>
                    Find Us Here
                </h3>
                <div class="rounded-lg overflow-hidden shadow-lg">
                    <iframe
                        src="https://www.google.com/maps?q=Colombo%2C+Sri+Lanka&output=embed"
                        width="100%" 
                        height="300" 
                        class="sm:h-80 lg:h-96"
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- Business Hours -->
        <div class="bg-white rounded-xl shadow-lg mt-6 sm:mt-8 overflow-hidden">
            <div class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">
                    <i class="fas fa-clock mr-2 text-green-600"></i>
                    Business Hours
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">Monday - Friday</span>
                            <span class="text-gray-600">9:00 AM - 6:00 PM</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">Saturday</span>
                            <span class="text-gray-600">9:00 AM - 4:00 PM</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="font-medium text-gray-700">Sunday</span>
                            <span class="text-red-600 font-medium">Closed</span>
                        </div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-800 mb-2">Quick Response</h4>
                        <p class="text-blue-700 text-sm">We typically respond to messages within 24 hours during business days.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional mobile-specific styles */
@media (max-width: 640px) {
    .min-h-screen {
        min-height: 100vh;
    }
    
    /* Ensure form inputs are properly sized on mobile */
    input[type="text"],
    input[type="email"],
    textarea {
        font-size: 16px !important; /* Prevents zoom on iOS */
    }
    
    /* Adjust iframe height for mobile */
    iframe {
        height: 250px !important;
    }
}

/* Focus states for better accessibility */
@media (prefers-reduced-motion: no-preference) {
    * {
        transition-duration: 0.2s;
    }
}

/* Print styles */
@media print {
    .bg-gray-50 {
        background: white !important;
    }
    
    .shadow-lg,
    .shadow-md {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
    }
}
</style>
HTML;

include 'layouts/app.php';
?>