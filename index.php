<?php
/**
 * index.php
 * * * Entry Point.
 * Απλά ελέγχει αν υπάρχει session και δρομολογεί ανάλογα.
 */
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: homepage.php");
} else {
    header("Location: login.php");
}
exit;
?>