<?php
/**
 * backend/get_movie_details.php
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();
$userID = $_SESSION['user_id'] ?? null;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: movies.php");
    exit;
}

$movieID = $_GET['id'];
$img_base_url = "https://image.tmdb.org/t/p/original";
$poster_base_url = "https://image.tmdb.org/t/p/w500";

try {
    // 1. Movie
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$movieID]);
    $movie = $stmt->fetch();
    if (!$movie) die("Movie not found.");

    // 2. Genres
    $stmt = $pdo->prepare("SELECT g.name FROM genres g JOIN movie_genres mg ON g.id = mg.genreID WHERE mg.movieID = ?");
    $stmt->execute([$movieID]);
    $genres = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // 3. Cast
    $stmt = $pdo->prepare("SELECT p.name, p.profile_path, mc.character_name 
        FROM people p 
        JOIN moviecrew mc ON p.id = mc.personID 
        WHERE mc.movieID = ? 
        AND mc.jobID = 1  /* <--- μόνο actors */
        GROUP BY p.id 
        ORDER BY mc.actor_order ASC 
        LIMIT 10				/* μόνο οι πρώτοι 10; */
		");
    $stmt->execute([$movieID]);
    $cast = $stmt->fetchAll();

    // 4. Reviews & Likes (UPDATED)
    $reviewLimit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    if (!in_array($reviewLimit, [5, 10, 20])) $reviewLimit = 5;

    $stmt = $pdo->prepare("
        SELECT r.id as reviewID, r.review, r.date_created, u.username, u.avatar_url, mr.rating,
        (SELECT COUNT(*) FROM moviereviews_likes rl WHERE rl.reviewID = r.id) as like_count,
        (SELECT COUNT(*) FROM moviereviews_likes rl WHERE rl.reviewID = r.id AND rl.userID = ?) as user_liked
        FROM moviereviews r 
        JOIN users u ON r.userID = u.id 
        LEFT JOIN movie_ratings mr ON (r.userID = mr.userID AND r.movieID = mr.movieID)
        WHERE r.movieID = ? 
        ORDER BY r.date_created DESC
        LIMIT $reviewLimit
    ");
    $stmt->execute([$userID ?? 0, $movieID]);
    $reviews = $stmt->fetchAll();

    // 5. User Specific
    $userLists = [];
    $isLiked = false;
    $myReview = null;
    $inWatchlist = false;

    if ($userID) {
        $stmt = $pdo->prepare("SELECT id, name FROM userlists WHERE userID = ?");
        $stmt->execute([$userID]);
        $userLists = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM movie_likes WHERE userID = ? AND movieID = ?");
        $stmt->execute([$userID, $movieID]);
        $isLiked = $stmt->rowCount() > 0;

        $stmt = $pdo->prepare("SELECT * FROM watchlist_items WHERE userID = ? AND movieID = ?");
        $stmt->execute([$userID, $movieID]);
        $inWatchlist = $stmt->rowCount() > 0;

        $stmt = $pdo->prepare("SELECT r.review, mr.rating FROM moviereviews r LEFT JOIN movie_ratings mr ON (r.userID = mr.userID AND r.movieID = mr.movieID) WHERE r.userID = ? AND r.movieID = ?");
        $stmt->execute([$userID, $movieID]);
        $myReview = $stmt->fetch();
    }
	
	// 6. Συνολικά Likes Ταινίας (για όλους τους χρήστες)
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM movie_likes WHERE movieID = ?");
    $stmtTotal->execute([$movieID]);
    $movie['likes_count'] = $stmtTotal->fetchColumn();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>