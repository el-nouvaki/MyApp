<?php
/**
 * list_details.php - Frontend προβολή λίστας
 */
require_once 'backend/get_list_details.php'; 

include 'frontend/includes/header.php';
include 'frontend/includes/navbar.php';
?>

<div class="container mt-5 mb-5">
    <div class="row align-items-center mb-5 p-4 bg-white shadow-sm rounded-4 border-start border-4 border-danger">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3 mb-1">
                <h1 class="fw-bold mb-0"><?= htmlspecialchars($listInfo['name']) ?></h1>
                <?php if ($currentUserID == $listInfo['userID']): ?>
                  <div class="d-flex gap-2 align-items-center">                          
                    <button class="btn btn-outline-secondary btn-sm border-0 rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#editListNameModal">
                        <i class="bi bi-pencil-fill"></i>
                    </button> <form action="backend/profile_controller.php" method="POST" onsubmit="return confirm('Είστε σίγουροι ότι θέλετε να διαγράψετε ολόκληρη τη λίστα;');">
                        <input type="hidden" name="action" value="delete_item">
                        <input type="hidden" name="type" value="userlist">
                        <input type="hidden" name="item_id" value="<?= $listID ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm border-0 rounded-circle shadow-sm">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </form>
                  </div>
                <?php endif; ?>
            </div>
            
            <p class="text-muted mb-0 d-flex align-items-center flex-wrap gap-3">
                <span>Created by <a href="view_profile.php?id=<?= $listInfo['userID'] ?>" class="text-decoration-none text-dark fw-bold"><?= htmlspecialchars($listInfo['username']) ?></a></span>
                <span><i class="bi bi-film text-danger me-1"></i> <?= count($movies) ?> movies</span>
                
                <button class="btn btn-link p-0 text-decoration-none d-flex align-items-center like-btn" data-id="<?= $listID ?>">
					<i class="bi <?= ($listInfo['user_liked'] ?? false) ? 'bi-heart-fill text-danger' : 'bi-heart' ?> me-1"></i> 
					<span class="text-dark small fw-bold count"><?= $listInfo['likes_count'] ?? 0 ?></span>
				</button>
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <?php if ($currentUserID == $listInfo['userID']): ?>
                <span class="badge <?= $listInfo['private'] ? 'bg-warning text-dark' : 'bg-success' ?> p-2 rounded-pill shadow-sm">
                    <?= $listInfo['private'] ? '<i class="bi bi-lock-fill"></i> Private' : '<i class="bi bi-globe"></i> Public' ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4">
        <?php if (empty($movies)): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-film display-1 text-light"></i>
                <p class="text-muted mt-3">Αυτή η λίστα δεν έχει ακόμα ταινίες.</p>
            </div>
        <?php else: ?>
            <?php foreach ($movies as $movie): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm movie-card position-relative overflow-hidden rounded-3">
                        <a href="movie_details.php?id=<?= $movie['id'] ?>">
                            <img src="<?= $movie['poster_path'] ? $poster_base_url . $movie['poster_path'] : './frontend/assets/images/no-poster.png' ?>" 
                                 class="card-img-top" alt="Poster">
                        </a>
                        
                        <?php if ($currentUserID == $listInfo['userID']): ?>
                            <form action="backend/list_items_controller.php" method="POST" class="position-absolute top-0 end-0 m-2">
                                <input type="hidden" name="action" value="remove_item">
                                <input type="hidden" name="list_id" value="<?= $listID ?>">
                                <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                                <button type="submit" 
                                        class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center p-0 border-2 border-white shadow-lg" 
                                        style="width: 34px; height: 34px; box-shadow: 0px 4px 8px rgba(0,0,0,0.5);"
                                        onclick="return confirm('Αφαίρεση από τη λίστα;');">
                                    <span class="fw-bold fs-5" style="line-height: 1; padding-bottom: 2px;">X</span>
                                </button>
                            </form>
                        <?php endif; ?>

                        <div class="card-body p-2 text-center">
                            <h6 class="card-title small fw-bold mb-0 text-truncate"><?= htmlspecialchars($movie['title']) ?></h6>
                            <small class="text-muted"><?= substr($movie['release_date'], 0, 4) ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="editListNameModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="backend/profile_controller.php" method="POST" class="modal-content border-0 shadow-lg">
            <input type="hidden" name="action" value="rename_list">
            <input type="hidden" name="list_id" value="<?= $listID ?>">
            <div class="modal-header border-0"><h5 class="fw-bold">Rename Movie List</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4">
                <label class="small fw-bold mb-1">New Name</label>
                <input type="text" name="new_name" class="form-control rounded-3" value="<?= htmlspecialchars($listInfo['name']) ?>" required>
            </div>
            <div class="modal-footer border-0"><button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">Update Name</button></div>
        </form>
    </div>
</div>

<script src="frontend/assets/js/likes.js"></script>
<?php include 'frontend/includes/footer.php'; ?>