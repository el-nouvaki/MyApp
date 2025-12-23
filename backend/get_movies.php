<?php
/**
 * backend/get_movies.php
 */

require_once __DIR__ . '/../config/db.php'; 
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();
$userID = $_SESSION['user_id'] ?? null;

$search_query = "";
$params = [];

// 1. Ρυθμίσεις Σελιδοποίησης
$limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], [20, 50, 100]) ? (int)$_GET['limit'] : 20;
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 2. Βασικό Query για το Count (για να ξέρουμε το σύνολο των σελίδων)
$countSql = "SELECT COUNT(*) FROM movies";
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_query = trim($_GET['q']);
    $countSql .= " WHERE title LIKE ?";
    $countParams = ["%$search_query%"];
} else {
    $countParams = [];
}

try {
    $stmtCount = $pdo->prepare($countSql);
    $stmtCount->execute($countParams);
    $totalMovies = $stmtCount->fetchColumn();
    $totalPages = ceil($totalMovies / $limit);

    // 3. Κύριο Query με LIMIT και OFFSET
    $sql = "SELECT m.id, m.title, m.release_date, m.poster_path, m.TMDB_vote_average,
        (SELECT COUNT(*) FROM movie_likes WHERE movieID = m.id) as likes_count 
        FROM movies m";
    
    if (!empty($search_query)) {
        $sql .= " WHERE title LIKE ?";
        $params[] = "%$search_query%";
    }
    
    $sql .= " ORDER BY release_date DESC LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $movies = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = "Σφάλμα φόρτωσης ταινιών: " . $e->getMessage();
    $movies = [];
    $totalMovies = 0;
    $totalPages = 0;
}

$img_base_url = "https://image.tmdb.org/t/p/w500";
?>