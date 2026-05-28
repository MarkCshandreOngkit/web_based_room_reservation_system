<?php
$host = 'localhost';
$dbname = 'loginsystem'; // Replace with your actual database name
$username = 'users';              // Replace with your database username
$password = '';                  // Replace with your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set error mode to exception to catch connection errors easily
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>