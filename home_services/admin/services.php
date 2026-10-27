<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

require_login();

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied.');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);

    if ($name === '' || $price === false || $price < 0) {
        $error = 'Enter a service name and valid non-negative price.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO services (name, description, price, status)
             VALUES (?, ?, ?, 'active')"
        );

        $stmt->bind_param("ssd", $name, $description, $price);

        if ($stmt->execute()) {
            $success = 'Service added successfully.';
        } else {
            $error = 'Unable to add service.';
        }

        $stmt->close();
    }
}

$result = $conn->query(
    "SELECT id, name, description, price, status
     FROM services
     ORDER BY id DESC"
);

include '../includes/header.php';
?>

<div class="container">
    <h2>Manage Services</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <h3>Add New Service</h3>

    <form method="POST" class="form-box">
        <label>Service Name</label>
        <input type="text" name="name" required>

        <label>Description</label>
        <textarea name="description" rows="3"></textarea>

        <label>Price (₹)</label>
        <input
            type="number"
            name="price"
            min="0"
            step="0.01"
            required
        >

        <button type="submit" class="btn">Add Service</button>
    </form>

    <h3>Existing Services</h3>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($service = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$service['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($service['name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($service['description'] ?? '') ?>
                            </td>

                            <td>
                                ₹<?= number_format((float)$service['price'], 2) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($service['status']) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="5">No services available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="dashboard.php" class="btn">Back to Dashboard</a>
</div>

<?php include '../includes/footer.php'; ?>