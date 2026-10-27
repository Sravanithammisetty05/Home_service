<?php

require_once __DIR__ . '/config/database.php';

$pageTitle = 'Our Services';

$stmt = $pdo->query(
    "SELECT * FROM services
     WHERE status = 'active'
     ORDER BY service_name"
);

$services = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';

?>

<h1 class="text-center mb-4">Our Services</h1>

<div class="row">

    <?php if (empty($services)): ?>

        <div class="col-12">
            <div class="alert alert-info">
                No services are currently available.
            </div>
        </div>

    <?php endif; ?>

    <?php foreach ($services as $service): ?>

        <div class="col-md-4 mb-4">

            <div class="card service-card h-100">

                <div class="card-body">

                    <h4>
                        <?= e($service['service_name']) ?>
                    </h4>

                    <p>
                        <?= e($service['description']) ?>
                    </p>

                    <h5 class="text-primary">
                        ₹<?= number_format(
                            (float) $service['price'],
                            2
                        ) ?>
                    </h5>

                    <a
                        href="booking.php?service_id=<?= (int) $service['id'] ?>"
                        class="btn btn-primary">

                        Book Now

                    </a>

                </div>

            </div>

        </div>

    <?php endforeach; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>