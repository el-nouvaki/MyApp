<?php
require_once 'backend/get_reviews.php';

// ΑΛΛΑΓΗ: Χρήση της εικόνας από τα assets
$defaultAvatar = './frontend/assets/images/noavatar.png';
?>

<?php include 'frontend/includes/header.php'; ?>
<?php $activePage = 'reviews'; include 'frontend/includes/navbar.php'; ?>

<style>
    /* Ενιαίο στυλ για avatar (και το κανονικό και το placeholder) */
    .user-avatar-img {
        width: 40px; height: 40px; 
        border-radius: 50%; 
        object-fit: cover;
        border: 1px solid #dee2e6;
    }
</style>

<div class="container mt-5 mb-5">
    
    <h3 class="fw-bold mb-4 border-bottom pb-2">Last Reviewed Movies</h3>

    <?php if (count($latestReviews) > 0): ?>
        <div class="vstack gap-4">
            <?php foreach($latestReviews as $review): ?>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-md-2 col-3">
                                <a href="movie_details.php?id=<?php echo $review['movieID']; ?>">
                                    <img src="<?php echo $review['poster_path'] ? $poster_base_url . $review['poster_path'] : 'https://placehold.co/150?text=No+Image'; ?>" 
                                         class="img-fluid rounded-start h-100" 
                                         style="object-fit: cover; min-height: 180px;" 
                                         alt="Poster">
                                </a>
                            </div>

                            <div class="col-md-10 col-9">
                                <div class="p-3">
                                    
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-2">
                                            <?php if ($review['avatar_url']): ?>
                                                <img src="<?php echo htmlspecialchars($review['avatar_url']); ?>" class="user-avatar-img" alt="User">
                                            <?php else: ?>
                                                <img src="<?php echo $defaultAvatar; ?>" class="user-avatar-img" alt="No Avatar">
                                            <?php endif; ?>
                                        </div>
                                        <span class="fw-bold fs-5"><?php echo htmlspecialchars($review['username']); ?></span>
                                    </div>

                                    <h5 class="mb-1">
                                        <a href="movie_details.php?id=<?php echo $review['movieID']; ?>" class="text-decoration-none text-dark fw-bold">
                                            <?php echo htmlspecialchars($review['title']); ?>
                                        </a>
                                        <small class="text-muted ms-1"><?php echo date('Y', strtotime($review['release_date'])); ?></small>
                                    </h5>
                                    
                                    <div class="text-warning mb-2 small">
                                        <?php 
                                            for($i=0; $i<5; $i++) echo $i < $review['rating'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                                        ?>
                                    </div>

                                    <p class="text-secondary mb-2" style="font-size: 0.95rem;">
                                        <?php echo htmlspecialchars($review['review']); ?>
                                    </p>

                                    <div class="d-flex align-items-center">
                                        <?php if ($currentUserID): ?>
                                            <form action="backend/reviews_controller.php" method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="toggle_like">
                                                <input type="hidden" name="review_id" value="<?php echo $review['reviewID']; ?>">
                                                
                                                <button type="submit" class="btn btn-link p-0 text-decoration-none border-0 bg-transparent">
                                                    <?php if ($review['user_liked']): ?>
                                                        <i class="bi bi-heart-fill text-danger fs-5"></i> <span class="text-danger fw-bold ms-1">Liked review</span>
                                                    <?php else: ?>
                                                        <i class="bi bi-heart-fill text-muted fs-5"></i> <span class="text-dark ms-1">Like review</span>
                                                    <?php endif; ?>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <a href="login.php" class="text-decoration-none text-dark">
                                                <i class="bi bi-heart-fill text-muted fs-5"></i> Like review
                                            </a>
                                        <?php endif; ?>
                                        
                                        <span class="text-muted ms-3 small"><?php echo number_format($review['like_count']); ?> likes</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-light text-center">No reviews found yet.</div>
    <?php endif; ?>

</div>

<?php include 'frontend/includes/footer.php'; ?>