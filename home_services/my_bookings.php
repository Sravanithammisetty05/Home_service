<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

require_login();

$user_id = (int)$_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT b.*, s.name AS service_name,
            w.name AS worker_name
     FROM bookings b
     JOIN services s ON b.service_id = s.id
     LEFT JOIN users w ON b.worker_id = w.id
     WHERE b.customer_id = ?
     ORDER BY b.created_at DESC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();

include 'includes/header.php';
?>

<div class="container">
    <h2>My Bookings</h2>

    <?php if (isset($_GET['success'])): ?>
        <p class="success">Your booking was created successfully!</p>
    <?php endif; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Address</th>
                    <th>Worker</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($bookings->num_rows > 0): ?>
                    <?php while ($booking = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$booking['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($booking['service_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($booking['booking_date']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($booking['booking_time']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($booking['address']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $booking['worker_name'] ?? 'Not assigned'
                                ) ?>
                            </td>

                            <td>
                                <span class="status">
                                    <?= htmlspecialchars($booking['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No bookings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="services.php" class="btn">Book Another Service</a>
</div>

<?php
$stmt->close();
include 'includes/footer.php';
?>