<?php

$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$db = getenv("DB_NAME");

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $db
);

if (!$conn) {
    die("Database connection failed");
}

?>