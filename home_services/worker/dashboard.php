<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

require_login();

$worker_id = (int)$_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT b.*, s.name AS service_name,
            c.name AS customer_name,
            c.phone AS customer_phone
     FROM bookings b
     JOIN services s ON b.service_id = s.id
     JOIN users c ON b.customer_id = c.id
     WHERE b.worker_id = ?
     ORDER BY b.booking_date DESC, b.booking_time DESC"
);

$stmt->bind_param("i", $worker_id);
$stmt->execute();
$jobs = $stmt->get_result();

include '../includes/header.php';
?>

<div class="container">
    <h2>Worker Dashboard</h2>

    <p>
        Welcome,
        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Worker') ?>!
    </p>

    <h3>My Assigned Jobs</h3>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Address</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($jobs->num_rows > 0): ?>
                    <?php while ($job = $jobs->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$job['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($job['customer_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($job['customer_phone'] ?? '') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($job['service_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($job['booking_date']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($job['booking_time']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($job['address']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($job['status']) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No jobs assigned yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="../logout.php" class="btn">Logout</a>
</div>

<?php
$stmt->close();
include '../includes/footer.php';
?>