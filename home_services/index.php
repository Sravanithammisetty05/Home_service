<?php

require_once __DIR__ . '/config/database.php';

$pageTitle = 'Home - Home Services';

require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->query(
    "SELECT id, service_name, description, price
     FROM services
     WHERE status = 'active'
     ORDER BY id DESC
     LIMIT 6"
);

$services = $stmt->fetchAll();

?>

<section class="hero-section text-center">

    <h1>Welcome to Home Services</h1>

    <p class="lead">
        Find trusted professionals for your everyday home needs.
    </p>

    <a href="services.php"
       class="btn btn-light btn-lg mt-3">
        Explore Services
    </a>

</section>

<section class="mt-5">

    <h2 class="text-center mb-4">
        Our Popular Services
    </h2>

    <div class="row">

        <?php foreach ($services as $service): ?>

            <div class="col-md-4 mb-4">

                <div class="card service-card h-100">

                    <div class="card-body">

                        <h5 class="card-title">
                            <?= e($service['service_name']) ?>
                        </h5>

                        <p class="card-text">
                            <?= e($service['description']) ?>
                        </p>

                        <h5>
                            ₹<?= number_format(
                                (float) $service['price'],
                                2
                            ) ?>
                        </h5>

                        <a
                            href="booking.php?service_id=<?= (int) $service['id'] ?>"
                            class="btn btn-primary mt-2">

                            Book Service

                        </a>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</section>

<section class="text-center mt-5">

    <h2>How It Works</h2>

    <div class="row mt-4">

        <div class="col-md-4 mb-3">
            <h4>1. Select</h4>
            <p>Choose the service you need.</p>
        </div>

        <div class="col-md-4 mb-3">
            <h4>2. Book</h4>
            <p>Select your preferred date and time.</p>
        </div>

        <div class="col-md-4 mb-3">
            <h4>3. Get Service</h4>
            <p>Receive service from an assigned worker.</p>
        </div>

    </div>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>