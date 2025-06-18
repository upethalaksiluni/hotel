<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodSwap - Trade & Share Food</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">

    <nav class="bg-green-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-utensils text-2xl"></i>
                <a href="#" class="text-xl font-bold">FoodSwap</a>
            </div>

            <div class="hidden md:flex space-x-6">
                <a href="#" class="hover:text-green-200">Home</a>
                <a href="#" class="hover:text-green-200">Browse</a>
                <a href="#" class="hover:text-green-200">Sell/Trade</a>
                <a href="#" class="hover:text-green-200">About</a>
                <a href="#" class="hover:text-green-200">Contact</a>
            </div>

            <div class="flex items-center space-x-4">
                <a href="#" class="hover:text-green-200">
                    <i class="fas fa-search"></i>
                </a>
                <a href="#" class="hover:text-green-200">
                    <i class="fas fa-user"></i>
                </a>
                <a href="#" class="hover:text-green-200 relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="absolute -top-2 -right-2 bg-yellow-400 text-black text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                </a>
                <button class="md:hidden focus:outline-none" id="menuButton">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <section class="bg-gradient-to-r from-green-500 to-green-700 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Trade Fresh Food in Your Community</h1>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Connect with local growers, share surplus produce, and discover delicious foods near you.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#" class="bg-white text-green-700 font-bold py-3 px-6 rounded-lg hover:bg-gray-100 transition duration-300">Browse Items</a>
                <a href="#" class="bg-transparent border-2 border-white text-white font-bold py-3 px-6 rounded-lg hover:bg-white hover:text-green-700 transition duration-300">List an Item</a>
            </div>
        </div>
    </section>

    <section class="py-12 container mx-auto px-4">
        <h2 class="text-3xl font-bold mb-8 text-center">Featured Items</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1518977676601-b53f82aba655?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Organic Tomatoes" class="w-full h-48 object-cover">
                    <span class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded">Organic</span>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-1">Fresh Organic Tomatoes</h3>
                    <p class="text-gray-600 text-sm mb-2">Homegrown, pesticide-free</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-green-700">$2.50/lb</span>
                        <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Trade</button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Homemade Bread" class="w-full h-48 object-cover">
                    <span class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">Baked</span>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-1">Sourdough Bread</h3>
                    <p class="text-gray-600 text-sm mb-2">Freshly baked today</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-green-700">$5.00/loaf</span>
                        <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Trade</button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1550583724-b2692b85b150?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Farm Eggs" class="w-full h-48 object-cover">
                    <span class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">Farm Fresh</span>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-1">Free-range Eggs</h3>
                    <p class="text-gray-600 text-sm mb-2">Dozen, collected yesterday</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-green-700">$3.50/dozen</span>
                        <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Trade</button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                <div class="relative">
                    <img src="https://www.thecountrycook.net/wp-content/uploads/2023/04/thumbnail-Homemade-Strawberry-Jam-scaled.jpg">
                    <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">Homemade</span>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-1">Strawberry Jam</h3>
                    <p class="text-gray-600 text-sm mb-2">Small batch, no preservatives</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-green-700">$4.00/jar</span>
                        <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Trade</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="#" class="inline-block bg-green-600 text-white font-bold py-2 px-6 rounded hover:bg-green-700 transition duration-300">View All Items</a>
        </div>
    </section>

    <section class="py-12 bg-gray-100">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold mb-8 text-center">Browse Categories</h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <a href="#" class="bg-white rounded-lg shadow p-4 text-center hover:shadow-md transition duration-300">
                    <div class="text-green-600 mb-2">
                        <i class="fas fa-apple-alt text-3xl"></i>
                    </div>
                    <h3 class="font-semibold">Produce</h3>
                </a>

                <a href="#" class="bg-white rounded-lg shadow p-4 text-center hover:shadow-md transition duration-300">
                    <div class="text-yellow-500 mb-2">
                        <i class="fas fa-bread-slice text-3xl"></i>
                    </div>
                    <h3 class="font-semibold">Bakery</h3>
                </a>

                <a href="#" class="bg-white rounded-lg shadow p-4 text-center hover:shadow-md transition duration-300">
                    <div class="text-blue-500 mb-2">
                        <i class="fas fa-cheese text-3xl"></i>
                    </div>
                    <h3 class="font-semibold">Dairy</h3>
                </a>

                <a href="#" class="bg-white rounded-lg shadow p-4 text-center hover:shadow-md transition duration-300">
                    <div class="text-red-500 mb-2">
                        <i class="fas fa-drumstick-bite text-3xl"></i>
                    </div>
                    <h3 class="font-semibold">Meat</h3>
                </a>

                <a href="#" class="bg-white rounded-lg shadow p-4 text-center hover:shadow-md transition duration-300">
                    <div class="text-purple-500 mb-2">
                        <i class="fas fa-jar text-3xl"></i>
                    </div>
                    <h3 class="font-semibold">Preserves</h3>
                </a>

                <a href="#" class="bg-white rounded-lg shadow p-4 text-center hover:shadow-md transition duration-300">
                    <div class="text-green-700 mb-2">
                        <i class="fas fa-leaf text-3xl"></i>
                    </div>
                    <h3 class="font-semibold">Herbs</h3>
                </a>
            </div>
        </div>
    </section>

    <section class="py-12 container mx-auto px-4">
        <h2 class="text-3xl font-bold mb-8 text-center">How FoodSwap Works</h2>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-green-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-search text-green-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-2">1. Browse or List</h3>
                <p class="text-gray-600">Find fresh food near you or list items you'd like to trade or sell.</p>
            </div>

            <div class="text-center">
                <div class="bg-green-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-comments text-green-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-2">2. Connect</h3>
                <p class="text-gray-600">Message the seller to arrange pickup or delivery details.</p>
            </div>

            <div class="text-center">
                <div class="bg-green-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-exchange-alt text-green-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-xl mb-2">3. Trade</h3>
                <p class="text-gray-600">Exchange goods and enjoy fresh, local food!</p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-green-600 text-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold mb-8 text-center">What Our Community Says</h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-green-700 p-6 rounded-lg">
                    <div class="flex items-center mb-4">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Sarah J." class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Sarah J.</h4>
                            <div class="flex text-yellow-300">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p>"I've been able to trade my extra garden produce for homemade bread and jam. It's amazing to connect with my neighbors this way!"</p>
                </div>

                <div class="bg-green-700 p-6 rounded-lg">
                    <div class="flex items-center mb-4">
                        <img src="https://randomuser.me/api/portraits/men/54.jpg" alt="Michael T." class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Michael T.</h4>
                            <div class="flex text-yellow-300">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p>"As a small-scale farmer, this platform helps me sell excess produce directly to my community. No middlemen, fair prices."</p>
                </div>

                <div class="bg-green-700 p-6 rounded-lg">
                    <div class="flex items-center mb-4">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Priya K." class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Priya K.</h4>
                            <div class="flex text-yellow-300">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p>"Found amazing organic ingredients for my home cooking at prices much better than the supermarket. Will definitely use again!"</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-800 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Join the FoodSwap Community?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Sign up today and start trading fresh, local food with people in your area.</p>
            <a href="#" class="inline-block bg-green-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-green-700 transition duration-300">Get Started - It's Free!</a>
        </div>
    </section>

    <footer class="bg-gray-900 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-utensils mr-2"></i> FoodSwap
                    </h3>
                    <p class="text-gray-400">Connecting communities through local food trading since 2023.</p>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Home</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Browse Items</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">List an Item</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">How It Works</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">FAQs</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Safety Tips</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Contact Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Privacy Policy</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Connect With Us</h4>
                    <div class="flex space-x-4 mb-4">
                        <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-pinterest"></i></a>
                    </div>
                    <p class="text-gray-400">Subscribe to our newsletter</p>
                    <div class="flex mt-2">
                        <input type="email" placeholder="Your email" class="bg-gray-800 text-white px-3 py-2 rounded-l focus:outline-none w-full">
                        <button class="bg-green-600 text-white px-4 py-2 rounded-r hover:bg-green-700"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-6 text-center text-gray-400">
                <p>&copy; 2023 FoodSwap. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <div class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden" id="mobileMenu">
        <div class="bg-green-600 h-full w-3/4 max-w-sm p-4">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-utensils text-2xl"></i>
                    <span class="text-xl font-bold">FoodSwap</span>
                </div>
                <button id="closeMenu" class="text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav class="space-y-4">
                <a href="#" class="block py-2 px-4 bg-green-700 rounded">Home</a>
                <a href="#" class="block py-2 px-4 hover:bg-green-700 rounded">Browse</a>
                <a href="#" class="block py-2 px-4 hover:bg-green-700 rounded">Sell/Trade</a>
                <a href="#" class="block py-2 px-4 hover:bg-green-700 rounded">About</a>
                <a href="#" class="block py-2 px-4 hover:bg-green-700 rounded">Contact</a>
                <a href="#" class="block py-2 px-4 hover:bg-green-700 rounded">My Account</a>
            </nav>
        </div>
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            const menuButton = document.getElementById('menuButton');
            const closeButton = document.getElementById('closeMenu');

            menuButton.addEventListener('click', function() {
                mobileMenu.classList.remove('hidden');
            });

            closeButton.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
            });
        });
    </script>
</body>
</html>