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


/* =========================================================
   GET - RECEPŠU IELĀDE
   ========================================================= */

if ($method === 'GET') {

    $id = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );


    /*
        Ja URL ir, piemēram:
        prescriptions.php?id=16

        tad ielādējam tikai vienu recepti rediģēšanai.
    */

    if ($id) {

        try {

            $stmt = $pdo->prepare(
                "SELECT
                    prescriptions.id,
                    prescriptions.prescription_number,
                    prescriptions.patient_name,
                    prescriptions.doctor_name,
                    prescriptions.issued_at,
                    prescriptions.valid_until,
                    prescriptions.status,
                    prescription_items.medicine_id,
                    prescription_items.quantity

                FROM prescriptions

                LEFT JOIN prescription_items
                    ON prescriptions.id =
                       prescription_items.prescription_id

                WHERE prescriptions.id = ?

                LIMIT 1"
            );


            $stmt->execute([$id]);


            $prescription =
                $stmt->fetch();


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
                'message' =>
                    'Neizdevās ielādēt recepti.'
            ], 500);
        }
    }


    /*
        Ja id nav norādīts,
        ielādējam visu recepšu sarakstu.
    */

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


        $prescriptions =
            $stmt->fetchAll();


        $medicineStmt = $pdo->query(
            "SELECT
                id,
                name,
                strength

            FROM medicines

            ORDER BY name ASC"
        );


        $medicines =
            $medicineStmt->fetchAll();


        jsonResponse([
            'success' => true,
            'data' => $prescriptions,
            'medicines' => $medicines
        ]);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Neizdevās ielādēt receptes.'
        ], 500);
    }
}


/* =========================================================
   POST - JAUNAS RECEPTES PIEVIENOŠANA
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
            'message' =>
                'Aizpildiet visus obligātos laukus.'
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


    if (!in_array(
        $status,
        $allowedStatuses,
        true
    )) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Nederīgs receptes statuss.'
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
            'message' =>
                'Izvēlētās zāles nav atrastas.'
        ], 422);
    }


    try {

        $pdo->beginTransaction();


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



/* =========================================================
   PUT - RECEPTES REDIĢĒŠANA
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
        (int) ($data['id'] ?? 0);

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


    /* Obligāto lauku validācija */

    if (
        $id <= 0 ||
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
            'message' =>
                'Aizpildiet visus obligātos laukus.'
        ], 422);
    }


    /* Datumu validācija */

    if ($validUntil < $issuedAt) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Derīguma termiņš nevar būt agrāks par izrakstīšanas datumu.'
        ], 422);
    }


    /* Statusa validācija */

    $allowedStatuses = [
        'Aktīva',
        'Izmantota',
        'Atcelta',
        'Beidzies termiņš'
    ];


    if (!in_array(
        $status,
        $allowedStatuses,
        true
    )) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Nederīgs receptes statuss.'
        ], 422);
    }


    /* Pārbaudām, vai recepte eksistē */

    $checkPrescription = $pdo->prepare(
        'SELECT id
         FROM prescriptions
         WHERE id = ?
         LIMIT 1'
    );


    $checkPrescription->execute([
        $id
    ]);


    if (!$checkPrescription->fetch()) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Recepte nav atrasta.'
        ], 404);
    }


    /*
        Pārbaudām receptes numura unikalitāti.

        AND id != ? nozīmē:
        pašreizējā recepte drīkst saglabāt savu esošo numuru.
    */

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
            'message' =>
                'Recepte ar šādu numuru jau eksistē.'
        ], 409);
    }


    /* Pārbaudām izvēlētās zāles */

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
            'message' =>
                'Izvēlētās zāles nav atrastas.'
        ], 422);
    }


    try {

        $pdo->beginTransaction();


        /* Rediģējam receptes pamatinformāciju */

        $stmt = $pdo->prepare(
            'UPDATE prescriptions
             SET
                prescription_number = ?,
                patient_name = ?,
                doctor_name = ?,
                issued_at = ?,
                valid_until = ?,
                status = ?
             WHERE id = ?'
        );


        $stmt->execute([
            $prescriptionNumber,
            $patientName,
            $doctorName,
            $issuedAt,
            $validUntil,
            $status,
            $id
        ]);


        /*
            Šajā projekta versijā vienai receptei
            izmantojam vienu zāļu ierakstu.

            Dzēšam iepriekšējo sasaisti un izveidojam
            jaunu ar izvēlētajām zālēm.
        */

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
            'message' =>
                'Recepte veiksmīgi rediģēta.'
        ]);


    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }


        jsonResponse([
            'success' => false,
            'message' =>
                'Neizdevās rediģēt recepti.'
        ], 500);
    }
}

/* =========================================================
   DELETE - RECEPTES DZĒŠANA
   ========================================================= */

if ($method === 'DELETE') {

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );


    if (!is_array($data)) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Nederīgi nosūtītie dati.'
        ], 400);
    }


    $id =
        (int) ($data['id'] ?? 0);


    if ($id <= 0) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Nederīgs receptes ID.'
        ], 422);
    }


    $checkPrescription = $pdo->prepare(
        'SELECT id
         FROM prescriptions
         WHERE id = ?
         LIMIT 1'
    );


    $checkPrescription->execute([
        $id
    ]);


    if (!$checkPrescription->fetch()) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Recepte nav atrasta.'
        ], 404);
    }


    try {

        $pdo->beginTransaction();


        /*
            Vispirms dzēšam receptes zāļu ierakstus,
            jo tie ir saistīti ar recepti.
        */

        $itemStmt = $pdo->prepare(
            'DELETE FROM prescription_items
             WHERE prescription_id = ?'
        );


        $itemStmt->execute([
            $id
        ]);


        /*
            Pēc tam dzēšam pašu recepti.
        */

        $stmt = $pdo->prepare(
            'DELETE FROM prescriptions
             WHERE id = ?'
        );


        $stmt->execute([
            $id
        ]);


        $pdo->commit();


        jsonResponse([
            'success' => true,
            'message' =>
                'Recepte veiksmīgi dzēsta.'
        ]);


    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }


        jsonResponse([
            'success' => false,
            'message' =>
                'Neizdevās dzēst recepti.'
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