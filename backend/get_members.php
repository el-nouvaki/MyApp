<?php
/**
 * backend/get_members.php
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();
$currentUserID = $_SESSION['user_id'] ?? 0;
$currentUserRole = $_SESSION['roleID'] ?? 0;	// Απαραίτητο για τον έλεγχο Admin

// 1. Παράμετροι Φιλτραρίσματος & Σελίδας
$search = isset($_GET['q']) ? trim($_GET['q']) : "";
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$totalToFetch = $limit + $offset;

$params = [];
$params[] = $currentUserID; // Για το LEFT JOIN follows

// 2. Query με Counts για Lists & Reviews
$sql = "SELECT u.id, u.username, u.avatar_url, u.date_created, u.roleID, u.active,
        (SELECT COUNT(*) FROM userlists WHERE userID = u.id AND private = 0) as list_count,
        (SELECT COUNT(*) FROM moviereviews WHERE userID = u.id) as review_count,
        (CASE WHEN f.followerID IS NOT NULL THEN 1 ELSE 0 END) as is_following
        FROM users u
        LEFT JOIN follows f ON (u.id = f.followedID AND f.followerID = ?)
		WHERE 1=1"; 	// XREIAZETAI ΟΠΩΣΔΗΠΟΤΕ
		
// ΠΡΟΣΘΗΚΗ: Αν δεν είναι Admin, βλέπει μόνο τους Active
if ($currentUserRole != 1) {
    $sql .= " AND u.active = 1";
}

if (!empty($search)) {
    $sql .= " AND u.username LIKE ?";
    $params[] = "%$search%";
}

// Ταξινόμηση u.date_created (u.username για αλφαβητικά)
$sql .= " ORDER BY u.username LIMIT " . (int)$totalToFetch;

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Έλεγχος αν υπάρχουν περισσότερα μέλη για το "View More"
    $countSql = "SELECT COUNT(*) FROM users WHERE 1=1";
    $countParams = [];
	if ($currentUserRole != 1) {
        $countSql .= " AND active = 1";
    }
    if (!empty($search)) {
        $countSql .= " AND username LIKE ?";
        $countParams[] = "%$search%";
    }
    $totalUsers = $pdo->prepare($countSql);
    $totalUsers->execute($countParams);
    $totalCount = $totalUsers->fetchColumn();
    
    $hasMore = $totalCount > $totalToFetch;

} catch (Exception $e) {
    $members = [];
    error_log("Members Error: " . $e->getMessage());
}
?>