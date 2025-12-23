<?php
/**
 * login.php
 * * * View Σελίδας Εισόδου.
 * Συνθέτει τη σελίδα χρησιμοποιώντας τα shared includes και τη φόρμα εισόδου.
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Αν είναι ήδη συνδεδεμένος, πάμε homepage
if (isset($_SESSION['user_id'])) {
    header("Location: homepage.php");
    exit;
}
include 'frontend/includes/header.php';
include 'frontend/includes/navbar.php';
?>

<div class="container mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-5 border-0 shadow-sm">
                <h2 class="text-center mb-5 fw-bold">LOGIN</h2>
                
                <?php if(isset($_GET['error'])): ?>
                    <?php if($_GET['error'] == 'invalid'): ?>
                        <div class="alert alert-danger text-center shadow-sm">Λάθος όνομα χρήστη ή κωδικός.</div>
                    <?php elseif($_GET['error'] == 'banned'): ?>
                        <div class="alert alert-dark text-center border-danger shadow-sm">
                            <i class="bi bi-exclamation-octagon-fill text-danger me-2"></i>
                            Ο λογαριασμός σας έχει απενεργοποιηθεί από τη διαχείριση.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form action="backend/auth.php" method="POST">
                    <input type="hidden" name="action" value="login">

                    <div class="mb-4">
                        <label class="form-label text-uppercase small text-muted">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-uppercase small text-muted">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="text-center mb-3">
                        <small>Don't have account yet? <a href="sign_up.php" class="text-decoration-none text-danger fw-bold">Register now!</a></small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary text-uppercase fw-bold" style="background-color: #2b7de9; border:none;">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'frontend/includes/footer.php'; ?>