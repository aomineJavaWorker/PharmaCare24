<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


header(
    'Cache-Control: no-store, no-cache, must-revalidate, max-age=0'
);

header('Pragma: no-cache');
header('Expires: 0');


if (!isset($_SESSION['user_id'])) {

    header('Location: login.php');

    exit;
}


/*
 * Vai pašreizējais lietotājs
 * ir galvenais administrators?
 */

function isMainAdmin(): bool
{
    return (
        $_SESSION['role']
        ?? ''
    ) === 'admin';
}


/*
 * Izmantojam lapās,
 * kuras drīkst atvērt tikai admin.
 */

function requireMainAdminPage(): void
{
    if (!isMainAdmin()) {

        header(
            'Location: dashboard.php'
        );

        exit;
    }
}