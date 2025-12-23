<?php
/**
 * /backend/logout.php
 * * * Backend script για αποσύνδεση.
 * Καταστρέφει το session και ανακατευθύνει στο login.
 */
session_start();
session_unset();
session_destroy();

// Επιστροφή στο root/login.php
header("Location: ../login.php");
exit;
?>