<?php
$host = 'localhost';
$dbname = 'smart_college';
$username = 'root'; // Default XAMPP/WAMP username
$password = '';     // Default XAMPP/WAMP password (usually empty)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set character set to UTF-8
    $pdo->exec("SET NAMES utf8mb4");
} catch(PDOException $e) {
    die("ERROR: Could not connect to the database. " . $e->getMessage());
}
?>
