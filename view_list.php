<?php
/**
 * view_list.php
 * * * Προβολή περιεχομένου λίστας.
 * Λειτουργικότητα:
 * 1. Ελέγχει δικαιώματα πρόσβασης (Public vs Private).
 * 2. Εμφανίζει τις ταινίες που έχουν προστεθεί στη συγκεκριμένη λίστα.
 * 3. Δίνει επιλογή διαγραφής λίστας αν είσαι ο κάτοχος.
 */
	// Φόρτωση λογικής
	require_once 'backend/get_view_list.php';

	include 'frontend/includes/header.php';
	
	$activePage = 'lists'; include 'frontend/includes/navbar.php';
?>

<div class="container pb-5">

    <div class="bg-light p-4 rounded-3 shadow-sm mb-4 border-start border-5 border-danger position-relative">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <span class="badge <?php echo $list['private'] ? 'bg-dark' : 'bg-success'; ?> mb-2">
                    <?php echo $list['private'] ? '<i class="bi bi-lock-fill"></i> Private List' : '<i class="bi bi-globe"></i> Public List'; ?>
                </span>
                <h1 class="fw-bold display-5 mb-1"><?php echo htmlspecialchars($list['name']); ?></h1>
                <p class="text-muted mb-0">
                    Created by <strong class="text-dark"><?php echo htmlspecialchars($list['username']); ?></strong> 
                    &bull; <?php echo count($movies); ?> movies
                </p>
            </div>
            
            <?php if ($currentUserID == $list['userID']): ?>
                <form action="backend/lists_controller.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this list?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="list_id" value="<?php echo $list['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash"></i> Delete List
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if (count($movies) > 0): ?>
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4">
            <?php foreach ($movies as $movie): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm hover-card">
                        <div style="position: relative; overflow: hidden; border-radius: 5px;">
                            <a href="movie_details.php?id=<?php echo $movie['id']; ?>">
                                <img src="<?php echo $movie['poster_path'] ? $poster_base_url . $movie['poster_path'] : 'https://placehold.co/300x450?text=No+Image'; ?>" 
                                     class="card-img-top" alt="Poster"
                                     style="height: 300px; object-fit: cover;">
                            </a>
                        </div>
                        
                        <div class="card-body p-2">
                            <h6 class="card-title fw-bold text-truncate mb-1">
                                <a href="movie_details.php?id=<?php echo $movie['id']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($movie['title']); ?>
                                </a>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center small text-muted">
                                <span><?php echo date('Y', strtotime($movie['release_date'])); ?></span>
                                <span class="text-warning fw-bold">
                                    <i class="bi bi-star-fill"></i> <?php echo number_format($movie['TMDB_vote_average'], 1); ?>
                                </span>
                            </div>
                            <div class="text-end mt-2">
                                <small class="text-muted" style="font-size: 0.75rem;">Added: <?php echo date('d/m/y', strtotime($movie['date_added'])); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="bi bi-film text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
            <h4 class="text-muted">This list is empty.</h4>
            <?php if ($currentUserID == $list['userID']): ?>
                <p>Go to Movies and start adding your favorites!</p>
                <a href="movies.php" class="btn btn-primary rounded-pill">Browse Movies</a>
            <?php else: ?>
                <p>The user hasn't added any movies yet.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<?php include 'frontend/includes/footer.php'; ?>