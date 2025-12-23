<?php
/**
 * /lib/functions.php
 * * * Βιβλιοθήκη γενικών συναρτήσεων (Helpers).
 */

// Μετατροπή λεπτών σε μορφή "2h 15m"
function formatRuntime($minutes) {
    if ($minutes < 1) return "";
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    return "{$hours}h {$mins}m";
}

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Ασφαλής εκκίνηση Session (για να μην χτυπάει αν υπάρχει ήδη)
function startSessionSafe() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Καθαρίζει τα δεδομένα εισόδου από ειδικούς χαρακτήρες
 * για αποφυγή XSS επιθέσεων.
 * * @param string $data
 * @return string
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>