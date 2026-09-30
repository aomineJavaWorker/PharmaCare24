<?php

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/audit_log.php';

header('Content-Type: application/json; charset=utf-8');


function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
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

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);


    /* Vienas receptes ielāde */

    if ($id) {

        try {

            $stmt = $pdo->prepare(
                "SELECT
                    prescriptions.id,
                    prescriptions.prescription_number,
                    prescriptions.patient_name,
                    prescriptions.personal_code,
                    prescriptions.doctor_name,
                    prescriptions.issued_at,
                    prescriptions.valid_until,
                    prescriptions.status,
                    prescription_items.medicine_id,
                    prescription_items.quantity
                 FROM prescriptions
                 LEFT JOIN prescription_items
                    ON prescriptions.id = prescription_items.prescription_id
                 WHERE prescriptions.id = ?
                    AND prescriptions.deleted_at IS NULL
                 LIMIT 1"
            );


            $stmt->execute([$id]);

            $prescription = $stmt->fetch();


            if (!$prescription) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Recepte nav atrasta.'
                ], 404);
            }


            jsonResponse([
                'success' => true,
                'data' => $prescription
            ]);


        } catch (PDOException $e) {

            jsonResponse([
                'success' => false,
                'message' => 'Neizdevās ielādēt recepti.'
            ], 500);
        }
    }


    /* Visu recepšu ielāde */

    try {

        $stmt = $pdo->query(
            "SELECT
                prescriptions.id,
                prescriptions.prescription_number,
                prescriptions.patient_name,
                prescriptions.personal_code,
                prescriptions.doctor_name,
                prescriptions.issued_at,
                prescriptions.valid_until,
                prescriptions.status,
                GROUP_CONCAT(
                    CONCAT(medicines.name, ' × ', prescription_items.quantity)
                    ORDER BY medicines.name
                    SEPARATOR ', '
                ) AS medicines
             FROM prescriptions
             LEFT JOIN prescription_items
                ON prescriptions.id = prescription_items.prescription_id
             LEFT JOIN medicines
                ON prescription_items.medicine_id = medicines.id
             WHERE prescriptions.deleted_at IS NULL
             GROUP BY
                prescriptions.id,
                prescriptions.prescription_number,
                prescriptions.patient_name,
                prescriptions.personal_code,
                prescriptions.doctor_name,
                prescriptions.issued_at,
                prescriptions.valid_until,
                prescriptions.status
             ORDER BY prescriptions.created_at DESC"
        );


        $prescriptions = $stmt->fetchAll();


        $medicineStmt = $pdo->query(
            "SELECT id, name, strength
             FROM medicines
             WHERE deleted_at IS NULL
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


/* =========================================================
   POST
   ========================================================= */

if ($method === 'POST') {

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


    $prescriptionNumber = trim($data['prescription_number'] ?? '');
    $patientName = trim($data['patient_name'] ?? '');
    $personalCode = trim($data['personal_code'] ?? '');
    $doctorName = trim($data['doctor_name'] ?? '');
    $issuedAt = trim($data['issued_at'] ?? '');
    $validUntil = trim($data['valid_until'] ?? '');
    $status = trim($data['status'] ?? '');
    $medicineId = (int) ($data['medicine_id'] ?? 0);
    $quantity = (int) ($data['quantity'] ?? 0);


    if (
        $prescriptionNumber === '' ||
        $patientName === '' ||
        $personalCode === '' ||
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


    if (!preg_match('/^\d{6}-\d{5}$/', $personalCode)) {

        jsonResponse([
            'success' => false,
            'message' => 'Personas kodam jābūt formātā 999999-00001.'
        ], 422);
    }


    if ($validUntil < $issuedAt) {

        jsonResponse([
            'success' => false,
            'message' => 'Derīguma termiņš nevar būt agrāks par izrakstīšanas datumu.'
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
            'message' => 'Recepte ar šādu numuru jau eksistē.'
        ], 409);
    }


    $checkMedicine = $pdo->prepare(
        'SELECT id
         FROM medicines
         WHERE id = ?
         AND deleted_at IS NULL
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


        $stmt = $pdo->prepare(
            'INSERT INTO prescriptions
                (
                    prescription_number,
                    patient_name,
                    personal_code,
                    doctor_name,
                    issued_at,
                    valid_until,
                    status
                )
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );


        $stmt->execute([
            $prescriptionNumber,
            $patientName,
            $personalCode,
            $doctorName,
            $issuedAt,
            $validUntil,
            $status
        ]);


        $prescriptionId =
            (int) $pdo->lastInsertId();


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
            'message' => 'Recepte veiksmīgi pievienota.'
        ], 201);


    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }


        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās saglabāt recepti.'
        ], 500);
    }
}


/* =========================================================
   PUT
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


    $id = (int) ($data['id'] ?? 0);
    $prescriptionNumber = trim($data['prescription_number'] ?? '');
    $patientName = trim($data['patient_name'] ?? '');
    $personalCode = trim($data['personal_code'] ?? '');
    $doctorName = trim($data['doctor_name'] ?? '');
    $issuedAt = trim($data['issued_at'] ?? '');
    $validUntil = trim($data['valid_until'] ?? '');
    $status = trim($data['status'] ?? '');
    $medicineId = (int) ($data['medicine_id'] ?? 0);
    $quantity = (int) ($data['quantity'] ?? 0);


    if (
        $id <= 0 ||
        $prescriptionNumber === '' ||
        $patientName === '' ||
        $personalCode === '' ||
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


    if (!preg_match('/^\d{6}-\d{5}$/', $personalCode)) {

        jsonResponse([
            'success' => false,
            'message' => 'Personas kodam jābūt formātā 999999-00001.'
        ], 422);
    }


    if ($validUntil < $issuedAt) {

        jsonResponse([
            'success' => false,
            'message' => 'Derīguma termiņš nevar būt agrāks par izrakstīšanas datumu.'
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


    $checkPrescription = $pdo->prepare(
        'SELECT id
         FROM prescriptions
         WHERE id = ?
         AND deleted_at IS NULL
         LIMIT 1'
    );


    $checkPrescription->execute([
        $id
    ]);


    if (!$checkPrescription->fetch()) {

        jsonResponse([
            'success' => false,
            'message' => 'Recepte nav atrasta.'
        ], 404);
    }


    $checkNumber = $pdo->prepare(
        'SELECT id
         FROM prescriptions
         WHERE prescription_number = ?
         AND id != ?
         LIMIT 1'
    );


    $checkNumber->execute([
        $prescriptionNumber,
        $id
    ]);


    if ($checkNumber->fetch()) {

        jsonResponse([
            'success' => false,
            'message' => 'Recepte ar šādu numuru jau eksistē.'
        ], 409);
    }


    $checkMedicine = $pdo->prepare(
        'SELECT id
         FROM medicines
         WHERE id = ?
         AND deleted_at IS NULL
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


        $stmt = $pdo->prepare(
            'UPDATE prescriptions
             SET
                prescription_number = ?,
                patient_name = ?,
                personal_code = ?,
                doctor_name = ?,
                issued_at = ?,
                valid_until = ?,
                status = ?
             WHERE id = ?
             AND deleted_at IS NULL'
        );


        $stmt->execute([
            $prescriptionNumber,
            $patientName,
            $personalCode,
            $doctorName,
            $issuedAt,
            $validUntil,
            $status,
            $id
        ]);


        $deleteItemStmt = $pdo->prepare(
            'DELETE FROM prescription_items
             WHERE prescription_id = ?'
        );


        $deleteItemStmt->execute([
            $id
        ]);


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
            $id,
            $medicineId,
            $quantity
        ]);


        $pdo->commit();


        jsonResponse([
            'success' => true,
            'message' => 'Recepte veiksmīgi rediģēta.'
        ]);


    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }


        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās rediģēt recepti.'
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


    $id = (int) ($data['id'] ?? 0);


    if ($id <= 0) {

        jsonResponse([
            'success' => false,
            'message' => 'Nederīgs receptes ID.'
        ], 422);
    }


    $checkPrescription = $pdo->prepare(
        'SELECT id
         FROM prescriptions
         WHERE id = ?
         AND deleted_at IS NULL
         LIMIT 1'
    );


    $checkPrescription->execute([
        $id
    ]);


    if (!$checkPrescription->fetch()) {

        jsonResponse([
            'success' => false,
            'message' => 'Recepte nav atrasta.'
        ], 404);
    }


    try {

        $stmt = $pdo->prepare(
            'UPDATE prescriptions
             SET deleted_at = NOW()
             WHERE id = ?
             AND deleted_at IS NULL'
        );


        $stmt->execute([
            $id
        ]);


        jsonResponse([
            'success' => true,
            'message' => 'Recepte veiksmīgi dzēsta.'
        ]);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās dzēst recepti.'
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