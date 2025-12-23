<?php
/**
 * backend/get_user_profile.php
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ΑΛΛΑΓΗ: Ονομάζουμε τη μεταβλητή $currentUserID
$currentUserID = $_SESSION['user_id']; 

try {
    // 1. Στοιχεία χρήστη (Χρησιμοποιούμε παντού το $currentUserID)
    $stmt = $pdo->prepare("SELECT u.*, g.name as favorite_genre_name 
                           FROM users u 
                           LEFT JOIN genres g ON u.genreID = g.id 
                           WHERE u.id = :uid");
    $stmt->execute([':uid' => $currentUserID]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $_SESSION['roleID'] = $userData['roleID'];

	// Στατιστικά Followers & Following
    $stmtFollowers = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE followedID = ?");
    $stmtFollowers->execute([$currentUserID]);
    $followersCount = $stmtFollowers->fetchColumn();

    $stmtFollowing = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE followerID = ?");
    $stmtFollowing->execute([$currentUserID]);
    $followingCount = $stmtFollowing->fetchColumn();

    // Στατιστικό Reviews
    $stmtRevCount = $pdo->prepare("SELECT COUNT(*) FROM moviereviews WHERE userID = ?");
    $stmtRevCount->execute([$currentUserID]);
    $reviewsCount = $stmtRevCount->fetchColumn();

    // 2. Watchlist
    $stmt = $pdo->prepare("SELECT m.id, m.title, m.poster_path 
                       FROM watchlist_items wi 
                       JOIN movies m ON wi.movieID = m.id 
                       WHERE wi.userID = :uid 
                       ORDER BY wi.date_added DESC");
    $stmt->execute([':uid' => $currentUserID]);
    $watchlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// --- Ανάκτηση χρηστών που ΑΚΟΛΟΥΘΩ (Following) ---
	$stmtFollowingList = $pdo->prepare("
		SELECT u.id, u.username, u.avatar_url, u.roleID 
		FROM users u 
		JOIN follows f ON u.id = f.followedID 
		WHERE f.followerID = ?");
	$stmtFollowingList->execute([$currentUserID]);
	$followingList = $stmtFollowingList->fetchAll(PDO::FETCH_ASSOC);

	// --- Ανάκτηση χρηστών που ΜΕ ΑΚΟΛΟΥΘΟΥΝ (Followers) ---
	$stmtFollowersList = $pdo->prepare("
		SELECT u.id, u.username, u.avatar_url, u.roleID 
		FROM users u 
		JOIN follows f ON u.id = f.followerID 
		WHERE f.followedID = ?");
	$stmtFollowersList->execute([$currentUserID]);
	$followersList = $stmtFollowersList->fetchAll(PDO::FETCH_ASSOC);

    // 3. Προσωπικές Λίστες
    $stmt = $pdo->prepare("SELECT ul.*, 
		(SELECT COUNT(*) FROM userlists_items WHERE ulID = ul.id) as movie_count,
		(SELECT COUNT(*) FROM userlists_likes WHERE ulID = ul.id) as likes_count,
		(SELECT 1 FROM userlists_likes WHERE ulID = ul.id AND userID = :current_uid) as user_liked 
		FROM userlists ul 
		WHERE ul.userID = :uid 
		ORDER BY ul.date_created DESC");
    $stmt->execute([':uid' => $currentUserID, ':current_uid' => $currentUserID]);
    $userLists = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	// Για κάθε λίστα, βρίσκουμε τις πρώτες 5 αφίσες
	foreach ($userLists as $key => $list) {
		$stmtP = $pdo->prepare("
			SELECT m.poster_path 
			FROM movies m 
			JOIN userlists_items uli ON m.id = uli.movieID 
			WHERE uli.ulID = ? 
			LIMIT 5");
		$stmtP->execute([$list['id']]);
		$userLists[$key]['posters'] = $stmtP->fetchAll(PDO::FETCH_COLUMN);
	}

    // 4. Κριτικές
    $stmt = $pdo->prepare("SELECT r.*, m.title FROM moviereviews r 
                           JOIN movies m ON r.movieID = m.id WHERE r.userID = :uid");
    $stmt->execute([':uid' => $currentUserID]);
    $userReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Likes
    $stmt = $pdo->prepare("SELECT m.id, m.title, m.poster_path 
                       FROM movie_likes ml 
                       JOIN movies m ON ml.movieID = m.id 
                       WHERE ml.userID = :uid");
    $stmt->execute([':uid' => $currentUserID]);
    $likedMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Πρόσφατη Δραστηριότητα (UNION Query με προσθήκη item_id)
	$sqlActivity = "
		(SELECT 'watchlist' as type, m.title as detail, m.id as item_id, wi.date_added as activity_date 
		 FROM watchlist_items wi JOIN movies m ON wi.movieID = m.id WHERE wi.userID = :u1)
		UNION ALL
		(SELECT 'review' as type, m.title as detail, m.id as item_id, r.date_created as activity_date 
		 FROM moviereviews r JOIN movies m ON r.movieID = m.id WHERE r.userID = :u2)
		UNION ALL
		(SELECT 'list' as type, name as detail, id as item_id, date_created as activity_date 
		 FROM userlists WHERE userID = :u3)
		UNION ALL
		(SELECT 'like' as type, m.title as detail, m.id as item_id, ml.date_liked as activity_date 
		 FROM movie_likes ml JOIN movies m ON ml.movieID = m.id WHERE ml.userID = :u4)
		ORDER BY activity_date DESC LIMIT 5";

    $stmtAct = $pdo->prepare($sqlActivity);
    $stmtAct->execute([':u1'=>$currentUserID, ':u2'=>$currentUserID, ':u3'=>$currentUserID, ':u4'=>$currentUserID]);
    $activities = $stmtAct->fetchAll(PDO::FETCH_ASSOC);
	
	// Ανάκτηση όλων των genres για να γεμίσει το dropdown στο modal του profile.php
	try {
		$genres = $pdo->query("SELECT * FROM genres ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
	} catch (Exception $e) {
		$genres = [];
		error_log("Error fetching genres: " . $e->getMessage());
	}

} catch (Exception $e) {
    error_log("Profile Data Error: " . $e->getMessage());
    die("Σφάλμα κατά την ανάκτηση των δεδομένων.");
}