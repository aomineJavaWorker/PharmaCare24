<?php

session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    jsonResponse(['success' => false, 'message' => 'Nav autorizācijas.'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];


/* GET */

if ($method === 'GET') {
    try {
        $stmt = $pdo->query(
            "SELECT medicines.id, medicines.category_id, medicines.name,
                    medicines.active_substance, medicines.manufacturer,
                    medicines.dosage_form, medicines.strength,
                    medicines.price, medicines.stock_quantity,
                    medicines.prescription_required, medicines.api_search_name,
                    categories.name AS category_name
             FROM medicines
             LEFT JOIN categories ON medicines.category_id = categories.id
             ORDER BY medicines.name ASC"
        );

        $medicines = $stmt->fetchAll();

        $categoryStmt = $pdo->query(
            "SELECT id, name FROM categories ORDER BY name ASC"
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


/* POST */

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!is_array($data)) {
        jsonResponse(['success' => false, 'message' => 'Nederīgi nosūtītie dati.'], 400);
    }

    $categoryId = (int) ($data['category_id'] ?? 0);
    $name = trim($data['name'] ?? '');
    $activeSubstance = trim($data['active_substance'] ?? '');
    $manufacturer = trim($data['manufacturer'] ?? '');
    $dosageForm = trim($data['dosage_form'] ?? '');
    $strength = trim($data['strength'] ?? '');
    $price = (float) ($data['price'] ?? 0);
    $stockQuantity = (int) ($data['stock_quantity'] ?? 0);
    $prescriptionRequired = !empty($data['prescription_required']) ? 1 : 0;
    $apiSearchName = trim($data['api_search_name'] ?? '');

    if (
        $categoryId <= 0 || $name === '' || $activeSubstance === '' ||
        $manufacturer === '' || $dosageForm === '' ||
        $price <= 0 || $stockQuantity < 0
    ) {
        jsonResponse([
            'success' => false,
            'message' => 'Aizpildiet visus obligātos laukus.'
        ], 422);
    }

    $categoryStmt = $pdo->prepare(
        'SELECT id FROM categories WHERE id = ? LIMIT 1'
    );
    $categoryStmt->execute([$categoryId]);

    if (!$categoryStmt->fetch()) {
        jsonResponse([
            'success' => false,
            'message' => 'Izvēlētā kategorija nav atrasta.'
        ], 422);
    }

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO medicines
                (category_id, name, active_substance, manufacturer,
                 dosage_form, strength, price, stock_quantity,
                 prescription_required, api_search_name)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $categoryId, $name, $activeSubstance, $manufacturer,
            $dosageForm, $strength !== '' ? $strength : null,
            $price, $stockQuantity, $prescriptionRequired,
            $apiSearchName !== '' ? $apiSearchName : null
        ]);

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

jsonResponse(['success' => false, 'message' => 'Metode nav atļauta.'], 405);