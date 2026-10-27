<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_admin = ($_SESSION['role'] ?? '') === 'admin';
$is_worker = ($_SESSION['role'] ?? '') === 'worker';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Home Services Booking System</title>

    <link
        rel="stylesheet"
        href="/home_services/assets/css/style.css"
    >
</head>

<body>

<header>
    <h1>Home Services</h1>

    <nav>
        <a href="/home_services/index.php">Home</a>

        <a href="/home_services/services.php">Services</a>

        <?php if (isset($_SESSION['user_id'])): ?>

            <?php if ($is_admin): ?>
                <a href="/home_services/admin/dashboard.php">Admin Dashboard</a>

            <?php elseif ($is_worker): ?>
                <a href="/home_services/worker/dashboard.php">Worker Dashboard</a>

            <?php else: ?>
                <a href="/home_services/customer/dashboard.php">My Dashboard</a>
                <a href="/home_services/my_bookings.php">My Bookings</a>
            <?php endif; ?>

            <a href="/home_services/logout.php">Logout</a>

        <?php else: ?>

            <a href="/home_services/login.php">Login</a>
            <a href="/home_services/register.php">Register</a>

        <?php endif; ?>
    </nav>
</header>