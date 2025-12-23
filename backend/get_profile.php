<?php
/**
 * backend/get_profile.php
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();

// Το προφίλ απαιτεί σύνδεση. Αν είναι guest, redirect στο login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$currentUserID = $_SESSION['user_id'] ?? 0;
$currentUserRole = $_SESSION['roleID'] ?? 2;
$targetID = $_GET['id'] ?? 0;

// Αν ο χρήστης βλέπει τον εαυτό του, πάει στο δικό του προφίλ
if ($targetID == $currentUserID) {
    // Παίρνουμε όλο το query string (π.χ. id=1&tab=reviews)
    $queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: profile.php" . $queryString);
    exit;
}

try {
    // 1. Βασικά στοιχεία χρήστη
    $stmt = $pdo->prepare("SELECT u.*, g.name as favorite_genre_name FROM users u LEFT JOIN genres g ON u.genreID = g.id WHERE u.id = ?");
    $stmt->execute([$targetID]);
    $targetData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$targetData) {
        die("Member not found.");
    }

    // 2. Έλεγχος αν ο συνδεδεμένος χρήστης ακολουθεί αυτόν τον χρήστη
    $isFollowing = false;
    if ($currentUserID > 0) {
        $checkFollow = $pdo->prepare("SELECT 1 FROM follows WHERE followerID = ? AND followedID = ?");
        $checkFollow->execute([$currentUserID, $targetID]);
        $isFollowing = (bool)$checkFollow->fetch();
    }

    // 3. Ανάκτηση Λιστών Ανάκτηση Λιστών (Admin βλέπει και Private)
    if ($currentUserRole == 1) {
        $stmtLists = $pdo->prepare("SELECT * FROM userlists WHERE userID = ? ORDER BY date_created DESC");
    } else {
        $stmtLists = $pdo->prepare("SELECT * FROM userlists WHERE userID = ? AND private = 0 ORDER BY date_created DESC");
    }
    $stmtLists->execute([$targetID]);
    $targetLists = $stmtLists->fetchAll(PDO::FETCH_ASSOC);

    // 4. 4. Ανάκτηση Πρόσφατης Δραστηριότητας
    $sqlActivity = "
        (SELECT 'review' as type, m.title as detail, m.id as item_id, r.date_created as activity_date 
         FROM moviereviews r JOIN movies m ON r.movieID = m.id WHERE r.userID = :u1)
        UNION ALL
        (SELECT 'list' as type, name as detail, id as item_id, date_created as activity_date 
         FROM userlists WHERE userID = :u2 " . ($currentUserRole == 1 ? "" : "AND private = 0") . ")
        UNION ALL
        (SELECT 'like' as type, m.title as detail, m.id as item_id, ml.date_liked as activity_date 
         FROM movie_likes ml JOIN movies m ON ml.movieID = m.id WHERE ml.userID = :u3)
        ORDER BY activity_date DESC LIMIT 10";

    $stmtAct = $pdo->prepare($sqlActivity);
    $stmtAct->execute([':u1' => $targetID, ':u2' => $targetID, ':u3' => $targetID]);
    $activities = $stmtAct->fetchAll(PDO::FETCH_ASSOC);

    $genres = $pdo->query("SELECT * FROM genres ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Profile Error: " . $e->getMessage());
    die("Παρουσιάστηκε σφάλμα κατά την ανάκτηση του προφίλ.");
}