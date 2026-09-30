<?php

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/audit_log.php';

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


if (!isset($_SESSION['user_id'])) {

    jsonResponse([
        'success' => false,
        'message' => 'Nav autorizācijas.'
    ], 401);
}


$method = $_SERVER['REQUEST_METHOD'];


/* =========================================================
   GET
   ========================================================= */

if ($method === 'GET') {

    $id = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );


    /* =====================================================
       VIENS PASŪTĪJUMS
       ===================================================== */

    if ($id) {

        try {

            $stmt = $pdo->prepare(
                'SELECT
                    id,
                    customer_name,
                    customer_email,
                    delivery_name,
                    delivery_address,
                    delivery_city,
                    delivery_postal_code,
                    delivery_country,
                    status,
                    payment_status,
                    total,
                    stripe_session_id,
                    created_at,
                    updated_at

                 FROM orders

                 WHERE id = ?
                 AND deleted_at IS NULL

                 LIMIT 1'
            );


            $stmt->execute([
                $id
            ]);


            $order =
                $stmt->fetch();


            if (!$order) {

                jsonResponse([
                    'success' => false,
                    'message' => 'Pasūtījums nav atrasts.'
                ], 404);
            }


            $itemsStmt = $pdo->prepare(
                'SELECT
                    order_items.id,
                    order_items.medicine_id,
                    order_items.quantity,
                    order_items.unit_price,
                    order_items.prescription_id,
                    medicines.name AS medicine_name,
                    prescriptions.prescription_number

                 FROM order_items

                 LEFT JOIN medicines
                    ON order_items.medicine_id =
                       medicines.id

                 LEFT JOIN prescriptions
                    ON order_items.prescription_id =
                       prescriptions.id

                 WHERE order_items.order_id = ?

                 ORDER BY order_items.id ASC'
            );


            $itemsStmt->execute([
                $id
            ]);


            $items =
                $itemsStmt->fetchAll();


            jsonResponse([
                'success' => true,
                'data' => $order,
                'items' => $items
            ]);


        } catch (PDOException $e) {

            jsonResponse([
                'success' => false,
                'message' => 'Neizdevās ielādēt pasūtījumu.'
            ], 500);
        }
    }


    /* =====================================================
       VISI PASŪTĪJUMI
       ===================================================== */

    try {

        $stmt = $pdo->query(
            "SELECT
                orders.id,

                COALESCE(
                    NULLIF(
                        orders.delivery_name,
                        ''
                    ),
                    orders.customer_name
                ) AS customer_name,

                orders.customer_email,
                orders.delivery_city,
                orders.status,
                orders.payment_status,
                orders.total,
                orders.created_at,

                GROUP_CONCAT(
                    CONCAT(
                        COALESCE(
                            medicines.name,
                            'Zāles'
                        ),
                        ' × ',
                        order_items.quantity
                    )
                    ORDER BY medicines.name
                    SEPARATOR ', '
                ) AS medicines

             FROM orders

             LEFT JOIN order_items
                ON orders.id =
                   order_items.order_id

             LEFT JOIN medicines
                ON order_items.medicine_id =
                   medicines.id

             WHERE orders.deleted_at IS NULL

             GROUP BY
                orders.id,
                orders.delivery_name,
                orders.customer_name,
                orders.customer_email,
                orders.delivery_city,
                orders.status,
                orders.payment_status,
                orders.total,
                orders.created_at

             ORDER BY orders.created_at DESC"
        );


        $orders =
            $stmt->fetchAll();


        jsonResponse([
            'success' => true,
            'data' => $orders
        ]);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās ielādēt pasūtījumus.'
        ], 500);
    }
}


/* =========================================================
   PUT - STATUSA MAIŅA
   ========================================================= */

if ($method === 'PUT') {

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


    $id =
        (int) (
            $data['id']
            ?? 0
        );


    $status =
        trim(
            $data['status']
            ?? ''
        );


    $allowedStatuses = [
        'Jauns',
        'Apstrādē',
        'Gatavs',
        'Izsniegts',
        'Atcelts'
    ];


    if (
        $id <= 0 ||
        !in_array(
            $status,
            $allowedStatuses,
            true
        )
    ) {

        jsonResponse([
            'success' => false,
            'message' => 'Nederīgi pasūtījuma dati.'
        ], 422);
    }


    try {

        /*
         * Vispirms atrodam pasūtījumu
         * un saglabājam veco statusu.
         */

        $checkStmt = $pdo->prepare(
            'SELECT
                id,
                status,
                customer_name,
                delivery_name

             FROM orders

             WHERE id = ?
             AND deleted_at IS NULL

             LIMIT 1'
        );


        $checkStmt->execute([
            $id
        ]);


        $order =
            $checkStmt->fetch();


        if (!$order) {

            jsonResponse([
                'success' => false,
                'message' => 'Pasūtījums nav atrasts.'
            ], 404);
        }


        $oldStatus =
            $order['status'];


        /*
         * Mainām statusu.
         */

        $stmt = $pdo->prepare(
            'UPDATE orders

             SET status = ?

             WHERE id = ?
             AND deleted_at IS NULL'
        );


        $stmt->execute([
            $status,
            $id
        ]);


        /*
         * AUDIT LOG
         */

        logAction(
            $pdo,
            'UPDATE',
            'order',
            $id,
            'Mainīts pasūtījuma statuss: ' .
            $oldStatus .
            ' → ' .
            $status
        );


        jsonResponse([
            'success' => true,
            'message' => 'Pasūtījuma statuss veiksmīgi mainīts.'
        ]);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās rediģēt pasūtījumu.'
        ], 500);
    }
}


/* =========================================================
   DELETE - SOFT DELETE
   ========================================================= */

if ($method === 'DELETE') {

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


    $id =
        (int) (
            $data['id']
            ?? 0
        );


    if ($id <= 0) {

        jsonResponse([
            'success' => false,
            'message' => 'Nederīgs pasūtījuma ID.'
        ], 422);
    }


    try {

        /*
         * Pārbaudām vai pasūtījums eksistē.
         */

        $checkStmt = $pdo->prepare(
            'SELECT
                id,
                customer_name,
                delivery_name,
                total,
                status

             FROM orders

             WHERE id = ?
             AND deleted_at IS NULL

             LIMIT 1'
        );


        $checkStmt->execute([
            $id
        ]);


        $order =
            $checkStmt->fetch();


        if (!$order) {

            jsonResponse([
                'success' => false,
                'message' => 'Pasūtījums nav atrasts.'
            ], 404);
        }


        /*
         * SOFT DELETE
         */

        $stmt = $pdo->prepare(
            'UPDATE orders

             SET deleted_at = NOW()

             WHERE id = ?
             AND deleted_at IS NULL'
        );


        $stmt->execute([
            $id
        ]);


        if ($stmt->rowCount() === 0) {

            jsonResponse([
                'success' => false,
                'message' => 'Pasūtījums nav atrasts.'
            ], 404);
        }


        /*
         * Klienta vārds audit log.
         */

        $customerName =
            !empty($order['delivery_name'])
                ? $order['delivery_name']
                : (
                    $order['customer_name']
                    ?? 'Klients'
                );


        /*
         * AUDIT LOG
         */

        logAction(
            $pdo,
            'SOFT_DELETE',
            'order',
            $id,
            'Dzēsts pasūtījums #' .
            $id .
            ', klients: ' .
            $customerName .
            ', summa: ' .
            $order['total'] .
            ' EUR'
        );


        jsonResponse([
            'success' => true,
            'message' => 'Pasūtījums veiksmīgi dzēsts.'
        ]);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās dzēst pasūtījumu.'
        ], 500);
    }
}


/* =========================================================
   NEATĻAUTA METODE
   ========================================================= */

jsonResponse([
    'success' => false,
    'message' => 'Metode nav atļauta.'
], 405);