<?php
/**
 * backend/get_view_list.php
 * * * Λογική προβολής μιας λίστας.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();
$currentUserID = $_SESSION['user_id'] ?? 0;

// Validation ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: profile.php");
    exit;
}

$listID = $_GET['id'];
$poster_base_url = "https://image.tmdb.org/t/p/w500";

try {
    // 1. Λήψη Πληροφοριών Λίστας & Κατόχου
    $sql = "SELECT ul.*, u.username, u.avatar_url 
            FROM userlists ul 
            JOIN users u ON ul.userID = u.id 
            WHERE ul.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$listID]);
    $list = $stmt->fetch();

    if (!$list) {
        die("Η λίστα δεν βρέθηκε.");
    }

    // 2. SECURITY CHECK: Έλεγχος απορρήτου
    // Αν η λίστα είναι Private ΚΑΙ ο χρήστης δεν είναι ο ιδιοκτήτης -> Access Denied
    if ($list['private'] == 1 && $list['userID'] != $currentUserID) {
        // Επιστρέφουμε ένα flag για να το χειριστεί το frontend ή κάνουμε die εδώ
        die('<div style="text-align:center; margin-top:50px; font-family:sans-serif;"><h1>🔒 Private List</h1><p>You do not have permission to view this list.</p><a href="homepage.php">Go Home</a></div>');
    }

    // 3. Λήψη Ταινιών της Λίστας
    $sqlItems = "SELECT m.id, m.title, m.poster_path, m.release_date, m.TMDB_vote_average, ui.date_added
                 FROM userlists_items ui
                 JOIN movies m ON ui.movieID = m.id
                 WHERE ui.ulID = ?
                 ORDER BY ui.date_added DESC";
    $stmtItems = $pdo->prepare($sqlItems);
    $stmtItems->execute([$listID]);
    $movies = $stmtItems->fetchAll();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>