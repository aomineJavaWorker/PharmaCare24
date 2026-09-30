<?php


function logAction(
    PDO $pdo,
    string $action,
    string $entityType,
    ?int $entityId = null,
    ?string $details = null
): void {

    $userId =
        isset($_SESSION['user_id'])
            ? (int) $_SESSION['user_id']
            : null;


    $username =
        $_SESSION['username']
        ?? 'system';


    $role =
        $_SESSION['role']
        ?? 'system';


    $ipAddress =
        $_SERVER['REMOTE_ADDR']
        ?? null;


    try {

        $stmt = $pdo->prepare(
            'INSERT INTO audit_logs
                (
                    user_id,
                    username,
                    role,
                    action,
                    entity_type,
                    entity_id,
                    details,
                    ip_address
                )
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );


        $stmt->execute([
            $userId,
            $username,
            $role,
            $action,
            $entityType,
            $entityId,
            $details,
            $ipAddress
        ]);


    } catch (Throwable $e) {

        error_log(
            'Audit log error: ' .
            $e->getMessage()
        );
    }
}