<?php
/**
 * backend/movie_likes_controller.php
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['movie_id'])) {
    $userID = $_SESSION['user_id'];
    $movieID = (int)$_POST['movie_id'];
    $redirectTo = $_POST['redirect_to'] ?? '../homepage.php';

    try {
        // Έλεγχος αν υπάρχει ήδη το like
        $stmt = $pdo->prepare("SELECT 1 FROM movie_likes WHERE userID = ? AND movieID = ?");
        $stmt->execute([$userID, $movieID]);

        if ($stmt->fetch()) {
            // Αν υπάρχει, το αφαιρούμε (Unlike)
            $del = $pdo->prepare("DELETE FROM movie_likes WHERE userID = ? AND movieID = ?");
            $del->execute([$userID, $movieID]);
        } else {
            // Αν δεν υπάρχει, το προσθέτουμε (Like)
            $ins = $pdo->prepare("INSERT INTO movie_likes (userID, movieID) VALUES (?, ?)");
            $ins->execute([$userID, $movieID]);
        }

        header("Location: " . $redirectTo);
        exit;
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}