<?php 
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASS');
$sslmode = getenv('DB_SSLMODE') ?: 'require';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=$sslmode";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    // echo "✅ Successfully connected to the database!";
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
