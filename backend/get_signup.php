<?php
/**
 * backend/get_signup.php
 * * * Λογική ανάκτησης δεδομένων για τη σελίδα εγγραφής.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();

// Αν είναι ήδη συνδεδεμένος, δεν έχει δουλειά εδώ
if (isset($_SESSION['user_id'])) {
    header("Location: homepage.php");
    exit;
}

// Λήψη Genres για το dropdown
try {
    $genres = $pdo->query("SELECT id, name FROM genres ORDER BY name ASC")->fetchAll();
} catch (Exception $e) {
    $genres = [];
}
?>