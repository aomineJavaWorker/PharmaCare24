<?php

session_start();

require_once __DIR__ . '/../config/database.php';


header('Content-Type: application/json; charset=utf-8');


if (!isset($_SESSION['user_id'])) {

    http_response_code(401);

    echo json_encode([
        'success' => false,
        'message' => 'Nav autorizācijas.'
    ]);

    exit;
}


try {

    $stmt = $pdo->query(
        "SELECT
            prescriptions.id,
            prescriptions.prescription_number,
            prescriptions.patient_name,
            prescriptions.doctor_name,
            prescriptions.issued_at,
            prescriptions.valid_until,
            prescriptions.status,

            GROUP_CONCAT(
                CONCAT(
                    medicines.name,
                    ' × ',
                    prescription_items.quantity
                )
                ORDER BY medicines.name
                SEPARATOR ', '
            ) AS medicines

        FROM prescriptions

        LEFT JOIN prescription_items
            ON prescriptions.id = prescription_items.prescription_id

        LEFT JOIN medicines
            ON prescription_items.medicine_id = medicines.id

        GROUP BY
            prescriptions.id,
            prescriptions.prescription_number,
            prescriptions.patient_name,
            prescriptions.doctor_name,
            prescriptions.issued_at,
            prescriptions.valid_until,
            prescriptions.status

        ORDER BY prescriptions.created_at DESC"
    );


    $prescriptions = $stmt->fetchAll();


    echo json_encode(
        [
            'success' => true,
            'data' => $prescriptions
        ],
        JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Neizdevās ielādēt receptes.'
    ]);
}