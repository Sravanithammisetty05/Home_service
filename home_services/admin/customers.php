<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

require_login();

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied.');
}

$result = $conn->query(
    "SELECT id, name, email, phone
     FROM users
     WHERE role = 'customer'
     ORDER BY id DESC"
);

include '../includes/header.php';
?>

<div class="container">
    <h2>Manage Customers</h2>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($customer = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$customer['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($customer['name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($customer['email']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($customer['phone'] ?? '') ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="4">No customers registered.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="dashboard.php" class="btn">Back to Dashboard</a>
</div>

<?php include '../includes/footer.php'; ?>