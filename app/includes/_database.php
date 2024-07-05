<?php

require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


try {
    $dbCo = new PDO(
        'mysql:host=' . $_ENV['DB_HOST']  . ';dbname=' . $_ENV['DB_NAME']  . ';charset=utf8',
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD']
    );
    $dbCo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );
} catch (Exception $e) {
    die('Unable to connect to the database.' . $e->getMessage());
}
