<?php
/**
 * /backend/auth.php
 * * * Controller Αυθεντικοποίησης.
 * Διαχειρίζεται την Εγγραφή (Register) και τη Σύνδεση (Login).
 */

session_start();
require '../config/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['action'])) {
        
        // --- LOGIC ΕΓΓΡΑΦΗΣ (REGISTER) ---
        if ($_POST['action'] === 'register') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $genreID = $_POST['genreID'];

            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            
            if ($stmt->rowCount() > 0) {
                header("Location: ../sign_up.php?error=exists"); 
                exit;
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                // Εγγραφή νέου χρήστη ως Active (1) και Role Member (2)
                $sql = "INSERT INTO users (username, email, password, genreID, roleID, active) VALUES (?, ?, ?, ?, 2, 1)";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$username, $email, $hashed_password, $genreID])) {
                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    $_SESSION['username'] = $username;
                    $_SESSION['roleID'] = 2; // Ορίζουμε το roleID για το session
                    header("Location: ../homepage.php");
                    exit;
                } else {
                    header("Location: ../sign_up.php?error=general");
                    exit;
                }
            }
        }
        
        // --- LOGIC ΣΥΝΔΕΣΗΣ (LOGIN) ---
        elseif ($_POST['action'] === 'login') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            // Ανάκτηση χρήστη μαζί με roleID και active status
            $stmt = $pdo->prepare("SELECT id, username, password, roleID, active FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                // Έλεγχος κωδικού (Hash και Legacy)
                $isPasswordCorrect = password_verify($password, $user['password']);

                if (!$isPasswordCorrect && $password === $user['password']) {
                    $isPasswordCorrect = true;
                }

                if ($isPasswordCorrect) {
                    
                    // 1. ΕΛΕΓΧΟΣ BAN: Αν ο χρήστης είναι ανενεργός, διακοπή
                    if ((int)$user['active'] === 0) {
                        header("Location: ../login.php?error=banned");
                        exit;
                    }

                    // 2. ΕΠΙΤΥΧΗΣ ΣΥΝΔΕΣΗ: Αποθήκευση στοιχείων στο Session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['roleID'] = $user['roleID']; // Απαραίτητο για το get_members.php

                    header("Location: ../homepage.php");
                    exit;
                } else {
                    // Λάθος κωδικός
                    header("Location: ../login.php?error=invalid");
                    exit;
                }
            } else {
                // Δεν βρέθηκε ο χρήστης
                header("Location: ../login.php?error=invalid");
                exit;
            }
        }
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>