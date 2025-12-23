<?php
/**
 * backend/profile_controller.php
 * Διαχειρίζεται όλες τις αλλαγές προφίλ (Χρήστη & Admin)
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/functions.php';

startSessionSafe();

// Σιγουρευόμαστε ότι ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_id'])) { 
    exit("Unauthorized Access"); 
}

$currentUserID = $_SESSION['user_id'];
$currentUserRole = $_SESSION['roleID'] ?? 2;
$action = $_POST['action'] ?? '';

try {
    // --- 1. ΕΝΗΜΕΡΩΣΗ ΑΠΟ ΤΟΝ ΧΡΗΣΤΗ (Email, Password, Genre) ---
    if ($action === 'update_profile') {
        $newUsername = trim($_POST['username']);
        $email = trim($_POST['email']);
        $genreID = $_POST['genreID'];
        $password = $_POST['password'];

        // Α. Έλεγχος αν το νέο Username υπάρχει ήδη σε άλλον χρήστη
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmtCheck->execute([$newUsername, $currentUserID]);
        if ($stmtCheck->fetch()) {
            header("Location: ../profile.php?error=username_exists");
            exit;
        }

        // Β. Βασικό Query ενημέρωσης (Username, Email, Genre)
        $sql = "UPDATE users SET username = :uname, email = :email, genreID = :gid";
        $params = [
            ':uname' => $newUsername,
            ':email' => $email,
            ':gid'   => $genreID,
            ':uid'   => $currentUserID
        ];

        // Γ. Αν ο χρήστης έβαλε νέο κωδικό, τον προσθέτουμε στο Query
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password = :pass";
            $params[':pass'] = $hashed;
        }

        $sql .= " WHERE id = :uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Ενημερώνουμε το Username στο session αν άλλαξε
        $_SESSION['username'] = $newUsername;

        header("Location: ../profile.php?success=profile_updated");
		exit;
    }

    // --- 2. ΕΝΗΜΕΡΩΣΗ AVATAR ---
    elseif ($action === 'update_avatar') {
        $url = filter_var($_POST['avatar_url'], FILTER_SANITIZE_URL);
        
        // Έλεγχος αν είναι έγκυρο URL
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $stmt = $pdo->prepare("UPDATE users SET avatar_url = :url WHERE id = :uid");
            $stmt->execute([':url' => $url, ':uid' => $currentUserID]);
            header("Location: ../profile.php?success=avatar_updated");
        } else {
            header("Location: ../profile.php?error=invalid_url");
        }
    }

    // --- 3. ΕΝΗΜΕΡΩΣΗ USERNAME (Profile Name) ---
    elseif ($action === 'update_username') {
        $newUsername = trim($_POST['username']);
        
        // Έλεγχος αν το όνομα υπάρχει ήδη σε άλλον χρήστη
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmtCheck->execute([$newUsername, $currentUserID]);
        
        if ($stmtCheck->fetch()) {
            header("Location: ../profile.php?error=username_exists");
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute([$newUsername, $currentUserID]);
            header("Location: ../profile.php?success=name_updated");
        }
    }

    // --- 4. ADMIN: ΕΝΗΜΕΡΩΣΗ ΣΤΟΙΧΕΙΩΝ ΑΛΛΟΥ ΧΡΗΣΤΗ ---
    elseif ($action === 'admin_update_user') {
        if ($currentUserRole != 1) { die("Access Denied"); }
        
        $targetID = $_POST['target_user_id'];
        $username = $_POST['username'];
        $email    = $_POST['email'];
        $genreID  = $_POST['genreID'];
        $roleID   = $_POST['roleID'];
        $active   = $_POST['active'] ?? 1;

        $stmt = $pdo->prepare("UPDATE users SET username = :uname, email = :email, genreID = :gid, roleID = :rid, active = :act WHERE id = :tid");
        $stmt->execute([
            ':uname' => $username,
            ':email' => $email,
            ':gid'   => $genreID,
            ':rid'   => $roleID,
            ':act'   => $active,
            ':tid'   => $targetID
        ]);
        // Δυναμικό Redirect
        $redirect = $_POST['redirect_to'] ?? '../profile.php';
        $connector = (strpos($redirect, '?') === false) ? '?' : '&';
        header("Location: " . $redirect . $connector . "success=admin_updated_user");
        exit;
    }

    // --- 5. ADMIN: BAN / UNBAN ΧΡΗΣΤΗ ---
    elseif ($action === 'toggle_status') {
        if ($currentUserRole != 1) { die("Access Denied"); }
        
        $targetID = $_POST['target_user_id'];
        $newStatus = $_POST['new_status'];

        $stmt = $pdo->prepare("UPDATE users SET active = :status WHERE id = :tid");
        $stmt->execute([':status' => $newStatus, ':tid' => $targetID]);
		
        // Δυναμικό Redirect
        $redirect = $_POST['redirect_to'] ?? '../profile.php';
        $connector = (strpos($redirect, '?') === false) ? '?' : '&';
        header("Location: " . $redirect . $connector . "success=status_changed");
        exit;
    }

    // --- 6. ΔΙΑΓΡΑΦΗ ΔΕΔΟΜΕΝΩΝ (Watchlist, Reviews, κτλ) ---
    elseif ($action === 'delete_item') {
        $type = $_POST['type'];
        $itemID = $_POST['item_id'];
        
        // Λήψη της σελίδας ανακατεύθυνσης ή default στο profile
        $redirect = $_POST['redirect_to'] ?? 'profile.php';

        if ($type === 'watchlist') {
            $stmt = $pdo->prepare("DELETE FROM watchlist_items WHERE userID = :uid AND movieID = :iid");
        } elseif ($type === 'review') {
            $stmt = $pdo->prepare("DELETE FROM moviereviews WHERE id = :iid AND userID = :uid");
        } elseif ($type === 'userlist') {
            $stmt = $pdo->prepare("DELETE FROM userlists WHERE id = :iid AND userID = :uid");
        } elseif ($type === 'movie_like') {
            $stmt = $pdo->prepare("DELETE FROM movie_likes WHERE userID = :uid AND movieID = :iid");
        }
        
        $stmt->execute([':iid' => $itemID, ':uid' => $currentUserID]);

        // Υπολογισμός του σωστού συνδέσμου για το success message
        $connector = (strpos($redirect, '?') === false) ? '?' : '&';
        header("Location: ../" . $redirect . $connector . "success=item_deleted");
        exit;
    }
	
	// --- 7. ΔΗΜΙΟΥΡΓΙΑ ΝΕΑΣ ΛΙΣΤΑΣ ---
	elseif ($action === 'create_list') {
		$listName = trim($_POST['list_name']);
		// Αν το checkbox είναι επιλεγμένο, η τιμή είναι 1 (Private), αλλιώς 0 (Public)
		$isPrivate = isset($_POST['is_private']) ? 1 : 0;

		if (!empty($listName)) {
			$stmt = $pdo->prepare("INSERT INTO userlists (userID, name, private, date_created) 
								   VALUES (:uid, :name, :priv, NOW())");
			$stmt->execute([
				':uid'  => $currentUserID,
				':name' => $listName,
				':priv' => $isPrivate
			]);
			
			// Ανακατεύθυνση πίσω στο tab των λιστών με μήνυμα επιτυχίας
			header("Location: ../profile.php?tab=lists&success=list_created");
		} else {
			header("Location: ../profile.php?tab=lists&error=empty_name");
		}
		exit;
	}
	
	// 8. ΜΕΤΟΝΟΜΑΣΙΑ ΛΙΣΤΑΣ
    elseif ($action === 'rename_list') {
        $listID = $_POST['list_id'];
        $newName = trim($_POST['new_name']);
        if (!empty($newName)) {
            $stmt = $pdo->prepare("UPDATE userlists SET name = ? WHERE id = ? AND userID = ?");
            $stmt->execute([$newName, $listID, $currentUserID]);
            header("Location: ../list_details.php?id=$listID&success=renamed");
        }
    }

    // 9. ΑΦΑΙΡΕΣΗ ΤΑΙΝΙΑΣ ΑΠΟ ΣΥΓΚΕΚΡΙΜΕΝΗ ΛΙΣΤΑ
    elseif ($action === 'remove_item_from_list') {
        $listID = $_POST['list_id'];
        $movieID = $_POST['movie_id'];
        $stmt = $pdo->prepare("DELETE FROM userlists_items WHERE ulID = ? AND movieID = ? AND EXISTS (SELECT 1 FROM userlists WHERE id = ? AND userID = ?)");
        $stmt->execute([$listID, $movieID, $listID, $currentUserID]);
        header("Location: ../list_details.php?id=$listID&success=movie_removed");
    }
	
	// --- 10. TOGGLE MOVIE LIKE (Καρδούλα) ---
	elseif ($action === 'toggle_movie_like') {
		$movieID = $_POST['movie_id'];
		$redirect = $_POST['redirect_to'] ?? '../profile.php';

		// Έλεγχος αν ο χρήστης έχει ήδη κάνει like στην ταινία
		$check = $pdo->prepare("SELECT 1 FROM movie_likes WHERE userID = ? AND movieID = ?");
		$check->execute([$currentUserID, $movieID]);
		$exists = $check->fetch();

		if ($exists) {
			// Αν υπάρχει, το αφαιρούμε (Unlike)
			$stmt = $pdo->prepare("DELETE FROM movie_likes WHERE userID = ? AND movieID = ?");
		} else {
			// Αν δεν υπάρχει, το προσθέτουμε (Like)
			$stmt = $pdo->prepare("INSERT INTO movie_likes (userID, movieID, date_liked) VALUES (?, ?, NOW())");
		}
		
		$stmt->execute([$currentUserID, $movieID]);

		// Επιστροφή στην ίδια σελίδα με το connector logic που ήδη έχουμε
		$connector = (strpos($redirect, '?') === false) ? '?' : '&';
		header("Location: " . $redirect . $connector . "success=liked_updated");
		exit;
	}

} catch (Exception $e) {
    error_log("Controller Error: " . $e->getMessage());
    die("Παρουσιάστηκε σφάλμα στη βάση δεδομένων.");
}