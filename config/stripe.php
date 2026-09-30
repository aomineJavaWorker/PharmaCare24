<?php

function stripeSecretKey(): string
{
    $envPath = dirname(__DIR__) . '/.env';

    if (!file_exists($envPath)) {
        throw new RuntimeException(
            '.env fails nav atrasts: ' . $envPath
        );
    }

    $lines = file(
        $envPath,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach ($lines as $line) {

        $line = trim($line);

        if (
            $line === '' ||
            str_starts_with($line, '#')
        ) {
            continue;
        }

        if (!str_contains($line, '=')) {
            continue;
        }

        [$key, $value] =
            explode('=', $line, 2);

        $key = trim($key);

        $value = trim(
            $value,
            " \t\n\r\0\x0B\"'"
        );

        if ($key === 'STRIPE_SECRET_KEY') {
            return $value;
        }
    }

    return '';
}