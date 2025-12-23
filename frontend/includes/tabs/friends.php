<div class="row g-5">
    <div class="col-md-6 border-end">
        <h5 class="fw-bold mb-4 text-primary">
            <i class="bi bi-person-check me-2"></i>Following (<?= count($followingList) ?>)
        </h5>
        
        <?php if (empty($followingList)): ?>
            <p class="text-muted small italic">You are not following anyone yet.</p>
        <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($followingList as $f): ?>
                    <div class="card border-0 shadow-sm p-2 rounded-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="view_profile.php?id=<?= $f['id'] ?>" class="d-flex align-items-center text-decoration-none text-dark">
                                <img src="<?= $f['avatar_url'] ?: './frontend/assets/images/noavatar.png' ?>" 
                                     class="rounded-circle border" style="width: 50px; height: 50px; object-fit: cover;">
                                <div class="ms-3">
                                    <div class="fw-bold small"><?= htmlspecialchars($f['username']) ?></div>
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        <?= $f['roleID'] == 1 ? 'Admin' : 'Member' ?>
                                    </small>
                                </div>
                            </a>
                            <form action="backend/social_controller.php" method="POST">
                                <input type="hidden" name="action" value="toggle_follow">
                                <input type="hidden" name="followed_id" value="<?= $f['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" style="font-size: 0.7rem;">Unfollow</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <h5 class="fw-bold mb-4 text-success">
            <i class="bi bi-people me-2"></i>Followers (<?= count($followersList) ?>)
        </h5>
        
        <?php if (empty($followersList)): ?>
            <p class="text-muted small italic">No one is following you yet.</p>
        <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($followersList as $f): ?>
                    <div class="card border-0 shadow-sm p-2 rounded-4">
                        <div class="d-flex align-items-center">
                            <a href="view_profile.php?id=<?= $f['id'] ?>" class="d-flex align-items-center text-decoration-none text-dark flex-grow-1">
                                <img src="<?= $f['avatar_url'] ?: './frontend/assets/images/noavatar.png' ?>" 
                                     class="rounded-circle border" style="width: 50px; height: 50px; object-fit: cover;">
                                <div class="ms-3">
                                    <div class="fw-bold small"><?= htmlspecialchars($f['username']) ?></div>
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        <?= $f['roleID'] == 1 ? 'Admin' : 'Member' ?>
                                    </small>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>