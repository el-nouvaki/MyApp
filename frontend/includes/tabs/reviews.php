<?php 
/**
 * frontend/includes/tabs/reviews.php
 */
require_once 'backend/get_user_reviews.php'; 
$poster_base_url = "https://image.tmdb.org/t/p/w154/"; 
?>

<div class="reviews-list mt-3">
    <?php if (empty($reviews)): ?>
        <div class="text-center py-5">
            <i class="bi bi-chat-left-text text-light display-1"></i>
            <p class="text-muted mt-3">No reviews posted yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden member-row">
                <div class="d-flex align-items-start p-3">
                    
                    <div class="flex-shrink-0 me-3">
                        <a href="movie_details.php?id=<?= $review['movieID'] ?>">
                            <img src="<?= $review['poster_path'] ? $poster_base_url . $review['poster_path'] : 'https://placehold.co/80x120?text=No+Img'; ?>" 
                                 class="rounded shadow-sm" 
                                 style="width: 80px; height: 120px; object-fit: cover;" 
                                 alt="Poster">
                        </a>
                    </div>

                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold mb-0">
                                    <a href="movie_details.php?id=<?= $review['movieID'] ?>" class="text-dark text-decoration-none hover-danger">
                                        <?= htmlspecialchars($review['title']) ?>
                                    </a>
                                </h6>
                                <?php if ($review['movie_liked']): ?>
                                    <i class="bi bi-heart-fill text-danger small"></i>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted small"><?= date('d/m/Y', strtotime($review['date_created'])) ?></small>
                        </div>
                        
                        <?php if ($review['user_rating']): ?>
                            <div class="text-warning mb-2 small">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="bi <?= $i <= $review['user_rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>

                        <p class="text-secondary mb-2" style="font-size: 0.95rem; line-height: 1.5; font-style: italic;">
                            "<?= htmlspecialchars($review['review']) ?>"
                        </p>

                        <div class="mt-2 pt-2 border-top">
                            <small class="text-muted">
                                <i class="bi bi-hand-thumbs-up-fill me-1"></i>
                                Liked by <span class="fw-bold text-dark"><?= $review['review_like_count'] ?></span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
    .member-row { transition: all 0.2s ease; }
    .member-row:hover { background-color: #fcfcfc !important; transform: translateX(5px); }
    .hover-danger:hover { color: #dc3545 !important; }
</style>