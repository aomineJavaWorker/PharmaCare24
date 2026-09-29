<?php

session_start();

require_once __DIR__ . '/../config/database.php';


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


/* =========================
   GET - RECEPŠU IELĀDE
   ========================= */

if ($method === 'GET') {

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
                ON prescriptions.id =
                   prescription_items.prescription_id

            LEFT JOIN medicines
                ON prescription_items.medicine_id =
                   medicines.id

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


        $medicineStmt = $pdo->query(
            "SELECT
                id,
                name,
                strength
            FROM medicines
            ORDER BY name ASC"
        );


        $medicines = $medicineStmt->fetchAll();


        jsonResponse([
            'success' => true,
            'data' => $prescriptions,
            'medicines' => $medicines
        ]);

    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās ielādēt receptes.'
        ], 500);
    }
}


/* =========================
   POST - JAUNA RECEPTE
   ========================= */

if ($method === 'POST') {

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );


    $prescriptionNumber =
        trim($data['prescription_number'] ?? '');

    $patientName =
        trim($data['patient_name'] ?? '');

    $doctorName =
        trim($data['doctor_name'] ?? '');

    $issuedAt =
        trim($data['issued_at'] ?? '');

    $validUntil =
        trim($data['valid_until'] ?? '');

    $status =
        trim($data['status'] ?? '');

    $medicineId =
        (int) ($data['medicine_id'] ?? 0);

    $quantity =
        (int) ($data['quantity'] ?? 0);


    if (
        $prescriptionNumber === '' ||
        $patientName === '' ||
        $doctorName === '' ||
        $issuedAt === '' ||
        $validUntil === '' ||
        $status === '' ||
        $medicineId <= 0 ||
        $quantity <= 0
    ) {

        jsonResponse([
            'success' => false,
            'message' => 'Aizpildiet visus obligātos laukus.'
        ], 422);
    }


    if ($validUntil < $issuedAt) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Derīguma termiņš nevar būt agrāks par izrakstīšanas datumu.'
        ], 422);
    }


    $allowedStatuses = [
        'Aktīva',
        'Izmantota',
        'Atcelta',
        'Beidzies termiņš'
    ];


    if (!in_array($status, $allowedStatuses, true)) {

        jsonResponse([
            'success' => false,
            'message' => 'Nederīgs receptes statuss.'
        ], 422);
    }


    /* Pārbaudām receptes numuru */

    $checkPrescription = $pdo->prepare(
        'SELECT id
         FROM prescriptions
         WHERE prescription_number = ?
         LIMIT 1'
    );

    $checkPrescription->execute([
        $prescriptionNumber
    ]);


    if ($checkPrescription->fetch()) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Recepte ar šādu numuru jau eksistē.'
        ], 409);
    }


    /* Pārbaudām zāles */

    $checkMedicine = $pdo->prepare(
        'SELECT id
         FROM medicines
         WHERE id = ?
         LIMIT 1'
    );

    $checkMedicine->execute([
        $medicineId
    ]);


    if (!$checkMedicine->fetch()) {

        jsonResponse([
            'success' => false,
            'message' => 'Izvēlētās zāles nav atrastas.'
        ], 422);
    }


    try {

        $pdo->beginTransaction();


        /* Izveidojam recepti */

        $stmt = $pdo->prepare(
            'INSERT INTO prescriptions
            (
                prescription_number,
                patient_name,
                doctor_name,
                issued_at,
                valid_until,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?)'
        );


        $stmt->execute([
            $prescriptionNumber,
            $patientName,
            $doctorName,
            $issuedAt,
            $validUntil,
            $status
        ]);


        $prescriptionId =
            (int) $pdo->lastInsertId();


        /* Pievienojam zāles receptei */

        $itemStmt = $pdo->prepare(
            'INSERT INTO prescription_items
            (
                prescription_id,
                medicine_id,
                quantity
            )
            VALUES (?, ?, ?)'
        );


        $itemStmt->execute([
            $prescriptionId,
            $medicineId,
            $quantity
        ]);


        $pdo->commit();


        jsonResponse([
            'success' => true,
            'message' =>
                'Recepte veiksmīgi pievienota.'
        ], 201);


    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }


        jsonResponse([
            'success' => false,
            'message' =>
                'Neizdevās saglabāt recepti.'
        ], 500);
    }
}


jsonResponse([
    'success' => false,
    'message' => 'Metode nav atļauta.'
], 405);