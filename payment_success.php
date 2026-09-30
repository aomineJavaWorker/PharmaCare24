<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/stripe.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/order_fulfillment.php';


$sessionId =
    trim($_GET['session_id'] ?? '');


$success = false;

$orderId = null;

$message =
    'Neizdevās pārbaudīt maksājumu.';


if ($sessionId !== '') {

    try {

        $stripe =
            new \Stripe\StripeClient(
                stripeSecretKey()
            );


        $session =
            $stripe
                ->checkout
                ->sessions
                ->retrieve(
                    $sessionId,
                    []
                );


        if (
            $session->payment_status
            === 'paid'
        ) {

            $orderId =
                fulfillPaidOrder(
                    $pdo,
                    $session
                );


            $success = true;


            $message =
                'Maksājums ir veiksmīgi saņemts.';

        } else {

            $message =
                'Maksājums vēl nav pabeigts.';
        }


    } catch (Throwable $e) {

        $message =
            'Neizdevās apstiprināt maksājumu.';
    }
}

?>

<!DOCTYPE html>

<html lang="lv">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Maksājums | Aptieka</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>


<main class="payment-result-page">

    <div class="payment-result-card">

        <?php if ($success): ?>

            <div class="payment-result-icon">
                ✓
            </div>

            <h1>
                Maksājums veiksmīgs
            </h1>

            <p>
                <?= htmlspecialchars($message) ?>
            </p>

            <?php if ($orderId): ?>

                <p>
                    Pasūtījuma numurs:
                    <strong>
                        #<?= (int) $orderId ?>
                    </strong>
                </p>

            <?php endif; ?>


            <a
                href="index.php"
                class="primary-button"
            >
                Atgriezties sākumā
            </a>


            <script>
                localStorage.removeItem(
                    'aptieka_cart'
                );
            </script>


        <?php else: ?>

            <div class="payment-result-icon error">
                !
            </div>

            <h1>
                Maksājumu neizdevās apstiprināt
            </h1>

            <p>
                <?= htmlspecialchars($message) ?>
            </p>

            <a
                href="index.php"
                class="primary-button"
            >
                Atgriezties
            </a>

        <?php endif; ?>

    </div>

</main>


</body>

</html>