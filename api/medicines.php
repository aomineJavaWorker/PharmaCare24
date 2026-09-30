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


    /* =========================
       VIENAS ZĀLES
       ========================= */

    if ($id) {

        try {

            $stmt = $pdo->prepare(
                'SELECT
                    id,
                    category_id,
                    name,
                    active_substance,
                    manufacturer,
                    dosage_form,
                    strength,
                    price,
                    stock_quantity,
                    prescription_required,
                    api_search_name
                 FROM medicines
                 WHERE id = ?
                 AND deleted_at IS NULL
                 LIMIT 1'
            );


            $stmt->execute([
                $id
            ]);


            $medicine = $stmt->fetch();


            if (!$medicine) {

                jsonResponse([
                    'success' => false,
                    'message' => 'Zāles nav atrastas.'
                ], 404);
            }


            jsonResponse([
                'success' => true,
                'data' => $medicine
            ]);


        } catch (PDOException $e) {

            jsonResponse([
                'success' => false,
                'message' => 'Neizdevās ielādēt zāles.'
            ], 500);
        }
    }


    /* =========================
       VISAS ZĀLES
       ========================= */

    try {

        $stmt = $pdo->query(
            'SELECT
                medicines.id,
                medicines.category_id,
                medicines.name,
                medicines.active_substance,
                medicines.manufacturer,
                medicines.dosage_form,
                medicines.strength,
                medicines.price,
                medicines.stock_quantity,
                medicines.prescription_required,
                medicines.api_search_name,
                categories.name AS category_name

             FROM medicines

             LEFT JOIN categories
                ON medicines.category_id =
                   categories.id

             WHERE medicines.deleted_at IS NULL

             ORDER BY medicines.name ASC'
        );


        $medicines = $stmt->fetchAll();


        $categoryStmt = $pdo->query(
            'SELECT
                id,
                name
             FROM categories
             ORDER BY name ASC'
        );


        $categories = $categoryStmt->fetchAll();


        jsonResponse([
            'success' => true,
            'data' => $medicines,
            'categories' => $categories
        ]);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās ielādēt zāles.'
        ], 500);
    }
}


/* =========================================================
   POST - CREATE
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


    $categoryId =
        (int) ($data['category_id'] ?? 0);

    $name =
        trim($data['name'] ?? '');

    $activeSubstance =
        trim($data['active_substance'] ?? '');

    $manufacturer =
        trim($data['manufacturer'] ?? '');

    $dosageForm =
        trim($data['dosage_form'] ?? '');

    $strength =
        trim($data['strength'] ?? '');

    $price =
        (float) ($data['price'] ?? 0);

    $stockQuantity =
        (int) ($data['stock_quantity'] ?? 0);

    $prescriptionRequired =
        !empty($data['prescription_required'])
            ? 1
            : 0;

    $apiSearchName =
        trim($data['api_search_name'] ?? '');


    if (
        $categoryId <= 0 ||
        $name === '' ||
        $activeSubstance === '' ||
        $manufacturer === '' ||
        $dosageForm === '' ||
        $price <= 0 ||
        $stockQuantity < 0
    ) {

        jsonResponse([
            'success' => false,
            'message' => 'Aizpildiet visus obligātos laukus.'
        ], 422);
    }


    /* KATEGORIJAS PĀRBAUDE */

    $categoryStmt = $pdo->prepare(
        'SELECT id
         FROM categories
         WHERE id = ?
         LIMIT 1'
    );


    $categoryStmt->execute([
        $categoryId
    ]);


    if (!$categoryStmt->fetch()) {

        jsonResponse([
            'success' => false,
            'message' => 'Izvēlētā kategorija nav atrasta.'
        ], 422);
    }


    try {

        $stmt = $pdo->prepare(
            'INSERT INTO medicines
                (
                    category_id,
                    name,
                    active_substance,
                    manufacturer,
                    dosage_form,
                    strength,
                    price,
                    stock_quantity,
                    prescription_required,
                    api_search_name
                )

             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );


        $stmt->execute([
            $categoryId,
            $name,
            $activeSubstance,
            $manufacturer,
            $dosageForm,
            $strength !== ''
                ? $strength
                : null,
            $price,
            $stockQuantity,
            $prescriptionRequired,
            $apiSearchName !== ''
                ? $apiSearchName
                : null
        ]);


        $medicineId =
            (int) $pdo->lastInsertId();


        /* =========================
           AUDIT LOG - CREATE
           ========================= */

        logAction(
            $pdo,
            'CREATE',
            'medicine',
            $medicineId,
            'Pievienotas zāles: ' . $name
        );


        jsonResponse([
            'success' => true,
            'message' => 'Zāles veiksmīgi pievienotas.'
        ], 201);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās saglabāt zāles.'
        ], 500);
    }
}


/* =========================================================
   PUT - UPDATE
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

    $categoryId =
        (int) ($data['category_id'] ?? 0);

    $name =
        trim($data['name'] ?? '');

    $activeSubstance =
        trim($data['active_substance'] ?? '');

    $manufacturer =
        trim($data['manufacturer'] ?? '');

    $dosageForm =
        trim($data['dosage_form'] ?? '');

    $strength =
        trim($data['strength'] ?? '');

    $price =
        (float) ($data['price'] ?? 0);

    $stockQuantity =
        (int) ($data['stock_quantity'] ?? 0);

    $prescriptionRequired =
        !empty($data['prescription_required'])
            ? 1
            : 0;

    $apiSearchName =
        trim($data['api_search_name'] ?? '');


    if (
        $id <= 0 ||
        $categoryId <= 0 ||
        $name === '' ||
        $activeSubstance === '' ||
        $manufacturer === '' ||
        $dosageForm === '' ||
        $price <= 0 ||
        $stockQuantity < 0
    ) {

        jsonResponse([
            'success' => false,
            'message' => 'Aizpildiet visus obligātos laukus.'
        ], 422);
    }


    /* ZĀĻU PĀRBAUDE */

    $medicineStmt = $pdo->prepare(
        'SELECT id
         FROM medicines
         WHERE id = ?
         AND deleted_at IS NULL
         LIMIT 1'
    );


    $medicineStmt->execute([
        $id
    ]);


    if (!$medicineStmt->fetch()) {

        jsonResponse([
            'success' => false,
            'message' => 'Zāles nav atrastas.'
        ], 404);
    }


    /* KATEGORIJAS PĀRBAUDE */

    $categoryStmt = $pdo->prepare(
        'SELECT id
         FROM categories
         WHERE id = ?
         LIMIT 1'
    );


    $categoryStmt->execute([
        $categoryId
    ]);


    if (!$categoryStmt->fetch()) {

        jsonResponse([
            'success' => false,
            'message' => 'Izvēlētā kategorija nav atrasta.'
        ], 422);
    }


    try {

        $stmt = $pdo->prepare(
            'UPDATE medicines

             SET
                category_id = ?,
                name = ?,
                active_substance = ?,
                manufacturer = ?,
                dosage_form = ?,
                strength = ?,
                price = ?,
                stock_quantity = ?,
                prescription_required = ?,
                api_search_name = ?

             WHERE id = ?
             AND deleted_at IS NULL'
        );


        $stmt->execute([
            $categoryId,
            $name,
            $activeSubstance,
            $manufacturer,
            $dosageForm,
            $strength !== ''
                ? $strength
                : null,
            $price,
            $stockQuantity,
            $prescriptionRequired,
            $apiSearchName !== ''
                ? $apiSearchName
                : null,
            $id
        ]);


        /* =========================
           AUDIT LOG - UPDATE
           ========================= */

        logAction(
            $pdo,
            'UPDATE',
            'medicine',
            $id,
            'Rediģētas zāles: ' . $name
        );


        jsonResponse([
            'success' => true,
            'message' => 'Zāles veiksmīgi rediģētas.'
        ]);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās rediģēt zāles.'
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
        (int) ($data['id'] ?? 0);


    if ($id <= 0) {

        jsonResponse([
            'success' => false,
            'message' => 'Nederīgs zāļu ID.'
        ], 422);
    }


    try {

        $stmt = $pdo->prepare(
            'UPDATE medicines

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
                'message' => 'Zāles nav atrastas.'
            ], 404);
        }


        /* =========================
           AUDIT LOG - SOFT DELETE
           ========================= */

        logAction(
            $pdo,
            'SOFT_DELETE',
            'medicine',
            $id,
            'Zāles dzēstas'
        );


        jsonResponse([
            'success' => true,
            'message' => 'Zāles veiksmīgi dzēstas.'
        ]);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās dzēst zāles.'
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