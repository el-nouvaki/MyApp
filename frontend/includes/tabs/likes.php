<?php 
/**
 * frontend/includes/tabs/likes.php
 */
// Φορτώνουμε το backend logic πριν την εμφάνιση
require_once 'backend/get_user_likes.php'; 
?>

<div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4 mt-2">
    <?php if (empty($likedMovies)): ?>
        <div class="col-12 text-center py-5">
            <i class="bi bi-heart text-light display-1"></i>
            <p class="text-muted mt-3">No favorite movies yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($likedMovies as $movie): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm movie-card overflow-hidden rounded-3">
                    <a href="movie_details.php?id=<?= $movie['id'] ?>">
                        <img src="https://image.tmdb.org/t/p/w300<?= $movie['poster_path'] ?>" 
                             class="card-img-top" alt="Poster" style="object-fit: cover;">
                    </a>
                    <div class="card-body p-2 text-center">
                        <h6 class="card-title small fw-bold mb-0 text-truncate"><?= htmlspecialchars($movie['title']) ?></h6>
                        <small class="text-muted"><?= substr($movie['release_date'] ?? '', 0, 4) ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>