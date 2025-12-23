<?php
/**
 * backend/get_reviews.php
 * * * Λογική για τη σελίδα "Latest Reviews".
 * * * ΤΡΟΠΟΠΟΙΗΣΗ: Εμφανίζει μόνο το πιο πρόσφατο review ανά ταινία (Unique Movies).
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();
$currentUserID = $_SESSION['user_id'] ?? 0;

try {
    // Query: Reviews + Movie Info + User Info + Ratings + Likes Count
    // Χρησιμοποιούμε Subquery (πίνακας 'latest') για να φιλτράρουμε μόνο το τελευταίο review κάθε ταινίας
    $sql = "
        SELECT r.id as reviewID, r.review, r.date_created, 
               u.id as authorID, u.username, u.avatar_url, 
               m.id as movieID, m.title, m.poster_path, m.release_date,
               mr.rating,
               (SELECT COUNT(*) FROM moviereviews_likes rl WHERE rl.reviewID = r.id) as like_count,
               (SELECT COUNT(*) FROM moviereviews_likes rl WHERE rl.reviewID = r.id AND rl.userID = ?) as user_liked
        FROM moviereviews r
        
        -- Ενώνουμε με έναν προσωρινό πίνακα που περιέχει μόνο την πιο πρόσφατη ημερομηνία για κάθε MovieID
        JOIN (
            SELECT movieID, MAX(date_created) as max_date
            FROM moviereviews
            GROUP BY movieID
        ) latest ON r.movieID = latest.movieID AND r.date_created = latest.max_date
        
        JOIN users u ON r.userID = u.id
        JOIN movies m ON r.movieID = m.id
        LEFT JOIN movie_ratings mr ON (r.userID = mr.userID AND r.movieID = mr.movieID)
        
        ORDER BY r.date_created DESC
        LIMIT 5
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$currentUserID]);
    $latestReviews = $stmt->fetchAll();

} catch (Exception $e) {
    $latestReviews = [];
    // die("Error: " . $e->getMessage());
}

$poster_base_url = "https://image.tmdb.org/t/p/w200";
?>