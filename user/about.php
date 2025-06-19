<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>The Royal Grand</title>
    <link href="./style.css" rel="stylesheet">

    <style>
        .bg-blue-500-vision {
            background-color: rgb(15, 23, 42);
        }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                        dark: '#0f172a',
                        light: '#f8fafc'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-light font-sans antialiased">

    <!-- Hero Section -->
    <header class="bg-gradient-to-r from-dark to-primary-900 text-white py-20">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">Welcome to The Royal Grand</h1>
            <p class="text-xl md:text-2xl max-w-3xl mx-auto opacity-90">
                Experience unmatched luxury, fine dining, and personalized hospitality at the heart of the city.
            </p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-16">

        <!-- Our Story -->
        <section class="mb-20">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2">
                    <h2 class="text-3xl font-bold text-dark mb-6">Our Story</h2>
                    <p class="text-gray-700 mb-4">
                        The Royal Grand began its journey in 1998 with a vision to redefine luxury and service in the hospitality industry.
                        With over two decades of experience, we’ve built a reputation for providing exceptional guest experiences.
                    </p>
                    <p class="text-gray-700 mb-4">
                        From lavish rooms to gourmet dining and tranquil spas, every detail at The Royal Grand is designed to inspire comfort and elegance.
                    </p>
                    <p class="text-gray-700">
                        Join us and immerse yourself in the legacy of timeless luxury and genuine hospitality.
                    </p>
                </div>
                <div class="md:w-1/2">
                    <img src="../images/room1.webp">
                </div>
            </div>
        </section>

        <!-- Hotel Experience -->
        <section class="bg-primary-50 rounded-xl p-12 mb-20">
            <div class="text-center max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold text-dark mb-6">The Royal Grand Experience</h2>
                <p class="text-xl text-gray-700 mb-8">Every moment matters. Discover what makes our hotel truly special.</p>
                <div class="flex flex-wrap justify-center gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-md w-full sm:w-64">
                        <img src="https://www.shangri-la.com/uploadedImages/SLIM/Content/Homepage/2023/SLIM_Homepage_Thumbnail_Dining.jpg" alt="Dining" class="w-full h-32 object-cover rounded mb-4">
                        <h3 class="font-bold text-dark mb-2">Gourmet Dining</h3>
                        <p class="text-gray-600">Savor exquisite international cuisines crafted by master chefs in elegant surroundings.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md w-full sm:w-64">
                        <img src="https://www.shangri-la.com/uploadedImages/SLIM/Content/Homepage/2023/SLIM_Homepage_Thumbnail_Health.jpg" alt="Spa" class="w-full h-32 object-cover rounded mb-4">
                        <h3 class="font-bold text-dark mb-2">Luxury Spa</h3>
                        <p class="text-gray-600">Rejuvenate with holistic spa treatments designed to relax your body and mind.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md w-full sm:w-64">
                        <img src="https://www.shangri-la.com/uploadedImages/SLIM/Content/Homepage/2023/SLIM_Homepage_Thumbnail_Stay.jpg" alt="Rooms" class="w-full h-32 object-cover rounded mb-4">
                        <h3 class="font-bold text-dark mb-2">Elegant Rooms</h3>
                        <p class="text-gray-600">Stay in beautifully appointed rooms with stunning cityscape and ocean views.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission -->
        <section class="bg-blue-500-vision text-white py-12 px-4 mb-10 rounded-lg">
            <h2 class="text-2xl font-bold text-center">Our Mission</h2>
            <p class="mt-4 text-center max-w-2xl mx-auto">
                To deliver unparalleled service, comfort, and elegance — creating memorable stays that exceed every expectation.
            </p>
        </section>

        <!-- Vision -->
        <section class="bg-blue-500-vision text-white py-12 px-4 mb-20 rounded-lg">
            <h2 class="text-2xl font-bold text-center">Our Vision</h2>
            <p class="mt-4 text-center max-w-2xl mx-auto">
                To be recognized globally as a symbol of excellence in hospitality and guest satisfaction.
            </p>
        </section>

        <!-- Achievements / Impact -->
        <section class="bg-dark text-white rounded-xl p-12 mb-20">
            <h2 class="text-3xl font-bold mb-12 text-center">Our Impact</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-5xl font-bold text-primary-400 mb-2">25+</div>
                    <div class="text-gray-300">Years of Excellence</div>
                </div>
                <div>
                    <div class="text-5xl font-bold text-primary-400 mb-2">100K+</div>
                    <div class="text-gray-300">Happy Guests</div>
                </div>
                <div>
                    <div class="text-5xl font-bold text-primary-400 mb-2">5-Star</div>
                    <div class="text-gray-300">International Rating</div>
                </div>
                <div>
                    <div class="text-5xl font-bold text-primary-400 mb-2">50+</div>
                    <div class="text-gray-300">Luxury Suites</div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-dark text-gray-300 py-8">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm">© 2025 The Royal Grand Hotel. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>
