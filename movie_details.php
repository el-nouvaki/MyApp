<?php
/**
 * movie_details.php - Πλήρης προβολή ταινίας με Like Button
 */
require_once 'backend/get_movie_details.php';

// ΑΛΛΑΓΗ: Χρήση της εικόνας από τα assets
$defaultAvatar = './frontend/assets/images/noavatar.png';
?>

<?php include 'frontend/includes/header.php'; ?>
<?php $activePage = 'movies'; include 'frontend/includes/navbar.php'; ?>

<style>
    .btn-custom-action {
        background-color: #ff3b30; color: white; border: none; border-radius: 50px; padding: 12px 20px;
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; width: 100%; margin-bottom: 15px;
        transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(255, 59, 48, 0.2);
    }
    .btn-custom-action:hover { background-color: #d32f2f; transform: translateY(-2px); color: white; }
    
    .actor-img {
        width: 100%; height: 240px; 
        object-fit: cover; object-position: top center;
        border-top-left-radius: 4px; border-top-right-radius: 4px;
        background-color: #f0f0f0; 
    }
    
    .user-avatar-img {
        width: 50px; height: 50px; 
        border-radius: 50%; 
        object-fit: cover;
        border: 1px solid #dee2e6;
    }
</style>

<div class="container mt-5 mb-5">
    <div class="row">
        
        <div class="col-md-3">
            <img src="<?php echo $movie['poster_path'] ? $poster_base_url . $movie['poster_path'] : 'https://placehold.co/300x450?text=No+Image'; ?>" class="img-fluid rounded shadow-sm w-100 mb-4">
            
            <div class="d-flex flex-column">
                <?php if ($userID): ?>
                    <a href="#reviewFormSection" class="btn btn-custom-action text-decoration-none text-center">Write Review</a>
                    <form action="backend/lists_controller.php" method="POST">
                        <input type="hidden" name="action" value="toggle_watchlist">
                        <input type="hidden" name="movie_id" value="<?php echo $movieID; ?>">
                        <button type="submit" class="btn btn-custom-action">
                            <?php echo $inWatchlist ? '<i class="bi bi-check-lg"></i> In Watchlist' : 'Add to Watchlist'; ?>
                        </button>
                    </form>
                    <button class="btn btn-custom-action" data-bs-toggle="modal" data-bs-target="#addToListModal">Add to myList</button>
                <?php else: ?>
                    <a href="login.php" class="btn btn-custom-action text-center text-decoration-none">Write Review</a>
                    <a href="login.php" class="btn btn-custom-action text-center text-decoration-none">Add to Watchlist</a>
                    <a href="login.php" class="btn btn-custom-action text-center text-decoration-none">Add to myList</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-9 ps-md-5">
            
            <div class="d-flex align-items-center gap-3 mb-2">
                <h1 class="fw-bold display-5 mb-0"><?php echo htmlspecialchars($movie['title']); ?></h1>
                
                <?php if ($userID): ?>
                    <button class="btn btn-link p-0 border-0 shadow-none d-flex align-items-center movie-like-btn" data-movie-id="<?php echo $movieID; ?>" title="Like this movie">
						<i class="bi <?php echo $isLiked ? 'bi-heart-fill text-danger' : 'bi-heart text-muted'; ?> fs-2 me-2"></i>
						<span class="count text-dark fw-bold"><?php echo $movie['likes_count'] ?? 0; ?></span>
					</button>
                <?php endif; ?>
            </div>

            <div class="text-muted mb-3 fs-5">
                <?php echo date('Y', strtotime($movie['release_date'])); ?>
                <span class="mx-2 text-warning">
                    <?php $rating = round($movie['TMDB_vote_average'] / 2); for($i=1; $i<=5; $i++) echo $i <= $rating ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>'; ?>
                    <span class="text-dark fw-bold ms-1"><?php echo number_format($movie['TMDB_vote_average'], 1); ?></span>
                </span>
            </div>

            <h4 class="fw-bold border-bottom pb-2 mb-3 mt-4">Overview</h4>
            <p class="lead text-secondary" style="line-height: 1.7;"><?php echo htmlspecialchars($movie['overview']); ?></p>

            <h4 class="fw-bold border-bottom pb-2 mb-3 mt-5">Top Cast</h4>
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4">
                <?php foreach($cast as $actor): ?>
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm text-center">
                            <img src="<?php echo $actor['profile_path'] ? $poster_base_url . $actor['profile_path'] : $defaultAvatar; ?>" 
                                 class="actor-img" alt="<?php echo htmlspecialchars($actor['name']); ?>">
                            <div class="card-body p-2 d-flex flex-column justify-content-center">
                                <div class="fw-bold small text-truncate"><?php echo htmlspecialchars($actor['name']); ?></div>
                                <div class="text-muted small text-truncate"><?php echo htmlspecialchars($actor['character_name']); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-5">
                <h4 class="fw-bold mb-0 text-danger">Recent Reviews</h4>
                <form action="movie_details.php" method="GET" class="d-flex align-items-center">
                    <input type="hidden" name="id" value="<?php echo $movieID; ?>">
                    <label class="me-2 small text-muted">Show:</label>
                    <select name="limit" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                        <option value="5" <?php if(isset($_GET['limit']) && $_GET['limit'] == 5) echo 'selected'; ?>>5</option>
                        <option value="10" <?php if(isset($_GET['limit']) && $_GET['limit'] == 10) echo 'selected'; ?>>10</option>
                        <option value="20" <?php if(isset($_GET['limit']) && $_GET['limit'] == 20) echo 'selected'; ?>>20</option>
                    </select>
                </form>
            </div>
            
            <div class="card shadow-sm border-danger" style="border-top-width: 3px;">
                <div class="card-body p-4">
                    <?php if(count($reviews) > 0): ?>
                        <?php foreach($reviews as $index => $review): ?>
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    <img src="<?php echo $review['avatar_url'] ?: $defaultAvatar; ?>" class="user-avatar-img" alt="User">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div>
                                            <span class="text-muted me-1">Review by</span>
                                            <span class="fw-bold text-dark me-2"><?php echo htmlspecialchars($review['username']); ?></span>
                                            <?php if(isset($review['rating']) && $review['rating'] > 0): ?>
                                                <span class="text-warning small"><?php for($i=0; $i<$review['rating']; $i++) echo '<i class="bi bi-star-fill"></i>'; ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($review['date_created'])); ?></small>
                                    </div>
                                    <p class="text-secondary mb-1" style="line-height: 1.5;"><?php echo htmlspecialchars($review['review']); ?></p>
                                    <div class="d-flex align-items-center">
                                        <?php if ($userID): ?>
                                            <form action="backend/reviews_controller.php" method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="toggle_like">
                                                <input type="hidden" name="review_id" value="<?php echo $review['reviewID']; ?>">
                                                <button type="submit" class="btn btn-link p-0 text-decoration-none border-0 bg-transparent">
                                                    <?php if ($review['user_liked']): ?>
                                                        <i class="bi bi-heart-fill text-danger"></i> <span class="text-danger small fw-bold">Liked review</span>
                                                    <?php else: ?>
                                                        <i class="bi bi-heart-fill text-muted"></i> <span class="text-dark small">Like review</span>
                                                    <?php endif; ?>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <i class="bi bi-heart-fill text-muted"></i> <span class="text-muted small">Like review</span>
                                        <?php endif; ?>
                                        <span class="text-muted ms-3 small"><?php echo $review['like_count']; ?> likes</span>
                                    </div>
                                </div>
                            </div>
                            <?php if ($index < count($reviews) - 1): ?><hr class="my-4 text-muted opacity-25"><?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center mb-0">No reviews yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div id="reviewFormSection" class="card mt-5 shadow-sm border-2 border-danger">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-danger mb-3">Write a Comment</h5>
                    <?php if ($userID): ?>
                        <form action="backend/reviews_controller.php" method="POST">
                            <input type="hidden" name="action" value="submit_review">
                            <input type="hidden" name="movie_id" value="<?php echo $movieID; ?>">
                            <div class="mb-3">
                                <div class="rate" style="float: left;">
                                    <input type="radio" id="star5" name="rating" value="5" <?php echo ($myReview && $myReview['rating'] == 5) ? 'checked' : ''; ?> /><label for="star5" title="5 stars"></label>
                                    <input type="radio" id="star4" name="rating" value="4" <?php echo ($myReview && $myReview['rating'] == 4) ? 'checked' : ''; ?> /><label for="star4" title="4 stars"></label>
                                    <input type="radio" id="star3" name="rating" value="3" <?php echo ($myReview && $myReview['rating'] == 3) ? 'checked' : ''; ?> /><label for="star3" title="3 stars"></label>
                                    <input type="radio" id="star2" name="rating" value="2" <?php echo ($myReview && $myReview['rating'] == 2) ? 'checked' : ''; ?> /><label for="star2" title="2 stars"></label>
                                    <input type="radio" id="star1" name="rating" value="1" <?php echo ($myReview && $myReview['rating'] == 1) ? 'checked' : ''; ?> /><label for="star1" title="1 star"></label>
                                </div>
                                <div style="clear: both;"></div>
                            </div>
                            <textarea class="form-control mb-3" name="review" rows="3" placeholder="Write your comment here..." required><?php echo $myReview ? htmlspecialchars($myReview['review']) : ''; ?></textarea>
                            <div class="text-end"><button type="submit" class="btn btn-danger px-4 rounded-pill fw-bold">Post</button></div>
                        </form>
                    <?php else: ?>
                        <p class="text-muted">Please <a href="login.php" class="text-danger fw-bold">login</a> to write a comment.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div> 
    </div>
</div>

<div class="modal fade" id="addToListModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0"><h5 class="modal-title fw-bold text-danger">Add to myList</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <?php if ($userID && count($userLists) > 0): ?>
            <form action="backend/lists_controller.php" method="POST">
                <input type="hidden" name="action" value="add_movie">
                <input type="hidden" name="movie_id" value="<?php echo $movieID; ?>">
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold text-uppercase">Select List</label>
                    <select name="list_id" class="form-select" required>
                        <?php foreach($userLists as $list): ?>
                            <option value="<?php echo $list['id']; ?>"><?php echo htmlspecialchars($list['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="d-grid"><button type="submit" class="btn btn-danger rounded-pill fw-bold">Save</button></div>
            </form>
        <?php elseif ($userID): ?>
            <div class="text-center py-3"><p>No custom lists found.</p><a href="profile.php" class="btn btn-outline-danger btn-sm rounded-pill">Create List</a></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
      <div class="toast show align-items-center text-white <?php echo strpos($_GET['msg'], 'removed') !== false ? 'bg-secondary' : 'bg-success'; ?> border-0">
        <div class="d-flex"><div class="toast-body">
            <?php 
                if($_GET['msg'] == 'watchlist_added') echo 'Added to Watchlist!';
                elseif($_GET['msg'] == 'watchlist_removed') echo 'Removed from Watchlist.';
                elseif($_GET['msg'] == 'added') echo 'Added to your list!';
                elseif($_GET['msg'] == 'exists') echo 'Already in this list.';
                elseif($_GET['msg'] == 'review_saved') echo 'Review Posted!';
            ?>
        </div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-toast="dismiss"></button></div>
      </div>
    </div>
<?php endif; ?>

<script src="frontend/assets/js/likes.js"></script>

<?php include 'frontend/includes/footer.php'; ?>