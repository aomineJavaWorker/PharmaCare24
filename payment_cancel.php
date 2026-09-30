<?php

$orderId =
    filter_input(
        INPUT_GET,
        'order_id',
        FILTER_VALIDATE_INT
    );

?>

<!DOCTYPE html>

<html lang="lv">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Maksājums atcelts | Aptieka</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>


<main class="payment-result-page">

    <div class="payment-result-card">

        <div class="payment-result-icon error">
            ×
        </div>


        <h1>
            Maksājums atcelts
        </h1>


        <p>
            Maksājums netika veikts.
            No kartes nauda nav noņemta.
        </p>


        <?php if ($orderId): ?>

            <p>
                Pasūtījums:
                <strong>
                    #<?= (int) $orderId ?>
                </strong>
            </p>

        <?php endif; ?>


        <a
            href="index.php"
            class="primary-button"
        >
            Atgriezties aptiekā
        </a>

    </div>

</main>


</body>

</html>