<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = (int)($_POST['service_id'] ?? 0);
    $booking_date = $_POST['booking_date'] ?? '';
    $booking_time = $_POST['booking_time'] ?? '';
    $address = trim($_POST['address'] ?? '');

    if ($service_id <= 0 || !$booking_date || !$booking_time || !$address) {
        $error = "Please fill in all fields.";
    } elseif ($booking_date < date('Y-m-d')) {
        $error = "Please select today or a future date.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id FROM services WHERE id = ? AND status = 'active'"
        );
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $service = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$service) {
            $error = "Please select a valid service.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO bookings
                (customer_id, service_id, booking_date, booking_time,
                 address, status)
                VALUES (?, ?, ?, ?, ?, 'pending')"
            );

            $stmt->bind_param(
                "iisss",
                $_SESSION['user_id'],
                $service_id,
                $booking_date,
                $booking_time,
                $address
            );

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: my_bookings.php?success=1");
                exit;
            }

            $error = "Booking failed. Please try again.";
            $stmt->close();
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <h2>Book a Home Service</h2>

    <?php if (!empty($error)): ?>
        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="form-box">
        <label>Select Service</label>

        <select name="service_id" required>
            <option value="">Choose a service</option>

            <?php
            $result = $conn->query(
                "SELECT id, name FROM services
                 WHERE status = 'active' ORDER BY name"
            );

            while ($service = $result->fetch_assoc()):
            ?>
                <option
                    value="<?= (int)$service['id'] ?>"
                    <?= (isset($_POST['service_id']) &&
                         (int)$_POST['service_id'] === (int)$service['id'])
                         ? 'selected' : '' ?>>
                    <?= htmlspecialchars($service['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Booking Date</label>
        <input
            type="date"
            name="booking_date"
            min="<?= date('Y-m-d') ?>"
            value="<?= htmlspecialchars($_POST['booking_date'] ?? '') ?>"
            required
        >

        <label>Booking Time</label>
        <input
            type="time"
            name="booking_time"
            value="<?= htmlspecialchars($_POST['booking_time'] ?? '') ?>"
            required
        >

        <label>Full Address</label>
        <textarea
            name="address"
            rows="4"
            required
        ><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>

        <button type="submit" class="btn">
            Confirm Booking
        </button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>