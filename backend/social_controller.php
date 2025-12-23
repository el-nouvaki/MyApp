<?php
/**
 * backend/social_controller.php
 * Διαχειρίζεται τα Follow/Unfollow μεταξύ χρηστών και την επιστροφή στη σωστή σελίδα.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'toggle_follow') {
    
    $followerID = $_SESSION['user_id']; // Ο συνδεδεμένος χρήστης
    $followedID = (int)$_POST['followed_id']; // Ο χρήστης-στόχος
    
    // Λήψη της σελίδας επιστροφής (για διατήρηση αναζήτησης/offset)
    $redirectTo = $_POST['redirect_to'] ?? '../members.php';

    // Απαγόρευση να ακολουθήσει ο χρήστης τον εαυτό του
    if ($followerID == $followedID) {
        header("Location: " . $redirectTo);
        exit;
    }

    try {
        // 1. Έλεγχος αν υπάρχει ήδη η σχέση follows στη βάση
        $check = $pdo->prepare("SELECT * FROM follows WHERE followerID = ? AND followedID = ?");
        $check->execute([$followerID, $followedID]);

        if ($check->rowCount() > 0) {
            // Αν υπάρχει ήδη -> UNFOLLOW
            $del = $pdo->prepare("DELETE FROM follows WHERE followerID = ? AND followedID = ?");
            $del->execute([$followerID, $followedID]);
        } else {
            // Αν δεν υπάρχει -> FOLLOW
            $ins = $pdo->prepare("INSERT INTO follows (followerID, followedID) VALUES (?, ?)");
            $ins->execute([$followerID, $followedID]);
        }
        
        // Ανακατεύθυνση στη σελίδα από την οποία προήλθε η κλήση
        header("Location: " . $redirectTo);
        exit;

    } catch (Exception $e) {
        error_log("Social Controller Error: " . $e->getMessage());
        die("Error: " . $e->getMessage());
    }
} else {
    // Προστασία από απευθείας πρόσβαση στο αρχείο
    header("Location: ../homepage.php");
    exit;
}
?>