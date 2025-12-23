<?php
/**
 * frontend/includes/tabs/watchlist.php
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0 text-dark">My Watchlist</h4>
    <div class="d-flex gap-2">
      <!--  <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addMovieWatchlistModal">
            <i class="bi bi-plus-circle me-1"></i> Add Movie
        </button>
		-->
        <form action="backend/profile_controller.php" method="POST" onsubmit="return confirm('Clear entire watchlist?');">
            <input type="hidden" name="action" value="clear_watchlist">
            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">Clear Watchlist</button>
        </form>
    </div>
</div>

<div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-4">
    <?php if (empty($watchlist)): ?>
        <div class="col-12">
            <p class="text-muted ps-1 italic">Your watchlist is currently empty. Start adding movies!</p>
        </div>
    <?php else: ?>
        <?php foreach ($watchlist as $movie): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm position-relative rounded-3 overflow-hidden">
                    <a href="movie_details.php?id=<?= $movie['id'] ?>">
                        <img src="<?= $movie['poster_path'] ? 'https://image.tmdb.org/t/p/w300' . $movie['poster_path'] : 'https://placehold.co/300x450?text=No+Image' ?>" 
                             class="card-img-top" alt="Poster">
                    </a>
                    
                    <form action="backend/profile_controller.php" method="POST" class="position-absolute top-0 end-0 m-1">
                        <input type="hidden" name="action" value="delete_item">
                        <input type="hidden" name="type" value="watchlist">
                        <input type="hidden" name="item_id" value="<?= $movie['id'] ?>">
                        <button type="submit" class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center p-0 border-2 border-white shadow-lg" style="width: 34px; height: 34px; box-shadow: 0px 4px 8px rgba(0,0,0,0.5);"
						title="Remove from watchlist">
                            <span class="fw-bold fs-5" style="line-height: 1; padding-bottom: 2px;">X</span>
                        </button>
                    </form>
                    
                    <div class="card-body p-2 text-center bg-white">
                        <small class="fw-bold text-dark d-block text-truncate"><?= htmlspecialchars($movie['title']) ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>