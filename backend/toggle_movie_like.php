<?php
// backend/toggle_movie_like.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

header('Content-Type: application/json');
startSessionSafe();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'NOT_LOGGED_IN']); exit;
}

$userID = $_SESSION['user_id'];
$movieID = $_POST['movie_id'];

try {
    $stmt = $pdo->prepare("SELECT 1 FROM movie_likes WHERE userID = ? AND movieID = ?");
    $stmt->execute([$userID, $movieID]);
    
    if ($stmt->fetch()) {
        $pdo->prepare("DELETE FROM movie_likes WHERE userID = ? AND movieID = ?")->execute([$userID, $movieID]);
        $status = 'unliked';
    } else {
        $pdo->prepare("INSERT INTO movie_likes (userID, movieID, date_liked) VALUES (?, ?, NOW())")->execute([$userID, $movieID]);
        $status = 'liked';
    }

    $count = $pdo->prepare("SELECT COUNT(*) FROM movie_likes WHERE movieID = ?");
    $count->execute([$movieID]);
    
    echo json_encode(['success' => true, 'status' => $status, 'newCount' => $count->fetchColumn()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}