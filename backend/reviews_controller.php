<?php
/**
 * /backend/reviews_controller.php
 * * * Controller για Κριτικές και Βαθμολογίες + Likes.
 */

session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    // --- 1. ΥΠΟΒΟΛΗ ΚΡΙΤΙΚΗΣ ---
    if ($_POST['action'] == 'submit_review') {
        $userID = $_SESSION['user_id'];
        $movieID = $_POST['movie_id'];
        $reviewText = trim($_POST['review']);
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

        if (empty($reviewText) && $rating == 0) {
            header("Location: ../movie_details.php?id=$movieID&error=empty_review");
            exit;
        }

        try {
            $pdo->beginTransaction();

            if (!empty($reviewText)) {
                $sqlReview = "INSERT INTO moviereviews (userID, movieID, review) VALUES (?, ?, ?)
                              ON DUPLICATE KEY UPDATE review = VALUES(review), date_updated = NOW()";
                $stmt = $pdo->prepare($sqlReview);
                $stmt->execute([$userID, $movieID, $reviewText]);
            }

            if ($rating > 0 && $rating <= 5) {
                $sqlRating = "INSERT INTO movie_ratings (userID, movieID, rating) VALUES (?, ?, ?)
                              ON DUPLICATE KEY UPDATE rating = VALUES(rating), date_updated = NOW()";
                $stmt = $pdo->prepare($sqlRating);
                $stmt->execute([$userID, $movieID, $rating]);
            }

            $pdo->commit();
            header("Location: ../movie_details.php?id=$movieID&msg=review_saved");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            die("Error saving review: " . $e->getMessage());
        }
    }

    // --- 2. TOGGLE REVIEW LIKE (ΝΕΟ) ---
    elseif ($_POST['action'] == 'toggle_like') {
        $likerID = $_SESSION['user_id'];
        $reviewID = $_POST['review_id'];
        
        // Επιστροφή στην ίδια σελίδα
        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? "../reviews.php";

        try {
            // Έλεγχος αν υπάρχει ήδη like (πίνακας moviereviews_likes)
            $check = $pdo->prepare("SELECT * FROM moviereviews_likes WHERE userID = ? AND reviewID = ?");
            $check->execute([$likerID, $reviewID]);

            if ($check->rowCount() > 0) {
                // UNLIKE
                $sql = "DELETE FROM moviereviews_likes WHERE userID = ? AND reviewID = ?";
                $pdo->prepare($sql)->execute([$likerID, $reviewID]);
            } else {
                // LIKE
                $sql = "INSERT INTO moviereviews_likes (userID, reviewID) VALUES (?, ?)";
                $pdo->prepare($sql)->execute([$likerID, $reviewID]);
            }
            
            header("Location: $redirectUrl");
            exit;

        } catch (Exception $e) {
            die("Error processing like: " . $e->getMessage());
        }
    }
}
?>