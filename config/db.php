<?php
/**
 * /config/db.php
 * Αρχείο παραμετροποίησης σύνδεσης βάσης δεδομένων.
 * Περιέχει τα διαπιστευτήρια και τη δημιουργία του αντικειμένου PDO.
 */

$host = 'localhost';
$db   = 'projectapi_db';
$user = 'root';
$pass = '123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

require_once __DIR__ . '/../lib/DataLoader.php';

// Δημιουργία του αντικειμένου $dataLoader
// Αυτό το $dataLoader θα είναι διαθέσιμο σε όποιο αρχείο κάνει include το db.php
$dataLoader = new DataLoader($pdo);
?>