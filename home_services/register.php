<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    redirect('/home_services/index.php');
}

$errors = [];

$name = '';
$email = '';
$phone = '';
$address = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $errors[] = 'Please enter your name.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($phone === '') {
        $errors[] = 'Please enter your phone number.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must contain at least 8 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        if ($stmt->fetch()) {

            $errors[] = 'This email is already registered.';

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare(
                "INSERT INTO users
                (name, email, phone, password, role, address)
                VALUES (?, ?, ?, ?, 'customer', ?)"
            );

            $stmt->execute([
                $name,
                $email,
                $phone,
                $hashedPassword,
                $address
            ]);

            redirect('/home_services/login.php?registered=1');
        }
    }
}

$pageTitle = 'Customer Registration';

require_once __DIR__ . '/includes/header.php';

?>

<div class="row justify-content-center">

    <div class="col-md-7 col-lg-6">

        <div class="card shadow">

            <div class="card-body p-4">

                <h2 class="text-center mb-4">
                    Create Customer Account
                </h2>

                <?php foreach ($errors as $error): ?>

                    <div class="alert alert-danger">
                        <?= e($error) ?>
                    </div>

                <?php endforeach; ?>

                <form method="POST">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(csrf_token()) ?>">

                    <div class="mb-3">

                        <label class="form-label">
                            Full Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="<?= e($name) ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= e($email) ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Phone Number
                        </label>

                        <input
                            type="tel"
                            name="phone"
                            class="form-control"
                            value="<?= e($phone) ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Address
                        </label>

                        <textarea
                            name="address"
                            class="form-control"
                            rows="3"><?= e($address) ?></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            minlength="8"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Confirm Password
                        </label>

                        <input
                            type="password"
                            name="confirm_password"
                            class="form-control"
                            minlength="8"
                            required>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        Register

                    </button>

                </form>

                <p class="text-center mt-3">
                    Already registered?
                    <a href="login.php">Login here</a>
                </p>

            </div>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>