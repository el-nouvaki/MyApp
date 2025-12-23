<?php
// backend/like_list.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

header('Content-Type: application/json');
startSessionSafe();

// Έλεγχος αν ο χρήστης είναι όντως login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'NOT_LOGGED_IN']);
    exit;
}

if (!isset($_POST['list_id'])) {
    echo json_encode(['success' => false, 'message' => 'MISSING_ID']);
    exit;
}

$userID = $_SESSION['user_id'];
$listID = $_POST['list_id'];

try {
    // Επιλέγουμε userID αντί για id, γιατί ο πίνακας δεν έχει στήλη id
    $stmt = $pdo->prepare("SELECT userID FROM userlists_likes WHERE userID = :uid AND ulID = :lid");
    $stmt->execute([':uid' => $userID, ':lid' => $listID]);
    $like = $stmt->fetch();

    if ($like) {
        // Unlike
        $stmt = $pdo->prepare("DELETE FROM userlists_likes WHERE userID = :uid AND ulID = :lid");
        $stmt->execute([':uid' => $userID, ':lid' => $listID]);
        $status = 'unliked';
    } else {
        // Like
        $stmt = $pdo->prepare("INSERT INTO userlists_likes (userID, ulID) VALUES (:uid, :lid)");
        $stmt->execute([':uid' => $userID, ':lid' => $listID]);
        $status = 'liked';
    }

    // Μέτρηση νέων likes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM userlists_likes WHERE ulID = :lid");
    $stmt->execute([':lid' => $listID]);
    $newCount = $stmt->fetchColumn();

    echo json_encode(['success' => true, 'status' => $status, 'newCount' => $newCount]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'SQL_ERROR: ' . $e->getMessage()]);
}