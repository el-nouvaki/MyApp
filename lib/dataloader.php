<?php
/**
 * lib/DataLoader.php
 * Κεντρική διαχείριση δεδομένων με Memoization για βέλτιστη απόδοση.
 */

class DataLoader {
    private $pdo;
    
    // Εδώ αποθηκεύουμε προσωρινά τα δεδομένα για να μην τα ξαναζητάμε
    private $cachedGenres = null;
    private $cachedUsersList = null;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Επιστρέφει τα Genres. 
     * Αν έχουν ήδη ζητηθεί σε αυτό το request, τα επιστρέφει από τη μνήμη.
     */
    public function getGenres() {
        if ($this->cachedGenres === null) {
            // Μόνο αν είναι κενό κάνουμε το query
            try {
                $stmt = $this->pdo->query("SELECT id, name FROM genres ORDER BY name ASC");
                $this->cachedGenres = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log("Error fetching genres: " . $e->getMessage());
                $this->cachedGenres = [];
            }
        }
        return $this->cachedGenres;
    }

    /**
     * Επιστρέφει τη λίστα χρηστών για το Admin Panel.
     * Το query τρέχει ΜΟΝΟ αν κληθεί η συνάρτηση (δηλαδή μόνο αν είσαι Admin).
     */
    public function getAllUsersForAdmin($currentUserID) {
        if ($this->cachedUsersList === null) {
            try {
                $sql = "SELECT id, username, email, roleID, active FROM users WHERE id != ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$currentUserID]);
                $this->cachedUsersList = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log("Error fetching users list: " . $e->getMessage());
                $this->cachedUsersList = [];
            }
        }
        return $this->cachedUsersList;
    }

    /**
     * Βασικά στοιχεία χρήστη (για profile & view_profile)
     */
    public function getUserData($userID) {
        try {
            $stmt = $this->pdo->prepare("SELECT u.*, g.name as favorite_genre_name FROM users u LEFT JOIN genres g ON u.genreID = g.id WHERE u.id = ?");
            $stmt->execute([$userID]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
}
?>