<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');


function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    jsonResponse([
        'success' => false,
        'message' => 'Metode nav atļauta.'
    ], 405);
}


$data = json_decode(
    file_get_contents('php://input'),
    true
);


if (!is_array($data)) {

    jsonResponse([
        'success' => false,
        'message' => 'Nederīgi nosūtītie dati.'
    ], 400);
}


$items = $data['items'] ?? [];

$patientName = trim(
    $data['patient_name'] ?? ''
);

$personalCode = trim(
    $data['personal_code'] ?? ''
);


if (
    !is_array($items) ||
    count($items) === 0
) {

    jsonResponse([
        'success' => false,
        'message' => 'Grozs ir tukšs.'
    ], 422);
}


$stripeSecretKey =
    stripeSecretKey();


if (
    $stripeSecretKey === '' ||
    !str_starts_with(
        $stripeSecretKey,
        'sk_'
    )
) {

    jsonResponse([
        'success' => false,
        'message' => 'Stripe Secret Key nav konfigurēts.'
    ], 500);
}


try {

    /*
     * Zāles vienmēr ielādējam no DB.
     *
     * Cenu no JavaScript neizmantojam.
     */

    $medicineStmt = $pdo->prepare(
        'SELECT
            id,
            name,
            price,
            stock_quantity,
            prescription_required
         FROM medicines
         WHERE id = ?
         AND deleted_at IS NULL
         LIMIT 1'
    );


    $prescriptionStmt = $pdo->prepare(
        "SELECT
            prescriptions.id,
            prescriptions.prescription_number,
            prescription_items.quantity
         FROM prescriptions
         INNER JOIN prescription_items
            ON prescriptions.id =
               prescription_items.prescription_id
         WHERE prescriptions.patient_name = ?
         AND prescriptions.personal_code = ?
         AND prescription_items.medicine_id = ?
         AND prescriptions.status = 'Aktīva'
         AND prescriptions.deleted_at IS NULL
         AND prescriptions.issued_at <= CURDATE()
         AND prescriptions.valid_until >= CURDATE()
         AND prescription_items.quantity >= ?
         ORDER BY prescriptions.valid_until ASC
         LIMIT 1"
    );


    $normalizedItems = [];

    $stripeLineItems = [];

    $totalCents = 0;

    $requiresPrescription = false;


    foreach ($items as $item) {

        $medicineId =
            (int) ($item['medicine_id'] ?? 0);

        $quantity =
            (int) ($item['quantity'] ?? 0);


        if (
            $medicineId <= 0 ||
            $quantity <= 0
        ) {

            jsonResponse([
                'success' => false,
                'message' => 'Grozā ir nederīgi dati.'
            ], 422);
        }


        $medicineStmt->execute([
            $medicineId
        ]);


        $medicine =
            $medicineStmt->fetch();


        if (!$medicine) {

            jsonResponse([
                'success' => false,
                'message' => 'Viena no izvēlētajām zālēm vairs nav pieejama.'
            ], 404);
        }


        if (
            $quantity >
            (int) $medicine['stock_quantity']
        ) {

            jsonResponse([
                'success' => false,
                'message' =>
                    'Nepietiekams zāļu "' .
                    $medicine['name'] .
                    '" daudzums noliktavā.'
            ], 422);
        }


        $prescriptionId = null;


        if (
            (int) $medicine[
                'prescription_required'
            ] === 1
        ) {

            $requiresPrescription = true;


            if (
                $patientName === '' ||
                $personalCode === ''
            ) {

                jsonResponse([
                    'success' => false,
                    'message' => 'Recepšu zālēm nepieciešama receptes pārbaude.'
                ], 422);
            }


            if (
                !preg_match(
                    '/^\d{6}-\d{5}$/',
                    $personalCode
                )
            ) {

                jsonResponse([
                    'success' => false,
                    'message' => 'Nederīgs personas kods.'
                ], 422);
            }


            $prescriptionStmt->execute([
                $patientName,
                $personalCode,
                $medicineId,
                $quantity
            ]);


            $prescription =
                $prescriptionStmt->fetch();


            if (!$prescription) {

                jsonResponse([
                    'success' => false,
                    'message' =>
                        'Aktīva un derīga recepte zālēm "' .
                        $medicine['name'] .
                        '" nav atrasta.'
                ], 422);
            }


            $prescriptionId =
                (int) $prescription['id'];
        }


        /*
         * EUR -> centi.
         */

        $unitAmount =
            (int) round(
                (float) $medicine['price']
                * 100
            );


        $totalCents +=
            $unitAmount * $quantity;


        $normalizedItems[] = [
            'medicine_id' =>
                $medicineId,

            'name' =>
                $medicine['name'],

            'quantity' =>
                $quantity,

            'unit_amount' =>
                $unitAmount,

            'prescription_id' =>
                $prescriptionId
        ];


        $stripeLineItems[] = [

            'price_data' => [

                'currency' => 'eur',

                'product_data' => [
                    'name' =>
                        $medicine['name']
                ],

                'unit_amount' =>
                    $unitAmount
            ],

            'quantity' =>
                $quantity
        ];
    }


    /*
     * Pasūtījumu izveidojam pirms Stripe.
     */

    $pdo->beginTransaction();


    $customerName =
        $requiresPrescription
            ? $patientName
            : 'Klients';


    $total =
        number_format(
            $totalCents / 100,
            2,
            '.',
            ''
        );


    $orderStmt = $pdo->prepare(
        "INSERT INTO orders
            (
                customer_name,
                customer_email,
                status,
                payment_status,
                total
            )
         VALUES
            (?, NULL, 'Jauns', 'Neapmaksāts', ?)"
    );


    $orderStmt->execute([
        $customerName,
        $total
    ]);


    $orderId =
        (int) $pdo->lastInsertId();


    $itemStmt = $pdo->prepare(
        'INSERT INTO order_items
            (
                order_id,
                medicine_id,
                prescription_id,
                quantity,
                unit_price
            )
         VALUES (?, ?, ?, ?, ?)'
    );


    foreach (
        $normalizedItems
        as $item
    ) {

        $itemStmt->execute([

            $orderId,

            $item['medicine_id'],

            $item['prescription_id'],

            $item['quantity'],

            number_format(
                $item['unit_amount'] / 100,
                2,
                '.',
                ''
            )
        ]);
    }


    $pdo->commit();


    /*
     * Izveidojam Stripe Checkout Session.
     */

    $stripe =
        new \Stripe\StripeClient(
            $stripeSecretKey
        );


    /*
     * Automātiski nosakām projekta URL.
     *
     * Piemēram:
     * http://localhost/WEB-final-Denisenko
     */

    $https =
        !empty($_SERVER['HTTPS']) &&
        $_SERVER['HTTPS'] !== 'off';


    $scheme =
        $https ? 'https' : 'http';


    $host =
        $_SERVER['HTTP_HOST']
        ?? 'localhost';


    $scriptName =
        $_SERVER['SCRIPT_NAME']
        ?? '';


    $basePath =
        dirname(
            dirname($scriptName)
        );


    $basePath =
        str_replace(
            '\\',
            '/',
            $basePath
        );


    $basePath =
        rtrim(
            $basePath,
            '/'
        );


    $baseUrl =
        $scheme .
        '://' .
        $host .
        $basePath;


    try {

        $session =
            $stripe
                ->checkout
                ->sessions
                ->create([

                    'mode' => 'payment',

                    'line_items' =>
                        $stripeLineItems,

                    'shipping_address_collection' => [
                        'allowed_countries' => [
                            'LV'
                        ]
                    ],

                    'success_url' =>
                        $baseUrl .
                        '/payment_success.php' .
                        '?session_id={CHECKOUT_SESSION_ID}',

                    'cancel_url' =>
                        $baseUrl .
                        '/payment_cancel.php' .
                        '?order_id=' .
                        $orderId,

                    'client_reference_id' =>
                        (string) $orderId,

                    'metadata' => [
                        'order_id' =>
                            (string) $orderId
                    ]
                ]);


        $updateStmt = $pdo->prepare(
            'UPDATE orders
             SET stripe_session_id = ?
             WHERE id = ?'
        );


        $updateStmt->execute([
            $session->id,
            $orderId
        ]);


        jsonResponse([
            'success' => true,
            'url' => $session->url
        ]);


    } catch (
        \Stripe\Exception\ApiErrorException $e
    ) {

        $failedStmt = $pdo->prepare(
            "UPDATE orders
             SET payment_status = 'Neizdevās'
             WHERE id = ?"
        );


        $failedStmt->execute([
            $orderId
        ]);


        jsonResponse([
            'success' => false,
            'message' => 'Stripe neizdevās izveidot maksājumu.'
        ], 502);
    }


} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    jsonResponse([
        'success' => false,
        'message' => 'Neizdevās izveidot pasūtījumu.'
    ], 500);


} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    jsonResponse([
        'success' => false,
        'message' => 'Radās neparedzēta kļūda.'
    ], 500);
}