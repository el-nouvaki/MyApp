<?php
/**
 * sign_up.php
 * * * View Σελίδας Εγγραφής.
 * Χρησιμοποιεί το config/db.php για να φορτώσει τα Genres και στέλνει δεδομένα στο backend.
 */
	// Φόρτωση λογικής
	require_once 'backend/get_signup.php';
	
	include 'frontend/includes/header.php';

	include 'frontend/includes/navbar.php';
?>

<div class="container mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 border-0 shadow-sm">
                <h3 class="text-center mb-4">Create New Account</h3>
                <p class="text-center">Already Registered? <a href="login.php" class="text-decoration-none text-danger fw-bold">Login</a></p>
                
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                        if($_GET['error'] == 'exists') echo "Το Username ή το Email χρησιμοποιείται ήδη.";
                        else echo "Προέκυψε σφάλμα κατά την εγγραφή.";
                        ?>
                    </div>
                <?php endif; ?>

                <form action="backend/auth.php" method="POST">
                    <input type="hidden" name="action" value="register">

                    <div class="mb-3">
                        <label class="form-label text-uppercase small text-muted">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-uppercase small text-muted">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-uppercase small text-muted">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-uppercase small text-muted">Favorite Genre</label>
                        <select name="genreID" class="form-select" required>
                            <option value="" selected disabled>Select Genre</option>
                            <?php foreach($genres as $genre): ?>
                                <option value="<?php echo $genre['id']; ?>"><?php echo htmlspecialchars($genre['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary text-uppercase" style="background-color: #2b7de9; border:none;">Sign Up</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'frontend/includes/footer.php'; ?>