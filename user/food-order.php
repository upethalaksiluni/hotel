<?php
session_start();
$check_in_filter = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out_filter = isset($_GET['check_out']) ? $_GET['check_out'] : '';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Sample menu data (in real application, this would come from database)
$menu_items = [
    [
        'id' => 1,
        'name' => 'Grilled Chicken Breast',
        'description' => 'Tender grilled chicken breast with herbs and spices',
        'price' => 18.99,
        'category' => 'Main Course',
        'image' => 'images/chicken-breast.webp'
    ],
    [
        'id' => 2,
        'name' => 'Beef Steak',
        'description' => 'Premium beef steak cooked to perfection',
        'price' => 24.99,
        'category' => 'Main Course',
        'image' => 'images/beef-steak.webp'
    ],
    [
        'id' => 3,
        'name' => 'Caesar Salad',
        'description' => 'Fresh romaine lettuce with Caesar dressing and croutons',
        'price' => 12.99,
        'category' => 'Salads',
        'image' => 'images/caesar-salad.webp'
    ],
    [
        'id' => 4,
        'name' => 'Margherita Pizza',
        'description' => 'Classic pizza with tomato, mozzarella, and basil',
        'price' => 16.99,
        'category' => 'Pizza',
        'image' => 'images/margherita-pizza.webp'
    ],
    [
        'id' => 5,
        'name' => 'Chocolate Cake',
        'description' => 'Rich chocolate cake with chocolate frosting',
        'price' => 8.99,
        'category' => 'Desserts',
        'image' => 'images/chocolate-cake.webp'
    ],
    [
        'id' => 6,
        'name' => 'Fresh Orange Juice',
        'description' => 'Freshly squeezed orange juice',
        'price' => 4.99,
        'category' => 'Beverages',
        'image' => 'images/orange-juice.webp'
    ],
    [
        'id' => 7,
        'name' => 'Seafood Pasta',
        'description' => 'Delicious pasta with fresh seafood in creamy sauce',
        'price' => 22.99,
        'category' => 'Main Course',
        'image' => 'images/seafood-pasta.webp'
    ],
    [
        'id' => 8,
        'name' => 'Greek Salad',
        'description' => 'Traditional Greek salad with feta cheese and olives',
        'price' => 13.99,
        'category' => 'Salads',
        'image' => 'images/greek-salad.webp'
    ]
];

// Handle AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'add_to_cart':
            $item_id = intval($_POST['item_id']);
            $quantity = intval($_POST['quantity']);
            
            // Find the item in menu
            $item = null;
            foreach ($menu_items as $menu_item) {
                if ($menu_item['id'] == $item_id) {
                    $item = $menu_item;
                    break;
                }
            }
            
            if ($item) {
                // Check if item already exists in cart
                $found = false;
                foreach ($_SESSION['cart'] as &$cart_item) {
                    if ($cart_item['id'] == $item_id) {
                        $cart_item['quantity'] += $quantity;
                        $found = true;
                        break;
                    }
                }
                
                // If not found, add new item
                if (!$found) {
                    $_SESSION['cart'][] = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $quantity,
                        'image' => $item['image']
                    ];
                }
                
                echo json_encode(['success' => true, 'cart_count' => array_sum(array_column($_SESSION['cart'], 'quantity'))]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Item not found']);
            }
            exit;
            
        case 'remove_from_cart':
            $item_id = intval($_POST['item_id']);
            
            foreach ($_SESSION['cart'] as $key => $cart_item) {
                if ($cart_item['id'] == $item_id) {
                    unset($_SESSION['cart'][$key]);
                    $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
                    break;
                }
            }
            
            echo json_encode(['success' => true, 'cart_count' => array_sum(array_column($_SESSION['cart'], 'quantity'))]);
            exit;
            
        case 'update_quantity':
            $item_id = intval($_POST['item_id']);
            $quantity = intval($_POST['quantity']);
            
            foreach ($_SESSION['cart'] as &$cart_item) {
                if ($cart_item['id'] == $item_id) {
                    if ($quantity > 0) {
                        $cart_item['quantity'] = $quantity;
                    } else {
                        // Remove item if quantity is 0
                        unset($_SESSION['cart'][$key]);
                        $_SESSION['cart'] = array_values($_SESSION['cart']);
                    }
                    break;
                }
            }
            
            echo json_encode(['success' => true, 'cart_count' => array_sum(array_column($_SESSION['cart'], 'quantity'))]);
            exit;
            
        case 'get_cart':
            echo json_encode(['cart' => $_SESSION['cart']]);
            exit;
    }
}

// Get unique categories
$categories = array_unique(array_column($menu_items, 'category'));

// Calculate cart totals
$cart_total = 0;
$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $cart_count += $item['quantity'];
}

// Check-in and Check-out date logic
$min_check_in = date('Y-m-d');
$min_check_out = date('Y-m-d', strtotime('+1 day'));

$content = <<<HTML
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <form class="grid grid-cols-1 md:grid-cols-4 gap-4" method="GET" action="">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
            <input type="date" name="check_in" value="{$check_in_filter}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" min="{$min_check_in}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
            <input type="date" name="check_out" value="{$check_out_filter}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" min="{$min_check_out}">
        </div>
        <!-- ...rest of your form... -->
    </form>
</div>
HTML;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Order - The Royal Grand Colombo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .menu-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .cart-sidebar {
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        .cart-sidebar.open {
            transform: translateX(0);
        }
        .overlay {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .quantity-btn {
            transition: all 0.2s ease;
        }
        .quantity-btn:hover {
            background-color: #3b82f6;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-40">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-800">The Royal Grand</h1>
                    <span class="text-sm text-gray-600 hidden md:block">Restaurant</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button onclick="toggleCart()" class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cart-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center <?php echo $cart_count > 0 ? '' : 'hidden'; ?>">
                            <?php echo $cart_count; ?>
                        </span>
                    </button>
                    <a href="dashboard.php" class="hidden md:block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Dashboard</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <!-- Page Title -->
        <div class="text-center mb-8">
            <h2 class="text-4xl font-bold text-gray-800 mb-2">Food Menu</h2>
            <p class="text-gray-600">Delicious meals prepared with finest ingredients</p>
        </div>

        <!-- Category Filter -->
        <div class="mb-8">
            <div class="flex flex-wrap justify-center gap-2 md:gap-4">
                <button onclick="filterMenu('all')" class="category-btn active px-4 py-2 rounded-full text-sm font-medium transition-colors">
                    All Items
                </button>
                <?php foreach ($categories as $category): ?>
                    <button onclick="filterMenu('<?php echo strtolower(str_replace(' ', '-', $category)); ?>')" 
                            class="category-btn px-4 py-2 rounded-full text-sm font-medium transition-colors">
                        <?php echo $category; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Menu Items Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="menu-grid">
            <?php foreach ($menu_items as $item): ?>
                <div class="menu-item bg-white rounded-lg shadow-md overflow-hidden" data-category="<?php echo strtolower(str_replace(' ', '-', $item['category'])); ?>">
                    <div class="relative">
                        <img src="images/food/<?php echo $item['image']; ?>" 
                             alt="<?php echo $item['name']; ?>" 
                             class="w-full h-48 object-cover"
                             onerror="this.src='https://via.placeholder.com/300x200/f3f4f6/6b7280?text=<?php echo urlencode($item['name']); ?>'">
                        <div class="absolute top-2 right-2">
                            <span class="bg-blue-600 text-white px-2 py-1 rounded-full text-xs font-medium">
                                <?php echo $item['category']; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2"><?php echo $item['name']; ?></h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?php echo $item['description']; ?></p>
                        
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xl font-bold text-blue-600">$<?php echo number_format($item['price'], 2); ?></span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <button onclick="changeQuantity(<?php echo $item['id']; ?>, -1)" class="quantity-btn w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-600">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span id="qty-<?php echo $item['id']; ?>" class="w-8 text-center font-medium">1</span>
                                <button onclick="changeQuantity(<?php echo $item['id']; ?>, 1)" class="quantity-btn w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-600">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            
                            <button onclick="addToCart(<?php echo $item['id']; ?>)" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                                <i class="fas fa-cart-plus"></i>
                                <span class="hidden sm:inline">Add to Cart</span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Cart Sidebar -->
    <div class="fixed inset-0 z-50 pointer-events-none">
        <!-- Overlay -->
        <div id="cart-overlay" class="overlay absolute inset-0 bg-black bg-opacity-50 pointer-events-auto" onclick="toggleCart()"></div>
        
        <!-- Cart Sidebar -->
        <div id="cart-sidebar" class="cart-sidebar absolute right-0 top-0 h-full w-full sm:w-96 bg-white shadow-lg pointer-events-auto">
            <div class="flex flex-col h-full">
                <!-- Cart Header -->
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Shopping Cart</h3>
                        <button onclick="toggleCart()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div id="cart-items">
                        <?php if (empty($_SESSION['cart'])): ?>
                            <div class="text-center py-8">
                                <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">Your cart is empty</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <div class="cart-item flex items-center space-x-3 p-3 border-b border-gray-100" data-id="<?php echo $item['id']; ?>">
                                    <img src="images/food/<?php echo $item['image']; ?>" 
                                         alt="<?php echo $item['name']; ?>" 
                                         class="w-12 h-12 object-cover rounded"
                                         onerror="this.src='https://via.placeholder.com/48x48/f3f4f6/6b7280'">
                                    
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-800 truncate"><?php echo $item['name']; ?></h4>
                                        <p class="text-xs text-gray-500">$<?php echo number_format($item['price'], 2); ?></p>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <button onclick="updateCartQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)" 
                                                class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-300">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <span class="w-8 text-center text-sm font-medium"><?php echo $item['quantity']; ?></span>
                                        <button onclick="updateCartQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)" 
                                                class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-300">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                    
                                    <button onclick="removeFromCart(<?php echo $item['id']; ?>)" 
                                            class="text-red-500 hover:text-red-700 p-1">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Cart Footer -->
                <?php if (!empty($_SESSION['cart'])): ?>
                    <div class="border-t border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-lg font-semibold text-gray-800">Total:</span>
                            <span id="cart-total" class="text-xl font-bold text-blue-600">$<?php echo number_format($cart_total, 2); ?></span>
                        </div>
                        
                        <button onclick="proceedToCheckout()" 
                                class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Proceed to Checkout
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center space-x-2">
            <i class="fas fa-check-circle"></i>
            <span id="toast-message">Item added to cart!</span>
        </div>
    </div>

    <script>
        // Quantity management for individual items
        const quantities = {};
        
        function changeQuantity(itemId, change) {
            if (!quantities[itemId]) quantities[itemId] = 1;
            
            quantities[itemId] += change;
            if (quantities[itemId] < 1) quantities[itemId] = 1;
            
            document.getElementById(`qty-${itemId}`).textContent = quantities[itemId];
        }
        
        // Add to cart functionality
        function addToCart(itemId) {
            const quantity = quantities[itemId] || 1;
            
            fetch('food-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add_to_cart&item_id=${itemId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartBadge(data.cart_count);
                    showToast('Item added to cart!');
                    refreshCartSidebar();
                } else {
                    showToast(data.message || 'Error adding item to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error adding item to cart', 'error');
            });
        }
        
        // Update cart badge
        function updateCartBadge(count) {
            const badge = document.getElementById('cart-badge');
            badge.textContent = count;
            badge.classList.toggle('hidden', count === 0);
        }
        
        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            
            toastMessage.textContent = message;
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 z-50 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            
            // Show toast
            toast.style.transform = 'translateX(0)';
            
            // Hide toast after 3 seconds
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
            }, 3000);
        }
        
        // Cart sidebar toggle
        function toggleCart() {
            const sidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('cart-overlay');
            
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }
        
        // Remove item from cart
        function removeFromCart(itemId) {
            fetch('food-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove_from_cart&item_id=${itemId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartBadge(data.cart_count);
                    refreshCartSidebar();
                    showToast('Item removed from cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error removing item from cart', 'error');
            });
        }
        
        // Update cart item quantity
        function updateCartQuantity(itemId, newQuantity) {
            if (newQuantity < 1) {
                removeFromCart(itemId);
                return;
            }
            
            fetch('food-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_quantity&item_id=${itemId}&quantity=${newQuantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartBadge(data.cart_count);
                    refreshCartSidebar();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error updating quantity', 'error');
            });
        }
        
        // Refresh cart sidebar content
        function refreshCartSidebar() {
            // Reload the page to refresh cart content
            // In a real application, you would fetch cart data via AJAX and update the DOM
            setTimeout(() => {
                location.reload();
            }, 500);
        }
        
        // Category filtering
        function filterMenu(category) {
            const items = document.querySelectorAll('.menu-item');
            const buttons = document.querySelectorAll('.category-btn');
            
            // Update active button
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Filter items
            items.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        // Proceed to checkout
        function proceedToCheckout() {
            window.location.href = 'food-bill.php';
        }
        
        // Initialize category buttons styling
        document.addEventListener('DOMContentLoaded', function() {
            const categoryButtons = document.querySelectorAll('.category-btn');
            
            categoryButtons.forEach(button => {
                if (button.classList.contains('active')) {
                    button.classList.add('bg-blue-600', 'text-white');
                } else {
                    button.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                }
            });
            
            // Add click event listeners
            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active styles from all buttons
                    categoryButtons.forEach(btn => {
                        btn.classList.remove('bg-blue-600', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                    });
                    
                    // Add active styles to clicked button
                    this.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                    this.classList.add('bg-blue-600', 'text-white');
                });
            });
        });
    </script>
</body>
</html>