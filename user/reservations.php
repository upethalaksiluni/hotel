<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$title = 'My Reservations';
$header = 'All My Reservations';

$user_id = $_SESSION['user_id'];

// Fetch all reservations for this user
$reservations_query = "SELECT r.*, ro.room_type, ro.room_number, ro.price_per_night 
                      FROM reservations r 
                      JOIN rooms ro ON r.room_id = ro.id 
                      WHERE r.user_id = ? 
                      ORDER BY r.created_at DESC";

$stmt = $conn->prepare($reservations_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservations = $stmt->get_result();

$content = <<<HTML
<div class="max-w-4xl mx-auto py-8 px-2">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">All My Reservations</h2>
HTML;

if ($reservations->num_rows > 0) {
    $content .= <<<HTML
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow text-sm">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-3 px-2">#</th>
                    <th class="py-3 px-2">Room</th>
                    <th class="py-3 px-2">Dates</th>
                    <th class="py-3 px-2">Total</th>
                    <th class="py-3 px-2">Status</th>
                    <th class="py-3 px-2">Action</th>
                </tr>
            </thead>
            <tbody>
HTML;

    $status_colors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'confirmed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'completed' => 'bg-blue-100 text-blue-800'
    ];

    while ($reservation = $reservations->fetch_assoc()) {
        $status = $reservation['status'];
        $status_color = $status_colors[$status] ?? 'bg-gray-100 text-gray-800';
        $check_in = date('M d, Y', strtotime($reservation['check_in_date']));
        $check_out = date('M d, Y', strtotime($reservation['check_out_date']));
        $special = !empty($reservation['special_requests']) ? '<br><span class="text-xs text-gray-500">(' . htmlspecialchars($reservation['special_requests']) . ')</span>' : '';
        $reservation_id = (int)$reservation['id'];

        $content .= <<<HTML
            <tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-2 font-semibold text-center">{$reservation_id}</td>
                <td class="py-3 px-2">
                    <div class="font-medium text-gray-900">{$reservation['room_type']}</div>
                    <div class="text-xs text-gray-500">Room #{$reservation['room_number']}</div>
                </td>
                <td class="py-3 px-2">
                    <div>{$check_in} <span class="text-gray-400">→</span> {$check_out}</div>
                    {$special}
                </td>
                <td class="py-3 px-2 text-right">\${$reservation['total_price']}</td>
                <td class="py-3 px-2 text-center">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {$status_color}">
                        {$status}
                    </span>
                </td>
                <td class="py-3 px-2 text-center">
HTML;
        // Only allow cancel if pending
        if ($status === 'pending') {
            $content .= <<<HTML
                    <form method="POST" action="cancel-reservation.php" onsubmit="return confirm('Cancel this reservation?');">
                        <input type="hidden" name="reservation_id" value="{$reservation_id}">
                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700 transition-colors">
                            Cancel
                        </button>
                    </form>
HTML;
        } else {
            $content .= '<span class="text-gray-400 text-xs">-</span>';
        }
        $content .= <<<HTML
                </td>
            </tr>
HTML;
    }

    $content .= <<<HTML
            </tbody>
        </table>
    </div>
HTML;
} else {
    $content .= <<<HTML
    <div class="text-center py-16">
        <i class="fas fa-calendar-times text-5xl text-gray-300 mb-4"></i>
        <h4 class="text-lg font-medium text-gray-800 mb-2">No Reservations Yet</h4>
        <p class="text-gray-600 mb-4">You haven't made any bookings yet. Start exploring our rooms!</p>
        <a href="room-booking.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Book Your First Room
        </a>
    </div>
HTML;
}

$content .= <<<HTML
    <div class="mt-8 text-center">
        <a href="dashboard.php" class="inline-block px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">Back to Dashboard</a>
    </div>
</div>
HTML;

include 'layouts/app.php';
?>
<!-- In the Add Reservation Modal (admin/reservations.php) -->
<div class="col-md-6 mb-3">
    <label class="form-label">User (Guest)</label>
    <select class="form-select" name="user_id" required>
        <option value="">Select User</option>
        <?php
        $users_query = "SELECT id, name, email FROM users WHERE role = 'user'";
        $users_result = $conn->query($users_query);
        while ($user = $users_result->fetch_assoc()):
        ?>
        <option value="<?php echo $user['id']; ?>">
            <?php echo htmlspecialchars($user['name'] . ' (' . $user['email'] . ')'); ?>
        </option>
        <?php endwhile; ?>
    </select>
</div>