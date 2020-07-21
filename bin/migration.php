<?php

use App\Database\DatabaseStorage;
use App\Migrations\Migration001;

require dirname(__DIR__).'/vendor/autoload.php';

$connection = new DatabaseStorage($_ENV['DATABASE_URL']);

try {
    $migration = new Migration001($connection);
    $migration->up();
    $message = "done";
    successMessage($message);
} catch (Exception $e) {
    $message = $e->getMessage();
    failureMessage("\e[31m$message\e[39m\n");
}

/**
 * @param string $message
 */
function successMessage(string $message): void
{
    echo "[32m{$message}[39m\n";
}

/**
 * @param string $message
 */
function failureMessage(string $message): void
{
    echo "[31m{$message}[39m\n";
}