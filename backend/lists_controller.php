<?php
/**
 * /backend/lists_controller.php
 * * * Controller Διαχείρισης Λιστών.
 * Διαχειρίζεται τη δημιουργία, διαγραφή και επεξεργασία λιστών χρηστών.
 */

session_start();
require '../config/db.php';

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- CREATE NEW LIST ---
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        
        $userID = $_SESSION['user_id'];
        $name = trim($_POST['name']);
        
        // Έλεγχος για το checkbox 'Private'. Αν είναι τσεκαρισμένο επιστρέφει 'on', αλλιώς τίποτα.
        // Στη βάση το private είναι 1 (true) ή 0 (false).
        $is_private = isset($_POST['is_private']) ? 1 : 0;

        // Validation: Το όνομα δεν πρέπει να είναι κενό
        if (empty($name)) {
            header("Location: ../profile.php?error=empty_name");
            exit;
        }

        try {
            $sql = "INSERT INTO userlists (userID, name, private) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$userID, $name, $is_private])) {
                // Επιτυχία: Επιστροφή στο προφίλ με μήνυμα επιτυχίας
                header("Location: ../profile.php?msg=list_created");
                exit;
            } else {
                header("Location: ../profile.php?error=db_error");
                exit;
            }
        } catch (Exception $e) {
            // Καταγραφή σφάλματος (σε πραγματική εφαρμογή σε log file)
            header("Location: ../profile.php?error=exception");
            exit;
        }
    }
	// --- ADD MOVIE TO LIST ---
    elseif (isset($_POST['action']) && $_POST['action'] === 'add_movie') {
        $listID = $_POST['list_id'];
        $movieID = $_POST['movie_id'];
        
        // Έλεγχος αν η ταινία υπάρχει ήδη στη λίστα
        $check = $pdo->prepare("SELECT * FROM userlists_items WHERE ulID = ? AND movieID = ?");
        $check->execute([$listID, $movieID]);
        
        if ($check->rowCount() == 0) {
            $sql = "INSERT INTO userlists_items (ulID, movieID) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$listID, $movieID]);
            $msg = "added";
        } else {
            $msg = "exists";
        }
        
        header("Location: ../movie_details.php?id=$movieID&msg=$msg");
        exit;
    }

    // --- TOGGLE LIKE (FAVORITE) ---
    elseif (isset($_POST['action']) && $_POST['action'] === 'toggle_like') {
        $userID = $_SESSION['user_id'];
        $movieID = $_POST['movie_id'];

        // Έλεγχος αν υπάρχει ήδη like
        $check = $pdo->prepare("SELECT * FROM movie_likes WHERE userID = ? AND movieID = ?");
        $check->execute([$userID, $movieID]);

        if ($check->rowCount() > 0) {
            // Αν υπάρχει, κάνουμε DELETE (Un-like)
            $sql = "DELETE FROM movie_likes WHERE userID = ? AND movieID = ?";
            $pdo->prepare($sql)->execute([$userID, $movieID]);
        } else {
            // Αν δεν υπάρχει, κάνουμε INSERT (Like)
            $sql = "INSERT INTO movie_likes (userID, movieID) VALUES (?, ?)";
            $pdo->prepare($sql)->execute([$userID, $movieID]);
        }
        
        header("Location: ../movie_details.php?id=$movieID");
        exit;
    }
	
	// --- TOGGLE WATCHLIST (ΝΕΟ) ---
    elseif (isset($_POST['action']) && $_POST['action'] === 'toggle_watchlist') {
        $userID = $_SESSION['user_id'];
        $movieID = $_POST['movie_id'];

        // Έλεγχος αν υπάρχει ήδη
        $check = $pdo->prepare("SELECT * FROM watchlist_items WHERE userID = ? AND movieID = ?");
        $check->execute([$userID, $movieID]);

        if ($check->rowCount() > 0) {
            // Αν υπάρχει -> Διαγραφή (Remove)
            $sql = "DELETE FROM watchlist_items WHERE userID = ? AND movieID = ?";
            $pdo->prepare($sql)->execute([$userID, $movieID]);
            $msg = "watchlist_removed";
        } else {
            // Αν δεν υπάρχει -> Εισαγωγή (Add)
            // watched = 0 (σημαίνει "δεν την έχω δει ακόμα")
            $sql = "INSERT INTO watchlist_items (userID, movieID, watched) VALUES (?, ?, 0)";
            $pdo->prepare($sql)->execute([$userID, $movieID]);
            $msg = "watchlist_added";
        }
        
        header("Location: ../movie_details.php?id=$movieID&msg=$msg");
        exit;
    }
	
	// --- RENAME LIST (ΝΕΟ) ---
	elseif (isset($_POST['action']) && $_POST['action'] === 'rename_list') {
		$userID = $_SESSION['user_id'];
		$listID = $_POST['list_id'];
		$newName = trim($_POST['new_name']);

		if (!empty($newName)) {
			// Ελέγχουμε αν η λίστα ανήκει όντως στον χρήστη για ασφάλεια
			$stmt = $pdo->prepare("UPDATE userlists SET name = ? WHERE id = ? AND userID = ?");
			$stmt->execute([$newName, $listID, $userID]);
		}
		header("Location: ../lists.php");
		exit;
	}
	
	// --- DELETE LIST ---
    elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $listID = $_POST['list_id'];
        $userID = $_SESSION['user_id'];

        // 1. Security Check: Η λίστα ανήκει όντως στον χρήστη;
        $check = $pdo->prepare("SELECT id FROM userlists WHERE id = ? AND userID = ?");
        $check->execute([$listID, $userID]);

        if ($check->rowCount() > 0) {
            // 2. Διαγραφή (Λόγω του ON DELETE CASCADE στη βάση, θα διαγραφούν και τα items αυτόματα)
            $stmt = $pdo->prepare("DELETE FROM userlists WHERE id = ?");
            $stmt->execute([$listID]);
            
            // Επιστροφή στο προφίλ
            header("Location: ../profile.php?msg=list_deleted");
            exit;
        } else {
            // Προσπάθεια διαγραφής ξένης λίστας
            header("Location: ../profile.php?error=unauthorized");
            exit;
        }
    }
} else {
    // Αν κάποιος προσπαθήσει να μπει απευθείας στο αρχείο
    header("Location: ../homepage.php");
    exit;
}
?>