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
    jsonResponse([
        'success' => false,
        'message' => 'Nav autorizācijas.'
    ], 401);
}


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse([
        'success' => false,
        'message' => 'Metode nav atļauta.'
    ], 405);
}


try {

    $stmt = $pdo->query(
        "SELECT medicines.id, medicines.name, medicines.active_substance,
                medicines.price, medicines.stock_quantity,
                medicines.prescription_required,
                categories.name AS category_name
         FROM medicines
         LEFT JOIN categories ON medicines.category_id = categories.id
         ORDER BY medicines.name ASC"
    );

    $medicines = $stmt->fetchAll();


    jsonResponse([
        'success' => true,
        'data' => $medicines
    ]);

} catch (PDOException $e) {

    jsonResponse([
        'success' => false,
        'message' => 'Neizdevās ielādēt zāles.'
    ], 500);
}