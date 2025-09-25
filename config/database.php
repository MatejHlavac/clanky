<?php
// Konfiguračný súbor pre databázu

// Databázové nastavenia
$host = 'localhost';
$dbname = 'clanky_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Chyba pripojenia k databáze: " . $e->getMessage());
}
?>
