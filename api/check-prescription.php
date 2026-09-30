<?php

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


$patientName = trim(
    $data['patient_name'] ?? ''
);

$personalCode = trim(
    $data['personal_code'] ?? ''
);

$items = $data['items'] ?? [];


if (
    $patientName === '' ||
    $personalCode === ''
) {

    jsonResponse([
        'success' => false,
        'message' => 'Ievadiet vārdu, uzvārdu un personas kodu.'
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
        'message' => 'Personas kodam jābūt formātā 999999-00001.'
    ], 422);
}


if (
    !is_array($items) ||
    count($items) === 0
) {

    jsonResponse([
        'success' => false,
        'message' => 'Grozs ir tukšs.'
    ], 422);
}


try {

    $medicineStmt = $pdo->prepare(
        'SELECT
            id,
            name,
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


    $requiresPrescription = false;

    $verifiedPrescriptions = [];


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


        if (
            (int) $medicine[
                'prescription_required'
            ] !== 1
        ) {

            continue;
        }


        $requiresPrescription = true;


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


        $verifiedPrescriptions[] = [
            'medicine_id' =>
                $medicineId,

            'prescription_id' =>
                (int) $prescription['id'],

            'prescription_number' =>
                $prescription[
                    'prescription_number'
                ]
        ];
    }


    jsonResponse([
        'success' => true,

        'requires_prescription' =>
            $requiresPrescription,

        'verified' => true,

        'prescriptions' =>
            $verifiedPrescriptions,

        'message' =>
            $requiresPrescription
                ? 'Recepte ir derīga.'
                : 'Recepte nav nepieciešama.'
    ]);


} catch (PDOException $e) {

    jsonResponse([
        'success' => false,
        'message' => 'Neizdevās pārbaudīt recepti.'
    ], 500);
}