<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

require_login();

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied.');
}

$result = $conn->query(
    "SELECT status, COUNT(*) AS total
     FROM bookings
     GROUP BY status
     ORDER BY status"
);

include '../includes/header.php';
?>

<div class="container">
    <h2>Booking Reports</h2>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Booking Status</th>
                    <th>Total Bookings</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars(ucfirst($row['status'])) ?>
                            </td>

                            <td><?= (int)$row['total'] ?></td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="2">No report data available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="dashboard.php" class="btn">Back to Dashboard</a>
</div>

<?php include '../includes/footer.php'; ?>