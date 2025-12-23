<?php
/**
 * backend/get_list_details.php - Λογική ανάκτησης δεδομένων λίστας
 */
require_once 'config/db.php'; 
require_once 'lib/functions.php'; 

startSessionSafe(); 

$currentUserID = $_SESSION['user_id'] ?? 0;
$currentUserRole = $_SESSION['roleID'] ?? 2;
$listID = $_GET['id'] ?? 0;

try {
    // 1. Ανάκτηση βασικών στοιχείων της λίστας και του δημιουργού
    $stmt = $pdo->prepare("
        SELECT ul.*, u.username, u.avatar_url,
        (SELECT COUNT(*) FROM userlists_likes WHERE ulID = ul.id) as likes_count
        FROM userlists ul
        JOIN users u ON ul.userID = u.id
        WHERE ul.id = ?");
    $stmt->execute([$listID]);
    $listInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listInfo) {
        die("Η λίστα δεν βρέθηκε.");
    }

    // --- ΠΡΟΣΘΗΚΗ: Έλεγχος αν ο συνδεδεμένος χρήστης έχει κάνει Like ---
    $isLikedByMe = false;
    if ($currentUserID > 0) {
        $stmtLike = $pdo->prepare("SELECT 1 FROM userlists_likes WHERE userID = ? AND ulID = ?");
        $stmtLike->execute([$currentUserID, $listID]);
        $isLikedByMe = (bool)$stmtLike->fetch();
    }
    // Ορισμός της μεταβλητής που λείπει
    $listInfo['user_liked'] = $isLikedByMe;

    // 2. Έλεγχος Privacy
    if ($listInfo['private'] == 1 && $currentUserID != $listInfo['userID'] && $currentUserRole != 1) {
        die("Αυτή η λίστα είναι ιδιωτική.");
    }

    // 3. Ανάκτηση των ταινιών
    $stmtMovies = $pdo->prepare("
        SELECT m.id, m.title, m.poster_path, m.release_date
        FROM movies m 
        INNER JOIN userlists_items uli ON (m.id = uli.movieID)
        WHERE uli.ulID = ? 
        ORDER BY uli.date_added DESC");
    $stmtMovies->execute([$listID]);
    $movies = $stmtMovies->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Σφάλμα συστήματος: " . $e->getMessage());
}

$poster_base_url = "https://image.tmdb.org/t/p/w300";