<?php
session_start();

// Get cart from session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cart_total = 0;
foreach ($cart as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Bill - The Royal Grand Colombo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto mt-10 bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Your Food Bill</h2>
        <?php if (empty($cart)): ?>
            <div class="text-center py-8">
                <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Your cart is empty.</p>
                <a href="food-order.php" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Back to Menu</a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-3 px-4 text-left">Item</th>
                            <th class="py-3 px-4 text-center">Qty</th>
                            <th class="py-3 px-4 text-right">Unit Price</th>
                            <th class="py-3 px-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $item): ?>
                        <tr class="border-b">
                            <td class="py-3 px-4 flex items-center gap-3">
                                <img src="images/food/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-10 h-10 object-cover rounded">
                                <span><?php echo htmlspecialchars($item['name']); ?></span>
                            </td>
                            <td class="py-3 px-4 text-center"><?php echo $item['quantity']; ?></td>
                            <td class="py-3 px-4 text-right">$<?php echo number_format($item['price'], 2); ?></td>
                            <td class="py-3 px-4 text-right">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-right font-bold text-lg">Grand Total:</td>
                            <td class="py-3 px-4 text-right font-bold text-lg text-blue-600">$<?php echo number_format($cart_total, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="mt-8 flex justify-between">
                <a href="food-order.php" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">Back to Menu</a>
                <form method="post" action="food-bill.php">
                    <button type="submit" name="clear_cart" class="px-6 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">Clear Cart</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <?php
    // Clear cart if requested
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        echo "<script>window.location='food-bill.php';</script>";
        exit;
    }
    ?>
</body>
</html>