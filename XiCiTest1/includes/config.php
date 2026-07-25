<?php

$db_host = getenv("DB_HOST");
$db_username = getenv("DB_USER");
$db_passwd = getenv("DB_PASSWORD");
$db_port = getenv("DB_PORT");
$db_name = getenv("DB_NAME");

$conn = mysqli_init();

mysqli_real_connect(
    $conn,
    $db_host,
    $db_username,
    $db_passwd,
    $db_name,
    $db_port,
    NULL,
    MYSQLI_CLIENT_SSL
);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>