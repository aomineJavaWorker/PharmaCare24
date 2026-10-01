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


function jsonInput(): array
{
    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    return is_array($data)
        ? $data
        : [];
}


function validRole(string $role): bool
{
    return in_array(
        $role,
        [
            'admin',
            'moderator'
        ],
        true
    );
}


function adminCount(PDO $pdo): int
{
    return (int) $pdo
        ->query(
            "SELECT COUNT(*)
             FROM users
             WHERE role = 'admin'"
        )
        ->fetchColumn();
}


/* =========================================================
   AUTORIZĀCIJA
   ========================================================= */

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


$method =
    $_SERVER['REQUEST_METHOD'];


/* =========================================================
   GET
   ========================================================= */

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
             ORDER BY id ASC'
        );


        jsonResponse([
            'success' => true,

            'current_user_id' =>
                (int) $_SESSION['user_id'],

            'data' =>
                $stmt->fetchAll()
        ]);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Neizdevās ielādēt lietotājus.'
        ], 500);
    }
}


/* =========================================================
   POST — IZVEIDOT
   ========================================================= */

if ($method === 'POST') {

    $data =
        jsonInput();


    $username =
        trim(
            $data['username']
            ?? ''
        );


    $role =
        $data['role']
        ?? 'moderator';


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
            'message' =>
                'Lietotājvārdam jābūt no 3 līdz 50 simboliem.'
        ], 422);
    }


    if (!validRole($role)) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Nederīga lietotāja loma.'
        ], 422);
    }


    if (strlen($password) < 8) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Parolei jābūt vismaz 8 simbolus garai.'
        ], 422);
    }


    if (
        $password !==
        $passwordConfirm
    ) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Paroles nesakrīt.'
        ], 422);
    }


    $checkStmt =
        $pdo->prepare(
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
            'message' =>
                'Šāds lietotājvārds jau eksistē.'
        ], 409);
    }


    try {

        $passwordHash =
            password_hash(
                $password,
                PASSWORD_DEFAULT
            );


        $stmt =
            $pdo->prepare(
                'INSERT INTO users
                    (
                        username,
                        password_hash,
                        role
                    )
                 VALUES (?, ?, ?)'
            );


        $stmt->execute([
            $username,
            $passwordHash,
            $role
        ]);


        $userId =
            (int) $pdo->lastInsertId();


        logAction(
            $pdo,
            'CREATE',
            'user',
            $userId,
            sprintf(
                'Izveidots lietotājs: %s (%s)',
                $username,
                $role
            )
        );


        jsonResponse([
            'success' => true,
            'message' =>
                'Lietotājs veiksmīgi izveidots.'
        ], 201);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Neizdevās izveidot lietotāju.'
        ], 500);
    }
}


/* =========================================================
   PUT — REDIĢĒT
   ========================================================= */

if ($method === 'PUT') {

    $data =
        jsonInput();


    $id =
        filter_var(
            $data['id']
            ?? null,
            FILTER_VALIDATE_INT
        );


    if (!$id) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Nederīgs lietotāja ID.'
        ], 422);
    }


    $username =
        trim(
            $data['username']
            ?? ''
        );


    $role =
        $data['role']
        ?? 'moderator';


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
            'message' =>
                'Lietotājvārdam jābūt no 3 līdz 50 simboliem.'
        ], 422);
    }


    if (!validRole($role)) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Nederīga lietotāja loma.'
        ], 422);
    }


    $stmt =
        $pdo->prepare(
            'SELECT
                id,
                username,
                role
             FROM users
             WHERE id = ?
             LIMIT 1'
        );


    $stmt->execute([
        $id
    ]);


    $user =
        $stmt->fetch();


    if (!$user) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Lietotājs nav atrasts.'
        ], 404);
    }


    /*
     * Neļaujam pašam sev noņemt admin lomu.
     */

    if (
        (int) $id ===
            (int) $_SESSION['user_id'] &&
        $role !== 'admin'
    ) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Savam kontam administratora lomu noņemt nevar.'
        ], 422);
    }


    /*
     * Sistēmā vienmēr jāpaliek
     * vismaz vienam administratoram.
     */

    if (
        $user['role'] === 'admin' &&
        $role !== 'admin' &&
        adminCount($pdo) <= 1
    ) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Sistēmā jāpaliek vismaz vienam administratoram.'
        ], 422);
    }


    $checkStmt =
        $pdo->prepare(
            'SELECT id
             FROM users
             WHERE username = ?
               AND id <> ?
             LIMIT 1'
        );


    $checkStmt->execute([
        $username,
        $id
    ]);


    if ($checkStmt->fetch()) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Šāds lietotājvārds jau eksistē.'
        ], 409);
    }


    if ($password !== '') {

        if (strlen($password) < 8) {

            jsonResponse([
                'success' => false,
                'message' =>
                    'Parolei jābūt vismaz 8 simbolus garai.'
            ], 422);
        }


        if (
            $password !==
            $passwordConfirm
        ) {

            jsonResponse([
                'success' => false,
                'message' =>
                    'Paroles nesakrīt.'
            ], 422);
        }
    }


    try {

        if ($password !== '') {

            $passwordHash =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            $stmt =
                $pdo->prepare(
                    'UPDATE users
                     SET
                        username = ?,
                        role = ?,
                        password_hash = ?
                     WHERE id = ?'
                );


            $stmt->execute([
                $username,
                $role,
                $passwordHash,
                $id
            ]);


        } else {

            $stmt =
                $pdo->prepare(
                    'UPDATE users
                     SET
                        username = ?,
                        role = ?
                     WHERE id = ?'
                );


            $stmt->execute([
                $username,
                $role,
                $id
            ]);
        }


        /*
         * Ja administrators rediģē pats sevi,
         * atjaunojam vārdu arī sesijā.
         */

        if (
            (int) $id ===
            (int) $_SESSION['user_id']
        ) {

            $_SESSION['username'] =
                $username;
        }


        logAction(
            $pdo,
            'UPDATE',
            'user',
            (int) $id,
            sprintf(
                'Rediģēts lietotājs: %s (%s)',
                $username,
                $role
            )
        );


        jsonResponse([
            'success' => true,
            'message' =>
                'Lietotāja dati veiksmīgi atjaunināti.'
        ]);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Neizdevās atjaunināt lietotāju.'
        ], 500);
    }
}


/* =========================================================
   DELETE — DZĒST
   ========================================================= */

if ($method === 'DELETE') {

    $data =
        jsonInput();


    $id =
        filter_var(
            $data['id']
            ?? null,
            FILTER_VALIDATE_INT
        );


    if (!$id) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Nederīgs lietotāja ID.'
        ], 422);
    }


    if (
        (int) $id ===
        (int) $_SESSION['user_id']
    ) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Savu lietotāja kontu dzēst nevar.'
        ], 422);
    }


    $stmt =
        $pdo->prepare(
            'SELECT
                id,
                username,
                role
             FROM users
             WHERE id = ?
             LIMIT 1'
        );


    $stmt->execute([
        $id
    ]);


    $user =
        $stmt->fetch();


    if (!$user) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Lietotājs nav atrasts.'
        ], 404);
    }


    if (
        $user['role'] === 'admin' &&
        adminCount($pdo) <= 1
    ) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Pēdējo administratoru dzēst nevar.'
        ], 422);
    }


    try {

        $stmt =
            $pdo->prepare(
                'DELETE FROM users
                 WHERE id = ?'
            );


        $stmt->execute([
            $id
        ]);


        logAction(
            $pdo,
            'DELETE',
            'user',
            (int) $id,
            sprintf(
                'Dzēsts lietotājs: %s (%s)',
                $user['username'],
                $user['role']
            )
        );


        jsonResponse([
            'success' => true,
            'message' =>
                'Lietotājs veiksmīgi dzēsts.'
        ]);


    } catch (PDOException $e) {

        jsonResponse([
            'success' => false,
            'message' =>
                'Neizdevās dzēst lietotāju.'
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