<?php

$envPath = dirname(__DIR__) . '/.env';

if (!file_exists($envPath)) {
    die('Nav atrasts .env konfigurācijas fails.');
}


$env = parse_ini_file(
    $envPath,
    false,
    INI_SCANNER_RAW
);


$host = $env['DB_HOST'] ?? 'localhost';

$dbname = $env['DB_NAME'] ?? '';

$username = $env['DB_USER'] ?? '';

$password = $env['DB_PASSWORD'] ?? '';

$charset = $env['DB_CHARSET'] ?? 'utf8mb4';


$dsn =
    "mysql:host={$host};" .
    "dbname={$dbname};" .
    "charset={$charset}";


$options = [
    PDO::ATTR_ERRMODE =>
        PDO::ERRMODE_EXCEPTION,

    PDO::ATTR_DEFAULT_FETCH_MODE =>
        PDO::FETCH_ASSOC,

    PDO::ATTR_EMULATE_PREPARES =>
        false
];


try {

    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        $options
    );

} catch (PDOException $e) {

    die('Neizdevās pieslēgties datubāzei.');
}