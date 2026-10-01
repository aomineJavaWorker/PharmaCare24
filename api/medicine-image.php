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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse([
        'success' => false,
        'message' => 'Metode nav atļauta.'
    ], 405);
}

$medicineId = filter_input(INPUT_POST, 'medicine_id', FILTER_VALIDATE_INT);

if (!$medicineId) {
    jsonResponse([
        'success' => false,
        'message' => 'Nederīgs zāļu ID.'
    ], 422);
}

if (!isset($_FILES['image'])) {
    jsonResponse([
        'success' => false,
        'message' => 'Attēla fails nav saņemts.'
    ], 422);
}

$file = $_FILES['image'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    jsonResponse([
        'success' => false,
        'message' => 'Neizdevās augšupielādēt attēlu.'
    ], 422);
}

$maxFileSize = 2 * 1024 * 1024;

if ((int) $file['size'] > $maxFileSize) {
    jsonResponse([
        'success' => false,
        'message' => 'Attēls ir pārāk liels. Maksimālais izmērs ir 2 MB.'
    ], 422);
}

if (!is_uploaded_file($file['tmp_name'])) {
    jsonResponse([
        'success' => false,
        'message' => 'Nederīgs augšupielādētais fails.'
    ], 422);
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
];

if (!isset($allowedTypes[$mimeType])) {
    jsonResponse([
        'success' => false,
        'message' => 'Atļauti tikai JPG, PNG un WEBP attēli.'
    ], 422);
}

$stmt = $pdo->prepare(
    'SELECT id, image
     FROM medicines
     WHERE id = ?
     AND deleted_at IS NULL
     LIMIT 1'
);
$stmt->execute([$medicineId]);
$medicine = $stmt->fetch();

if (!$medicine) {
    jsonResponse([
        'success' => false,
        'message' => 'Zāles nav atrastas.'
    ], 404);
}

$uploadDirectory = dirname(__DIR__) . '/uploads/medicines';

if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
    jsonResponse([
        'success' => false,
        'message' => 'Neizdevās izveidot attēlu mapi.'
    ], 500);
}

$extension = $allowedTypes[$mimeType];
$fileName = bin2hex(random_bytes(16)) . '.' . $extension;
$absolutePath = $uploadDirectory . '/' . $fileName;
$databasePath = 'uploads/medicines/' . $fileName;

if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
    jsonResponse([
        'success' => false,
        'message' => 'Neizdevās saglabāt attēlu.'
    ], 500);
}

try {
    $updateStmt = $pdo->prepare(
        'UPDATE medicines
         SET image = ?
         WHERE id = ?
         AND deleted_at IS NULL'
    );

    $updateStmt->execute([$databasePath, $medicineId]);

    if ($updateStmt->rowCount() === 0) {
        @unlink($absolutePath);

        jsonResponse([
            'success' => false,
            'message' => 'Neizdevās piesaistīt attēlu zālēm.'
        ], 500);
    }

    $oldImage = $medicine['image'] ?? null;

    if ($oldImage && str_starts_with($oldImage, 'uploads/medicines/')) {
        $oldAbsolutePath = dirname(__DIR__) . '/' . $oldImage;

        if (is_file($oldAbsolutePath)) {
            @unlink($oldAbsolutePath);
        }
    }

    logAction(
        $pdo,
        'UPLOAD_IMAGE',
        'medicine',
        (int) $medicineId,
        'Augšupielādēts zāļu attēls'
    );

    jsonResponse([
        'success' => true,
        'message' => 'Attēls veiksmīgi augšupielādēts.',
        'image' => $databasePath
    ]);

} catch (PDOException $e) {
    @unlink($absolutePath);

    jsonResponse([
        'success' => false,
        'message' => 'Neizdevās saglabāt attēla informāciju.'
    ], 500);
}
