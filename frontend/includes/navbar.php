<?php
// Ασφαλής εκκίνηση session
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Ορισμός μεταβλητών
$userID = $_SESSION['user_id'] ?? null;
if (!isset($activePage)) { $activePage = ''; }
?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
  <div class="container-fluid px-4">
    
    <a class="navbar-brand" href="homepage.php">
        <img src="frontend/assets/images/logo.png" alt="MOVIE MANIACS Logo">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      
      <?php if ($userID): ?>
          <ul class="navbar-nav me-4 mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link position-relative <?php echo ($activePage == 'profile') ? 'active' : ''; ?>" href="profile.php">
                    Profile <i class="bi bi-person-circle ms-1"></i>
                    <span class="username-label">
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>
                </a>
            </li>
          </ul>
      <?php endif; ?>

      <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-bold">
        <li class="nav-item px-2">
            <a class="nav-link <?php echo ($activePage == 'movies') ? 'active' : ''; ?>" href="movies.php">Movies</a>
        </li>
        <li class="nav-item px-2">
            <a class="nav-link <?php echo ($activePage == 'lists') ? 'active' : ''; ?>" href="lists.php">Lists</a>
        </li>
       <li class="nav-item px-2">
			<a class="nav-link <?php echo ($activePage == 'members') ? 'active' : ''; ?>" href="members.php">Members</a>
		</li>
        <li class="nav-item px-2">
            <a class="nav-link <?php echo ($activePage == 'reviews') ? 'active' : ''; ?>" href="reviews.php">Reviews</a>
        </li>
      </ul>

      <?php if ($userID): ?>
          <a href="backend/logout.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">Exit</a>
      <?php else: ?>
          <div class="d-flex">
              <a href="login.php" class="btn btn-outline-light btn-sm rounded-pill px-3 me-2">Login</a>
              <a href="sign_up.php" class="btn btn-primary btn-sm rounded-pill px-3">Sign Up</a>
          </div>
      <?php endif; ?>

    </div>
  </div>
</nav>

<style>
    /* Διόρθωση: min-height αντί για height για να μην κόβεται το μενού στα κινητά */
    .navbar-custom { 
        background-color: #000000; 
        min-height: 80px; 
        padding: 10px 0;
		position: relative; 
        z-index: 1050; /* Τιμή που εξασφαλίζει ότι θα είναι πάνω από κάρτες και σκιές */
    }
    
    .navbar-brand img { height: 60px; }
    
    .nav-link {
        color: #fff !important; font-size: 1.1rem; font-weight: 500;
        padding: 8px 18px !important; margin: 0 5px; border-radius: 20px; transition: all 0.3s ease;
    }
    
    .nav-link:hover {
        background-color: #b91d2b; box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3); transform: translateY(-2px);
    }
    
    .nav-link.active { color: #dc3545 !important; font-weight: bold; }

    /* Ετικέτα ονόματος χρήστη */
    .username-label {
        font-size: 0.7rem;
        color: #ccc;
        display: block;
        text-align: center;
    }

    /* Ρυθμίσεις για μεγάλες οθόνες */
    @media (min-width: 992px) {
        .username-label {
            position: absolute;
            bottom: -14px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            white-space: nowrap;
        }
    }

    /* Ρυθμίσεις για κινητά (Responsive) */
    @media (max-width: 991px) {
        .navbar-collapse {
            background-color: #000;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
        }
        .nav-link { margin: 5px 0; text-align: center; }
        .username-label { color: #dc3545; margin-bottom: 10px; }
    }
</style>