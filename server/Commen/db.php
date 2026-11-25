<?php
$configuration = [];

foreach (['DB_HOST', 'DB_USER', 'DB_PASSWORD', 'DB_NAME'] as $name) {
    $value = getenv($name);

    if ($value === false || ($name !== 'DB_PASSWORD' && $value === '')) {
        throw new RuntimeException("Missing required database environment variable: {$name}");
    }

    $configuration[$name] = $value;
}

$conn = new mysqli(
    $configuration['DB_HOST'],
    $configuration['DB_USER'],
    $configuration['DB_PASSWORD'],
    $configuration['DB_NAME']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>