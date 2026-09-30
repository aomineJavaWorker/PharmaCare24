<?php

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/audit_log.php';


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


/*
 * SVARĪGI:
 * pārbaude notiek arī serverī.
 *
 * Nepietiek tikai paslēpt pogu HTML.
 */

if (
    ($_SESSION['role'] ?? '')
    !== 'admin'
) {

    jsonResponse([
        'success' => false,
        'message' => 'Nav pietiekamu tiesību.'
    ], 403);
}


$method =
    $_SERVER['REQUEST_METHOD'];


/* =========================
   GET
   ========================= */

if ($method === 'GET') {

    try {

        $stmt = $pdo->query(
            'SELECT
                id,
                username,
                role,
                created_at,
                updated_at
             FROM users
             ORDER BY created_at DESC'
        );


        jsonResponse([
            'success' => true,
            'data' => $stmt->fetchAll()
        ]);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās ielādēt lietotājus.'
        ], 500);
    }
}


/* =========================
   POST
   ========================= */

if ($method === 'POST') {

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );


    if (!is_array($data)) {

        jsonResponse([
            'success' => false,
            'message' => 'Nederīgi dati.'
        ], 400);
    }


    $username =
        trim(
            $data['username']
            ?? ''
        );


    $password =
        $data['password']
        ?? '';


    $passwordConfirm =
        $data['password_confirm']
        ?? '';


    if (
        strlen($username) < 3 ||
        strlen($username) > 50
    ) {

        jsonResponse([
            'success' => false,
            'message' => 'Lietotājvārdam jābūt no 3 līdz 50 simboliem.'
        ], 422);
    }


    if (strlen($password) < 8) {

        jsonResponse([
            'success' => false,
            'message' => 'Parolei jābūt vismaz 8 simbolus garai.'
        ], 422);
    }


    if ($password !== $passwordConfirm) {

        jsonResponse([
            'success' => false,
            'message' => 'Paroles nesakrīt.'
        ], 422);
    }


    $checkStmt = $pdo->prepare(
        'SELECT id
         FROM users
         WHERE username = ?
         LIMIT 1'
    );


    $checkStmt->execute([
        $username
    ]);


    if ($checkStmt->fetch()) {

        jsonResponse([
            'success' => false,
            'message' => 'Šāds lietotājvārds jau eksistē.'
        ], 409);
    }


    try {

        $passwordHash =
            password_hash(
                $password,
                PASSWORD_DEFAULT
            );


        $stmt = $pdo->prepare(
            "INSERT INTO users
                (
                    username,
                    password_hash,
                    role
                )
             VALUES (?, ?, 'moderator')"
        );


        $stmt->execute([
            $username,
            $passwordHash
        ]);


        $userId =
            (int) $pdo->lastInsertId();


        logAction(
            $pdo,
            'CREATE',
            'user',
            $userId,
            'Izveidots moderators: ' .
            $username
        );


        jsonResponse([
            'success' => true,
            'message' => 'Moderators veiksmīgi izveidots.'
        ], 201);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās izveidot moderatoru.'
        ], 500);
    }
}


jsonResponse([
    'success' => false,
    'message' => 'Metode nav atļauta.'
], 405);