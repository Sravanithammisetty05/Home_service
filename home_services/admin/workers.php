<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

require_login();

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied. Admin access is required.');
}

function getCount($conn, $sql) {
    $result = $conn->query($sql);

    if (!$result) {
        return 0;
    }

    return (int)$result->fetch_assoc()['total'];
}

$total_users = getCount(
    $conn,
    "SELECT COUNT(*) AS total FROM users"
);

$total_workers = getCount(
    $conn,
    "SELECT COUNT(*) AS total FROM users WHERE role = 'worker'"
);

$total_customers = getCount(
    $conn,
    "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'"
);

$total_services = getCount(
    $conn,
    "SELECT COUNT(*) AS total FROM services"
);

$total_bookings = getCount(
    $conn,
    "SELECT COUNT(*) AS total FROM bookings"
);

include '../includes/header.php';
?>

<div class="container">
    <h2>Admin Dashboard</h2>

    <p>
        Welcome,
        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>!
    </p>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h3>Total Users</h3>
            <p><?= $total_users ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Total Workers</h3>
            <p><?= $total_workers ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Total Customers</h3>
            <p><?= $total_customers ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Total Services</h3>
            <p><?= $total_services ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Total Bookings</h3>
            <p><?= $total_bookings ?></p>
        </div>

    </div>

    <h3>Admin Management</h3>

    <div class="dashboard-links">
        <a href="workers.php" class="btn">Manage Workers</a>
        <a href="customers.php" class="btn">Manage Customers</a>
        <a href="services.php" class="btn">Manage Services</a>
        <a href="bookings.php" class="btn">Manage Bookings</a>
        <a href="reports.php" class="btn">View Reports</a>
        <a href="../logout.php" class="btn">Logout</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>