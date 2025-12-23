<?php
/**
 * backend/get_user_likes.php
 */
require_once __DIR__ . '/../config/db.php';

// Το $targetID ορίζεται στο profile.php ή view_profile.php
$likedMovies = [];

if (isset($targetID)) {
    try {
        $stmtLikes = $pdo->prepare("
            SELECT m.* FROM movies m 
            JOIN movie_likes ml ON m.id = ml.movieID 
            WHERE ml.userID = ? 
            ORDER BY ml.date_liked DESC");
        $stmtLikes->execute([$targetID]);
        $likedMovies = $stmtLikes->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Fetch Likes Error: " . $e->getMessage());
    }
}