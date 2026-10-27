<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    redirect('/home_services/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT id, name, email, password, role
         FROM users
         WHERE email = ?
         LIMIT 1"
    );

    $stmt->execute([$email]);

    $user = $stmt->fetch();

    if (
        $user &&
        password_verify($password, $user['password']) &&
        in_array($user['role'], ['customer', 'worker'], true)
    ) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'customer') {
            redirect('/home_services/customer/dashboard.php');
        }

        redirect('/home_services/worker/dashboard.php');

    } else {

        $error = 'Invalid email or password.';
    }
}

$pageTitle = 'Login';

require_once __DIR__ . '/includes/header.php';

?>

<div class="row justify-content-center">

    <div class="col-md-6 col-lg-5">

        <div class="card shadow">

            <div class="card-body p-4">

                <h2 class="text-center mb-4">
                    Login
                </h2>

                <?php if (isset($_GET['registered'])): ?>

                    <div class="alert alert-success">
                        Registration successful. Please log in.
                    </div>

                <?php endif; ?>

                <?php if ($error !== ''): ?>

                    <div class="alert alert-danger">
                        <?= e($error) ?>
                    </div>

                <?php endif; ?>

                <form method="POST">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(csrf_token()) ?>">

                    <div class="mb-3">

                        <label class="form-label">
                            Email Address
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        Login

                    </button>

                </form>

                <p class="text-center mt-3">
                    New customer?
                    <a href="register.php">Create an account</a>
                </p>

                <p class="text-center">
                    <a href="admin/admin_login.php">
                        Administrator Login
                    </a>
                </p>

            </div>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>