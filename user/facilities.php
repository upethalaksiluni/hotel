<?php
session_start();
require_once '../config/database.php';

// Fetch facilities from the database (replace with your actual table/column names)
$facilities = [];
$result = $conn->query("SELECT name, icon, description FROM room_facilities ORDER BY name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $facilities[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Facilities - The Royal Grand</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold font-playfair text-center text-gray-900 mb-2">Our Facilities</h1>
        <p class="text-center text-gray-600 mb-8">Enjoy a range of amenities and services designed for your comfort and convenience.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($facilities)): ?>
                <div class="col-span-full text-center text-gray-400 py-12">
                    <i class="fas fa-concierge-bell text-5xl mb-4"></i>
                    <p>No facilities available at this time.</p>
                </div>
            <?php else: ?>
                <?php foreach ($facilities as $facility): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center text-center hover:shadow-md transition">
                        <div class="w-14 h-14 flex items-center justify-center rounded-full bg-blue-50 mb-4">
                            <i class="<?php echo htmlspecialchars($facility['icon']); ?> text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold font-playfair text-gray-900 mb-2"><?php echo htmlspecialchars($facility['name']); ?></h3>
                        <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($facility['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="mt-10 text-center">
            <a href="dashboard.php" class="inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>