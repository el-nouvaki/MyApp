<?php
/**
 * backend/list_items_controller.php
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$currentUserID = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

try {
    if ($action === 'remove_item') {
        $listID = $_POST['list_id'];
        $movieID = $_POST['movie_id'];

        // ΠΡΩΤΑ ΕΛΕΓΧΟΥΜΕ αν η λίστα ανήκει στον χρήστη
        $stmtCheck = $pdo->prepare("SELECT userID FROM userlists WHERE id = ?");
        $stmtCheck->execute([$listID]);
        $ownerID = $stmtCheck->fetchColumn();

        if ($ownerID == $currentUserID || $_SESSION['roleID'] == 1) {
            // Διαγραφή της ταινίας από τη λίστα
            $stmtDel = $pdo->prepare("DELETE FROM userlists_items WHERE ulID = ? AND movieID = ?");
            $stmtDel->execute([$listID, $movieID]);
            
            header("Location: ../list_details.php?id=" . $listID . "&msg=item_removed");
        } else {
            die("Δεν έχετε δικαίωμα να τροποποιήσετε αυτή τη λίστα.");
        }
    }
} catch (Exception $e) {
    error_log("List Items Controller Error: " . $e->getMessage());
    die("Παρουσιάστηκε σφάλμα κατά την επεξεργασία της λίστας.");
}