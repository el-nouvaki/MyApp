<?php
/**
 * backend/get_user_reviews.php
 */
require_once __DIR__ . '/../config/db.php';

$reviews = [];

if (isset($targetID)) {
    try {
        $sql = "SELECT r.*, m.title, m.poster_path,
                (SELECT rating FROM movie_ratings WHERE userID = r.userID AND movieID = r.movieID) as user_rating,
                (SELECT COUNT(*) FROM moviereviews_likes WHERE reviewID = r.id) as review_like_count,
                (SELECT 1 FROM movie_likes WHERE userID = r.userID AND movieID = r.movieID LIMIT 1) as movie_liked
                FROM moviereviews r
                JOIN movies m ON r.movieID = m.id
                WHERE r.userID = ?
                ORDER BY r.date_created DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$targetID]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        error_log("Fetch User Reviews Error: " . $e->getMessage());
    }
}