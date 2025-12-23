<?php
/**
 * component: list_card.php
 * Χρησιμοποιεί το frontend/assets/css/list_cards.css
 */
$noPoster = "./frontend/assets/images/no-poster.png";
$noAvatar = "./frontend/assets/images/noavatar.png";
?>

<div class="list-card-horizontal">
    <a href="list_details.php?id=<?= $list['id'] ?>" class="collage-link-wrapper">
        <div class="collage-accordion">
            <?php if (!empty($list['posters'])): ?>
                <?php foreach ($list['posters'] as $p_path): ?>
                    <?php $p_url = $p_path ? "https://image.tmdb.org/t/p/w200" . $p_path : $noPoster; ?>
                    <div class="accordion-item-img">
                        <img src="<?= $p_url ?>" alt="Movie Poster">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="w-100 d-flex align-items-center justify-content-center text-white-50 small italic">No films yet</div>
            <?php endif; ?>
        </div>
    </a>

    <div class="list-info-content">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="fw-bold mb-1">
                    <a href="list_details.php?id=<?= $list['id'] ?>" class="text-decoration-none text-dark">
                        <?= htmlspecialchars($list['name']) ?>
                    </a>
                    <?php if ($currentUserID == $list['userID']): ?>
                        <i class="bi bi-pencil-fill ms-2 small text-muted" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#editListModal_<?= $list['id'] ?>"></i>
                    <?php endif; ?>
                </h4>
                
                <a href="view_profile.php?id=<?= $list['userID'] ?>" class="creator-info-link mt-2">
                    <img src="<?= $list['avatar_url'] ?: $noAvatar ?>" class="creator-avatar-small">
                    <span class="text-muted small">by <strong><?= htmlspecialchars($list['username']) ?></strong></span>
                </a>
            </div>

            <?php if ($currentUserID == $list['userID']): ?>
                <form action="backend/profile_controller.php" method="POST" onsubmit="return confirm('Delete list?');">
                    <input type="hidden" name="action" value="delete_item">
                    <input type="hidden" name="type" value="userlist">
                    <input type="hidden" name="item_id" value="<?= $list['id'] ?>">
                    
                    <input type="hidden" name="redirect_to" value="<?= basename($_SERVER['PHP_SELF']) . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '') ?>">
                    
                    <button type="submit" class="btn btn-link text-danger p-0 border-0"><i class="bi bi-trash"></i></button>
                </form>
            <?php endif; ?>
        </div>

        <p class="text-muted small my-2" style="font-style: italic;">No description provided for this collection.</p>

        <div class="d-flex justify-content-between align-items-center">
            <div class="list-stats-row">
				<span class="small text-muted fw-bold"><i class="bi bi-film me-1"></i> <?= $list['movie_count'] ?? 0 ?> movies</span>
				
				<button class="btn btn-link p-0 text-decoration-none like-btn" data-id="<?= $list['id'] ?>">
					<i class="bi <?= ($list['user_liked'] ?? false) ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
					<span class="count small text-dark ms-1"><?= $list['likes_count'] ?? 0 ?></span>
				</button>
			</div>
            <a href="list_details.php?id=<?= $list['id'] ?>" class="btn btn-sm btn-dark rounded-pill px-4 fw-bold">View Details</a>
        </div>
    </div>
</div>

<?php if ($currentUserID == $list['userID']): ?>
<div class="modal fade" id="editListModal_<?= $list['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="backend/profile_controller.php" method="POST" class="modal-content border-0 shadow-lg">
            <input type="hidden" name="action" value="rename_list">
            <input type="hidden" name="list_id" value="<?= $list['id'] ?>">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Rename List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <label class="small fw-bold mb-1">New List Name</label>
                <input type="text" name="new_name" class="form-control rounded-3" value="<?= htmlspecialchars($list['name']) ?>" required>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold py-2">Update Name</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>