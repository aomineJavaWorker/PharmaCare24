<?php


function fulfillPaidOrder(
    PDO $pdo,
    \Stripe\Checkout\Session $session
): int {

    if ($session->payment_status !== 'paid') {

        throw new RuntimeException(
            'Maksājums nav pabeigts.'
        );
    }


    $orderId =
        (int) (
            $session->metadata->order_id
            ?? 0
        );


    if ($orderId <= 0) {

        throw new RuntimeException(
            'Pasūtījums nav norādīts.'
        );
    }


    /*
     * =========================
     * KLIENTA DATI NO STRIPE
     * =========================
     */

    $customerEmail = null;

    $deliveryName = null;

    $deliveryAddress = null;

    $deliveryCity = null;

    $deliveryPostalCode = null;

    $deliveryCountry = null;


    /*
     * E-pasts un klienta vārds
     */

    if (
        isset(
            $session
                ->customer_details
        )
    ) {

        $customerDetails =
            $session
                ->customer_details;


        $customerEmail =
            $customerDetails->email
            ?? null;


        $deliveryName =
            $customerDetails->name
            ?? null;
    }


    /*
     * Piegādes adrese
     */

    if (
        isset(
            $session
                ->shipping_details
        ) &&
        $session->shipping_details
    ) {

        $shippingDetails =
            $session
                ->shipping_details;


        if (
            !empty(
                $shippingDetails->name
            )
        ) {

            $deliveryName =
                $shippingDetails->name;
        }


        if (
            isset(
                $shippingDetails->address
            )
        ) {

            $address =
                $shippingDetails->address;


            $addressParts = [];


            if (!empty($address->line1)) {

                $addressParts[] =
                    $address->line1;
            }


            if (!empty($address->line2)) {

                $addressParts[] =
                    $address->line2;
            }


            if ($addressParts) {

                $deliveryAddress =
                    implode(
                        ', ',
                        $addressParts
                    );
            }


            $deliveryCity =
                $address->city
                ?? null;


            $deliveryPostalCode =
                $address->postal_code
                ?? null;


            $deliveryCountry =
                $address->country
                ?? null;
        }
    }


    /*
     * Ja shipping_details nav,
     * mēģinām izmantot customer_details.address.
     */

    if (
        $deliveryAddress === null &&
        isset(
            $session
                ->customer_details
                ->address
        )
    ) {

        $address =
            $session
                ->customer_details
                ->address;


        $addressParts = [];


        if (!empty($address->line1)) {

            $addressParts[] =
                $address->line1;
        }


        if (!empty($address->line2)) {

            $addressParts[] =
                $address->line2;
        }


        if ($addressParts) {

            $deliveryAddress =
                implode(
                    ', ',
                    $addressParts
                );
        }


        $deliveryCity =
            $address->city
            ?? null;


        $deliveryPostalCode =
            $address->postal_code
            ?? null;


        $deliveryCountry =
            $address->country
            ?? null;
    }


    /*
     * =========================
     * PASŪTĪJUMA APSTRĀDE
     * =========================
     */

    $pdo->beginTransaction();


    try {

        /*
         * Bloķējam pasūtījumu,
         * lai tas netiktu apstrādāts
         * divas reizes.
         */

        $orderStmt = $pdo->prepare(
            'SELECT
                id,
                payment_status
             FROM orders
             WHERE id = ?
             AND stripe_session_id = ?
             FOR UPDATE'
        );


        $orderStmt->execute([
            $orderId,
            $session->id
        ]);


        $order =
            $orderStmt->fetch();


        if (!$order) {

            throw new RuntimeException(
                'Pasūtījums nav atrasts.'
            );
        }


        /*
         * Saglabājam klienta un
         * piegādes datus.
         *
         * To darām arī tad, ja pasūtījums
         * jau iepriekš tika atzīmēts
         * kā apmaksāts.
         */

        $customerStmt = $pdo->prepare(
            'UPDATE orders
             SET
                customer_email =
                    COALESCE(?, customer_email),

                delivery_name =
                    COALESCE(?, delivery_name),

                delivery_address =
                    COALESCE(?, delivery_address),

                delivery_city =
                    COALESCE(?, delivery_city),

                delivery_postal_code =
                    COALESCE(?, delivery_postal_code),

                delivery_country =
                    COALESCE(?, delivery_country)

             WHERE id = ?'
        );


        $customerStmt->execute([
            $customerEmail,
            $deliveryName,
            $deliveryAddress,
            $deliveryCity,
            $deliveryPostalCode,
            $deliveryCountry,
            $orderId
        ]);


        /*
         * Ja pasūtījums jau ir apmaksāts,
         * preci otro reizi no noliktavas
         * neatņemam.
         */

        if (
            $order['payment_status']
            === 'Apmaksāts'
        ) {

            $pdo->commit();

            return $orderId;
        }


        /*
         * =========================
         * PASŪTĪJUMA PRECES
         * =========================
         */

        $itemsStmt = $pdo->prepare(
            'SELECT
                order_items.medicine_id,
                order_items.prescription_id,
                order_items.quantity,
                medicines.stock_quantity

             FROM order_items

             INNER JOIN medicines
                ON order_items.medicine_id =
                   medicines.id

             WHERE order_items.order_id = ?

             FOR UPDATE'
        );


        $itemsStmt->execute([
            $orderId
        ]);


        $items =
            $itemsStmt->fetchAll();


        if (!$items) {

            throw new RuntimeException(
                'Pasūtījumā nav zāļu.'
            );
        }


        /*
         * Pārbaudām atlikumu.
         */

        foreach ($items as $item) {

            if (
                (int) $item[
                    'stock_quantity'
                ]
                <
                (int) $item[
                    'quantity'
                ]
            ) {

                throw new RuntimeException(
                    'Noliktavā nav pietiekams zāļu daudzums.'
                );
            }
        }


        /*
         * =========================
         * NOLIKTAVAS ATJAUNINĀŠANA
         * =========================
         */

        $stockStmt = $pdo->prepare(
            'UPDATE medicines

             SET stock_quantity =
                 stock_quantity - ?

             WHERE id = ?
             AND stock_quantity >= ?'
        );


        $prescriptionIds = [];


        foreach ($items as $item) {

            $quantity =
                (int) $item[
                    'quantity'
                ];


            $medicineId =
                (int) $item[
                    'medicine_id'
                ];


            $stockStmt->execute([
                $quantity,
                $medicineId,
                $quantity
            ]);


            if (
                $stockStmt->rowCount()
                !== 1
            ) {

                throw new RuntimeException(
                    'Neizdevās atjaunināt noliktavas atlikumu.'
                );
            }


            if (
                $item[
                    'prescription_id'
                ] !== null
            ) {

                $prescriptionIds[] =
                    (int) $item[
                        'prescription_id'
                    ];
            }
        }


        /*
         * =========================
         * RECEPTES STATUSS
         * =========================
         */

        if ($prescriptionIds) {

            $prescriptionIds =
                array_unique(
                    $prescriptionIds
                );


            $prescriptionStmt =
                $pdo->prepare(
                    "UPDATE prescriptions

                     SET status = 'Izmantota'

                     WHERE id = ?
                     AND status = 'Aktīva'"
                );


            foreach (
                $prescriptionIds
                as $prescriptionId
            ) {

                $prescriptionStmt->execute([
                    $prescriptionId
                ]);
            }
        }


        /*
         * =========================
         * MAKSĀJUMS APSTIPRINĀTS
         * =========================
         */

        $paidStmt = $pdo->prepare(
            "UPDATE orders

             SET
                payment_status = 'Apmaksāts',

                customer_email =
                    COALESCE(?, customer_email),

                delivery_name =
                    COALESCE(?, delivery_name),

                delivery_address =
                    COALESCE(?, delivery_address),

                delivery_city =
                    COALESCE(?, delivery_city),

                delivery_postal_code =
                    COALESCE(
                        ?,
                        delivery_postal_code
                    ),

                delivery_country =
                    COALESCE(
                        ?,
                        delivery_country
                    )

             WHERE id = ?"
        );


        $paidStmt->execute([
            $customerEmail,
            $deliveryName,
            $deliveryAddress,
            $deliveryCity,
            $deliveryPostalCode,
            $deliveryCountry,
            $orderId
        ]);


        $pdo->commit();


        return $orderId;


    } catch (Throwable $e) {

        if ($pdo->inTransaction()) {

            $pdo->rollBack();
        }


        throw $e;
    }
}