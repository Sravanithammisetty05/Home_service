<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

require_login();

$user_id = (int)$_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM bookings WHERE customer_id = ?"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_bookings = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM bookings
     WHERE customer_id = ? AND status = 'pending'"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pending_bookings = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

include '../includes/header.php';
?>

<div class="container">
    <h2>Customer Dashboard</h2>

    <p>
        Welcome,
        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Customer') ?>!
    </p>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>Total Bookings</h3>
            <p><?= (int)$total_bookings ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Pending Bookings</h3>
            <p><?= (int)$pending_bookings ?></p>
        </div>
    </div>

    <div class="dashboard-links">
        <a href="../services.php" class="btn">Browse Services</a>
        <a href="../booking.php" class="btn">Book a Service</a>
        <a href="../my_bookings.php" class="btn">My Bookings</a>
        <a href="../logout.php" class="btn">Logout</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>