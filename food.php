<?php
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Sample menu items
$menu_items = [
    1 => ['name' => 'Fresh Organic Tomatoes', 'price' => 2.50, 'image' => 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80', 'category' => 'Produce', 'description' => 'Homegrown, pesticide-free'],
    2 => ['name' => 'Sourdough Bread', 'price' => 5.00, 'image' => 'https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80', 'category' => 'Bakery', 'description' => 'Freshly baked today'],
    3 => ['name' => 'Free-range Eggs', 'price' => 3.50, 'image' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80', 'category' => 'Dairy', 'description' => 'Dozen, collected yesterday'],
    4 => ['name' => 'Strawberry Jam', 'price' => 4.00, 'image' => 'https://www.thecountrycook.net/wp-content/uploads/2023/04/thumbnail-Homemade-Strawberry-Jam-scaled.jpg', 'category' => 'Preserves', 'description' => 'Small batch, no preservatives'],
    5 => ['name' => 'Organic Carrots', 'price' => 1.75, 'image' => 'https://images.unsplash.com/photo-1445282768818-728615cc910a?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80', 'category' => 'Produce', 'description' => 'Fresh from the farm'],
    6 => ['name' => 'Artisan Cheese', 'price' => 8.50, 'image' => 'https://images.unsplash.com/photo-1452195100486-9cc805987862?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80', 'category' => 'Dairy', 'description' => 'Locally made aged cheddar'],
    7 => ['name' => 'Honey', 'price' => 6.00, 'image' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80', 'category' => 'Preserves', 'description' => 'Raw wildflower honey'],
    8 => ['name' => 'Whole Wheat Bread', 'price' => 4.50, 'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80', 'category' => 'Bakery', 'description' => 'Freshly baked with organic flour']
];

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_to_cart') {
        $item_id = (int)$_POST['item_id'];
        $quantity = (int)$_POST['quantity'];
        
        if (isset($menu_items[$item_id]) && $quantity > 0) {
            if (isset($_SESSION['cart'][$item_id])) {
                $_SESSION['cart'][$item_id] += $quantity;
            } else {
                $_SESSION['cart'][$item_id] = $quantity;
            }
            
            $cart_count = array_sum($_SESSION['cart']);
            echo json_encode(['success' => true, 'cart_count' => $cart_count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid item or quantity']);
        }
        exit;
    }
    
    if ($action === 'update_cart') {
        $item_id = (int)$_POST['item_id'];
        $quantity = (int)$_POST['quantity'];
        
        if ($quantity > 0) {
            $_SESSION['cart'][$item_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$item_id]);
        }
        
        $cart_count = array_sum($_SESSION['cart']);
        echo json_encode(['success' => true, 'cart_count' => $cart_count]);
        exit;
    }
    
    if ($action === 'remove_from_cart') {
        $item_id = (int)$_POST['item_id'];
        unset($_SESSION['cart'][$item_id]);
        
        $cart_count = array_sum($_SESSION['cart']);
        echo json_encode(['success' => true, 'cart_count' => $cart_count]);
        exit;
    }
    
    if ($action === 'get_cart_count') {
        $cart_count = array_sum($_SESSION['cart']);
        echo json_encode(['cart_count' => $cart_count]);
        exit;
    }
    
    if ($action === 'place_order') {
        if (!empty($_SESSION['cart'])) {
            // In a real application, you would save the order to a database
            $order_id = 'ORD-' . time();
            $_SESSION['last_order'] = $order_id;
            $_SESSION['cart'] = [];
            echo json_encode(['success' => true, 'order_id' => $order_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        }
        exit;
    }
}

// Calculate cart totals
function calculateCartTotal($cart, $menu_items) {
    $total = 0;
    foreach ($cart as $item_id => $quantity) {
        if (isset($menu_items[$item_id])) {
            $total += $menu_items[$item_id]['price'] * $quantity;
        }
    }
    return $total;
}

$cart_count = array_sum($_SESSION['cart']);
$cart_total = calculateCartTotal($_SESSION['cart'], $menu_items);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodSwap - Order Food Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .cart-modal {
            display: none;
        }
        .cart-modal.active {
            display: flex;
        }
        .order-success {
            display: none;
        }
        .order-success.active {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Navigation -->
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-utensils text-2xl"></i>
                <a href="#" class="text-xl font-bold">FoodSwap</a>
            </div>
            
            <div class="hidden md:flex space-x-6">
                <a href="#" class="hover:text-green-200" onclick="showSection('home')">Home</a>
                <a href="#" class="hover:text-green-200" onclick="showSection('menu')">Menu</a>
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
                <a href="#" class="hover:text-green-200 relative" onclick="openCart()">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="absolute -top-2 -right-2 bg-yellow-400 text-black text-xs rounded-full h-5 w-5 flex items-center justify-center" id="cartCount"><?php echo $cart_count; ?></span>
                </a>
                <button class="md:hidden focus:outline-none" id="menuButton">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Home Section -->
    <div id="homeSection">
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-green-500 to-green-700 text-white py-16">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Order Fresh Food Online</h1>
                <p class="text-xl mb-8 max-w-2xl mx-auto">Get fresh, local produce and homemade goods delivered to your door.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button onclick="showSection('menu')" class="bg-white text-green-700 font-bold py-3 px-6 rounded-lg hover:bg-gray-100 transition duration-300">Order Now</button>
                    <button onclick="openCart()" class="bg-transparent border-2 border-white text-white font-bold py-3 px-6 rounded-lg hover:bg-white hover:text-green-700 transition duration-300">View Cart</button>
                </div>
            </div>
        </section>

        <!-- Featured Items -->
        <section class="py-12 container mx-auto px-4">
            <h2 class="text-3xl font-bold mb-8 text-center">Featured Items</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach (array_slice($menu_items, 0, 4) as $id => $item): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                    <div class="relative">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="w-full h-48 object-cover">
                        <span class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded"><?php echo $item['category']; ?></span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-1"><?php echo $item['name']; ?></h3>
                        <p class="text-gray-600 text-sm mb-2"><?php echo $item['description']; ?></p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-green-700">$<?php echo number_format($item['price'], 2); ?></span>
                            <button onclick="addToCart(<?php echo $id; ?>)" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-8">
                <button onclick="showSection('menu')" class="inline-block bg-green-600 text-white font-bold py-2 px-6 rounded hover:bg-green-700 transition duration-300">View Full Menu</button>
            </div>
        </section>
    </div>

    <!-- Menu Section -->
    <div id="menuSection" style="display: none;">
        <section class="py-12 container mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold">Our Menu</h2>
                <button onclick="showSection('home')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </button>
            </div>
            
            <!-- Category Filter -->
            <div class="mb-8">
                <div class="flex flex-wrap gap-2">
                    <button onclick="filterMenu('all')" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 filter-btn active">All</button>
                    <button onclick="filterMenu('Produce')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 filter-btn">Produce</button>
                    <button onclick="filterMenu('Bakery')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 filter-btn">Bakery</button>
                    <button onclick="filterMenu('Dairy')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 filter-btn">Dairy</button>
                    <button onclick="filterMenu('Preserves')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 filter-btn">Preserves</button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="menuItems">
                <?php foreach ($menu_items as $id => $item): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300 menu-item" data-category="<?php echo $item['category']; ?>">
                    <div class="relative">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="w-full h-48 object-cover">
                        <span class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded"><?php echo $item['category']; ?></span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-1"><?php echo $item['name']; ?></h3>
                        <p class="text-gray-600 text-sm mb-2"><?php echo $item['description']; ?></p>
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold text-green-700 text-lg">$<?php echo number_format($item['price'], 2); ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" min="1" value="1" class="w-16 px-2 py-1 border rounded text-center" id="qty-<?php echo $id; ?>">
                            <button onclick="addToCart(<?php echo $id; ?>)" class="flex-1 bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 transition duration-300">
                                <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <!-- Cart Modal -->
    <div id="cartModal" class="cart-modal fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center">
        <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Shopping Cart</h2>
                    <button onclick="closeCart()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6" id="cartContent">
                <?php if (empty($_SESSION['cart'])): ?>
                    <p class="text-center text-gray-500 py-8">Your cart is empty</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($_SESSION['cart'] as $item_id => $quantity): ?>
                            <?php if (isset($menu_items[$item_id])): ?>
                                <?php $item = $menu_items[$item_id]; ?>
                                <div class="flex items-center gap-4 p-4 border rounded-lg cart-item" id="cart-item-<?php echo $item_id; ?>">
                                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="w-16 h-16 object-cover rounded">
                                    <div class="flex-1">
                                        <h3 class="font-bold"><?php echo $item['name']; ?></h3>
                                        <p class="text-gray-600 text-sm"><?php echo $item['description']; ?></p>
                                        <p class="text-green-700 font-bold">$<?php echo number_format($item['price'], 2); ?></p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button onclick="updateCartQuantity(<?php echo $item_id; ?>, -1)" class="bg-gray-200 text-gray-700 w-8 h-8 rounded-full hover:bg-gray-300">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span class="w-8 text-center" id="cart-qty-<?php echo $item_id; ?>"><?php echo $quantity; ?></span>
                                        <button onclick="updateCartQuantity(<?php echo $item_id; ?>, 1)" class="bg-gray-200 text-gray-700 w-8 h-8 rounded-full hover:bg-gray-300">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <button onclick="removeFromCart(<?php echo $item_id; ?>)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="border-t pt-4 mt-6">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xl font-bold">Total: $<span id="cartTotal"><?php echo number_format($cart_total, 2); ?></span></span>
                        </div>
                        <button onclick="placeOrder()" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition duration-300">
                            <i class="fas fa-credit-card mr-2"></i>Place Order
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Order Success Modal -->
    <div id="orderSuccess" class="order-success fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg max-w-md w-full mx-4 p-6 text-center">
            <div class="text-green-600 mb-4">
                <i class="fas fa-check-circle text-6xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Order Placed Successfully!</h2>
            <p class="text-gray-600 mb-4">Your order ID is: <span id="orderIdDisplay" class="font-bold"></span></p>
            <p class="text-gray-600 mb-6">Thank you for your order. We'll prepare your items and notify you when they're ready.</p>
            <button onclick="closeOrderSuccess()" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Continue Shopping</button>
        </div>
    </div>

    <!-- Mobile Menu -->
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
                <a href="#" onclick="showSection('home')" class="block py-2 px-4 bg-green-700 rounded">Home</a>
                <a href="#" onclick="showSection('menu')" class="block py-2 px-4 hover:bg-green-700 rounded">Menu</a>
                <a href="#" class="block py-2 px-4 hover:bg-green-700 rounded">About</a>
                <a href="#" class="block py-2 px-4 hover:bg-green-700 rounded">Contact</a>
                <a href="#" onclick="openCart()" class="block py-2 px-4 hover:bg-green-700 rounded">Cart (<span id="mobileCartCount"><?php echo $cart_count; ?></span>)</a>
            </nav>
        </div>
    </div>

    <script>
        // Global variables
        const menuItems = <?php echo json_encode($menu_items); ?>;
        let currentCart = <?php echo json_encode($_SESSION['cart']); ?>;

        // Section management
        function showSection(section) {
            document.getElementById('homeSection').style.display = section === 'home' ? 'block' : 'none';
            document.getElementById('menuSection').style.display = section === 'menu' ? 'block' : 'none';
            closeMobileMenu();
        }

        // Cart functions
        function addToCart(itemId) {
            const quantityInput = document.getElementById('qty-' + itemId);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add_to_cart&item_id=${itemId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount(data.cart_count);
                    showToast('Item added to cart!', 'success');
                    if (quantityInput) quantityInput.value = 1;
                } else {
                    showToast('Error adding item to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error adding item to cart', 'error');
            });
        }

        function updateCartQuantity(itemId, change) {
            const currentQtyElement = document.getElementById('cart-qty-' + itemId);
            const currentQty = parseInt(currentQtyElement.textContent);
            const newQty = Math.max(0, currentQty + change);
            
            if (newQty === 0) {
                removeFromCart(itemId);
                return;
            }
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_cart&item_id=${itemId}&quantity=${newQty}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentQtyElement.textContent = newQty;
                    updateCartCount(data.cart_count);
                    updateCartTotal();
                }
            });
        }

        function removeFromCart(itemId) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove_from_cart&item_id=${itemId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-item-' + itemId).remove();
                    updateCartCount(data.cart_count);
                    updateCartTotal();
                    
                    // Check if cart is empty
                    if (data.cart_count === 0) {
                        document.getElementById('cartContent').innerHTML = '<p class="text-center text-gray-500 py-8">Your cart is empty</p>';
                    }
                }
            });
        }

        function updateCartCount(count) {
            document.getElementById('cartCount').textContent = count;
            document.getElementById('mobileCartCount').textContent = count;
        }

        function updateCartTotal() {
            let total = 0;
            const cartItems = document.querySelectorAll('.cart-item');
            
            cartItems.forEach(item => {
                const itemId = item.id.split('-')[2];
                const quantity = parseInt(document.getElementById('cart-qty-' + itemId).textContent);
                const price = menuItems[itemId].price;
                total += price * quantity;
            });
            
            document.getElementById('cartTotal').textContent = total.toFixed(2);
        }

        function openCart() {
            document.getElementById('cartModal').classList.add('active');
        }

        function closeCart() {
            document.getElementById('cartModal').classList.remove('active');
        }

        function placeOrder() {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=place_order'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeCart();
                    document.getElementById('orderIdDisplay').textContent = data.order_id;
                    document.getElementById('orderSuccess').classList.add('active');
                    updateCartCount(0);
                    document.getElementById('cartContent').innerHTML = '<p class="text-center text-gray-500 py-8">Your cart is empty</p>';
                } else {
                    showToast('Error placing order', 'error');
                }
            });
        }

        function closeOrderSuccess() {
            document.getElementById('orderSuccess').classList.remove('active');
            showSection('home');
        }

        // Menu filtering
        function filterMenu(category) {
            const items = document.querySelectorAll('.menu-item');
            const buttons = document.querySelectorAll('.filter-btn');
            
            // Update button styles
            buttons.forEach(btn => {
                btn.classList.remove('active', 'bg-green-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            
            event.target.classList.add('active', 'bg-green-600', 'text-white');
            event.target.classList.remove('bg-gray-200', 'text-gray-700');
            
            // Filter items
            items.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Mobile menu
        function closeMobileMenu() {
            document.getElementById('mobileMenu').classList.add('hidden');
        }

        // Toast notifications
        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Event listeners
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
            
            // Close modals when clicking outside
            document.getElementById('cartModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeCart();
                }
            });
            
            document.getElementById('orderSuccess').addEventListener('click', function(e) {
                if (e.


