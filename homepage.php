<?php
/**
 * homepage.php
 * * * Κεντρική σελίδα (Dashboard).
 * Ενημερώθηκε ώστε να περιέχει σωστούς συνδέσμους προς Movies και Profile,
 * καθώς και "Quick Actions" στο κυρίως μέρος της σελίδας.
 */
// Απλά ξεκινάμε το session αν δεν έχει ξεκινήσει
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Ορίζουμε το $userID ως null αν δεν είναι συνδεδεμένος
$userID = $_SESSION['user_id'] ?? null;
// ----------------------------------------

require 'config/db.php';
?>

<?php include 'frontend/includes/header.php'; ?>

<?php 
    $activePage = 'home'; include 'frontend/includes/navbar.php'; 
?>

<div class="container mt-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <?php if ($userID): ?>
                <h1 class="display-4 fw-bold">Welcome back, <span class="text-danger"><?php echo htmlspecialchars($_SESSION['username']); ?></span></h1>
                <p class="text-muted lead">What would you like to do today?</p>
            <?php else: ?>
                <h1 class="display-4 fw-bold">Welcome to <span class="text-danger">MOVIE MANIACS</span></h1>
                <p class="text-muted lead">Discover movies, create lists, and share your passion.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row justify-content-center g-4">
        
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-5">
                    <i class="bi bi-film text-danger display-1 mb-3"></i>
                    <h3 class="card-title fw-bold">Browse Movies</h3>
                    <p class="card-text text-muted">Discover new releases, find top rated movies and read reviews.</p>
                    <a href="movies.php" class="btn btn-dark rounded-pill px-4 mt-3">Go to Movies</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm hover-card">
                <div class="card-body text-center p-5">
                    <i class="bi bi-list-check text-danger display-1 mb-3"></i>
                    
                    <?php if ($userID): ?>
                        <h3 class="card-title fw-bold">My Lists</h3>
                        <p class="card-text text-muted">Organize your watchlist, create custom collections and track what you've seen.</p>
                        <a href="profile.php" class="btn btn-dark rounded-pill px-4 mt-3">Go to Profile</a>
                    <?php else: ?>
                        <h3 class="card-title fw-bold">Join the Community</h3>
                        <p class="card-text text-muted">Sign up to create your own lists, rate movies and write reviews.</p>
                        <a href="sign_up.php" class="btn btn-primary rounded-pill px-4 mt-3">Sign Up Now</a>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
</style>

<?php include 'frontend/includes/footer.php'; ?>