<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

require_login();

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied.');
}

$error = '';
$allowed_statuses = [
    'pending',
    'confirmed',
    'completed',
    'cancelled'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = filter_input(
        INPUT_POST,
        'booking_id',
        FILTER_VALIDATE_INT
    );

    $worker_id = ($_POST['worker_id'] ?? '') === ''
        ? null
        : filter_var($_POST['worker_id'], FILTER_VALIDATE_INT);

    $status = $_POST['status'] ?? '';

    if (
        !$booking_id ||
        !in_array($status, $allowed_statuses, true) ||
        ($worker_id !== null && !$worker_id)
    ) {
        $error = 'Invalid booking details.';
    } else {
        if ($worker_id !== null) {
            $check = $conn->prepare(
                "SELECT id FROM users WHERE id = ? AND role = 'worker'"
            );

            $check->bind_param("i", $worker_id);
            $check->execute();
            $valid_worker = $check->get_result()->fetch_assoc();
            $check->close();

            if (!$valid_worker) {
                $error = 'Please select a valid worker.';
            }
        }

        if ($error === '') {
            $stmt = $conn->prepare(
                "UPDATE bookings
                 SET worker_id = ?, status = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "isi",
                $worker_id,
                $status,
                $booking_id
            );

            if ($stmt->execute() && $stmt->affected_rows >= 0) {
                $stmt->close();

                header('Location: bookings.php?updated=1');
                exit;
            }

            $error = 'Unable to update booking.';
            $stmt->close();
        }
    }
}

$workers = $conn->query(
    "SELECT id, name FROM users
     WHERE role = 'worker'
     ORDER BY name"
);

$worker_list = [];

if ($workers) {
    while ($worker = $workers->fetch_assoc()) {
        $worker_list[] = $worker;
    }
}

$result = $conn->query(
    "SELECT b.id, b.booking_date, b.booking_time,
            b.address, b.status, b.worker_id,
            s.name AS service_name,
            c.name AS customer_name
     FROM bookings b
     JOIN services s ON b.service_id = s.id
     JOIN users c ON b.customer_id = c.id
     ORDER BY b.id DESC"
);

include '../includes/header.php';
?>

<div class="container">
    <h2>Manage Bookings</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <p class="success">Booking updated successfully.</p>
    <?php endif; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Address</th>
                    <th>Assign Worker</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($booking = $result->fetch_assoc()): ?>
                        <tr>
                            <form method="POST">
                                <td>
                                    <?= (int)$booking['id'] ?>

                                    <input
                                        type="hidden"
                                        name="booking_id"
                                        value="<?= (int)$booking['id'] ?>"
                                    >
                                </td>

                                <td>
                                    <?= htmlspecialchars($booking['customer_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($booking['service_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($booking['booking_date']) ?>
                                    <br>
                                    <?= htmlspecialchars($booking['booking_time']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($booking['address']) ?>
                                </td>

                                <td>
                                    <select name="worker_id">
                                        <option value="">Not assigned</option>

                                        <?php foreach ($worker_list as $worker): ?>
                                            <option
                                                value="<?= (int)$worker['id'] ?>"
                                                <?= (int)$booking['worker_id'] ===
                                                    (int)$worker['id']
                                                    ? 'selected' : '' ?>
                                            >
                                                <?= htmlspecialchars($worker['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>

                                <td>
                                    <select name="status">
                                        <?php foreach ($allowed_statuses as $status): ?>
                                            <option
                                                value="<?= htmlspecialchars($status) ?>"
                                                <?= $booking['status'] === $status
                                                    ? 'selected' : '' ?>
                                            >
                                                <?= htmlspecialchars(ucfirst($status)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>

                                <td>
                                    <button type="submit" class="btn">
                                        Update
                                    </button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="8">No bookings available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="dashboard.php" class="btn">Back to Dashboard</a>
</div>

<?php include '../includes/footer.php'; ?>