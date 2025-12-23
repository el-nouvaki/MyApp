<?php
/**
 * backend/get_lists.php
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();
$currentUserID = $_SESSION['user_id'] ?? 0;
// Φέρνουμε το roleID από το session (το ορίσαμε στο login ή στο get_user_profile)
$currentUserRole = $_SESSION['roleID'] ?? 2; 

try {
    // Η νέα συνθήκη WHERE επιτρέπει την προβολή αν:
    // 1. Η λίστα είναι δημόσια (private = 0)
    // 2. Ο χρήστης είναι ο ιδιοκτήτης (userID = currentUserID)
    // 3. Ο χρήστης είναι Admin (currentUserRole = 1)
    $sql = "SELECT ul.*, u.username, u.avatar_url,
            (SELECT COUNT(*) FROM userlists_items WHERE ulID = ul.id) as movie_count,
            (SELECT COUNT(*) FROM userlists_likes WHERE ulID = ul.id) as likes_count,
            (SELECT COUNT(*) FROM userlists_likes WHERE ulID = ul.id AND userID = :userId1) as user_liked
            FROM userlists ul
            LEFT JOIN users u ON ul.userID = u.id
            WHERE ul.private = 0 
               OR ul.userID = :userId2 
               OR :userRole = 1
            ORDER BY ul.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':userId1'  => $currentUserID,
        ':userId2'  => $currentUserID,
        ':userRole' => $currentUserRole
    ]);
    
    $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($lists as $key => $list) {
        $stmtImg = $pdo->prepare("
            SELECT m.poster_path 
            FROM movies m 
            INNER JOIN userlists_items uli ON m.id = uli.movieID 
            WHERE uli.ulID = :listId
            LIMIT 5");
        $stmtImg->execute([':listId' => $list['id']]);
        $lists[$key]['posters'] = $stmtImg->fetchAll(PDO::FETCH_COLUMN);
    }

} catch (Exception $e) {
    error_log("Lists SQL Error: " . $e->getMessage());
    $lists = [];
}

$poster_base_url = "https://image.tmdb.org/t/p/w200";
$noAvatar = "./frontend/assets/images/noavatar.png";
?>