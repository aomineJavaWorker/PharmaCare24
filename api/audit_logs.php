<?php

session_start();

require_once __DIR__ . '/../config/database.php';


header(
    'Content-Type: application/json; charset=utf-8'
);


function jsonResponse(
    array $data,
    int $status = 200
): void {

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


if (
    ($_SESSION['role'] ?? '')
    !== 'admin'
) {

    jsonResponse([
        'success' => false,
        'message' => 'Nav pietiekamu tiesību.'
    ], 403);
}


try {

    $stmt = $pdo->query(
        'SELECT
            id,
            username,
            role,
            action,
            entity_type,
            entity_id,
            details,
            ip_address,
            created_at
         FROM audit_logs
         ORDER BY created_at DESC
         LIMIT 200'
    );


    jsonResponse([
        'success' => true,
        'data' => $stmt->fetchAll()
    ]);


} catch (PDOException $e) {

    jsonResponse([
        'success' => false,
        'message' => 'Neizdevās ielādēt darbību žurnālu.'
    ], 500);
}