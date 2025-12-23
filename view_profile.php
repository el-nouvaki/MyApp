<?php 
/**
 * view_profile.php - Frontend
 */
require_once 'backend/get_profile.php'; 
include 'frontend/includes/header.php'; 
include 'frontend/includes/navbar.php'; 
?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-3">
            <div class="text-center p-4 bg-white shadow-sm rounded-4 border">
                <img src="<?= $targetData['avatar_url'] ?: './frontend/assets/images/noavatar.png' ?>" 
                     class="rounded-circle mb-3 border shadow-sm" 
                     style="width:140px; height:140px; object-fit:cover;">
                
                <h4 class="fw-bold mb-0 text-dark">
                    <?= htmlspecialchars($targetData['username']) ?>
                    <?php if($targetData['active'] == 0): ?>
                        <br><span class="badge bg-danger small mt-1" style="font-size: 0.6em;">BANNED</span>
                    <?php endif; ?>
                </h4>
                <p class="text-muted small mb-0"><?= $targetData['roleID'] == 1 ? 'Administrator' : 'Community Member' ?></p>
                
                <hr class="my-4">

                <?php if ($currentUserRole == 1): ?>
                    <div class="d-grid gap-2 mb-2">
                        <button class="btn btn-danger btn-sm rounded-pill py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#adminEditUserModal">
                            <i class="bi bi-shield-lock me-2"></i> Manage User
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($currentUserID > 0): ?>
                    <form action="backend/social_controller.php" method="POST">
                        <input type="hidden" name="action" value="toggle_follow">
                        <input type="hidden" name="followed_id" value="<?= $targetID ?>">
                        <input type="hidden" name="redirect_to" value="../view_profile.php?id=<?= $targetID ?>">
                        
                        <?php if ($isFollowing): ?>
                            <button type="submit" class="btn btn-secondary btn-sm rounded-pill w-100 py-2 fw-bold shadow-sm">
                                <i class="bi bi-person-check-fill me-1"></i> Following
                            </button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill w-100 py-2 fw-bold shadow-sm">
                                Follow User
                            </button>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-9 ps-md-5">
            <h2 class="fw-bold mb-4">
                <?= htmlspecialchars($targetData['username']) ?>'s Profile
                <?php if($targetData['active'] == 0): ?>
                    <span class="badge bg-danger ms-2" style="font-size: 0.5em; vertical-align: middle;">BANNED</span>
                <?php endif; ?>
            </h2>
            
            <div class="card border-0 bg-light p-4 rounded-4 mb-5 shadow-sm">
                <h6 class="text-muted text-uppercase small fw-bold mb-1">Favorite Genre</h6>
                <h5 class="fw-bold text-primary mb-0"><?= $targetData['favorite_genre_name'] ?: 'Not set' ?></h5>
            </div>

            <div class="row g-5">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-4 text-muted"><i class="bi bi-list-ul me-2"></i>Movie Lists</h5>
                    <?php if(empty($targetLists)): ?>
                        <p class="text-muted small italic">No public lists available.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush shadow-sm rounded-3 overflow-hidden">
                            <?php foreach($targetLists as $list): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($list['name']) ?></div>
                                        <?php if($list['private']): ?><span class="badge bg-warning text-dark" style="font-size: 0.6rem;">PRIVATE</span><?php endif; ?>
                                    </div>
                                    <a href="list_details.php?id=<?= $list['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill">View</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <h5 class="fw-bold mb-4 text-muted"><i class="bi bi-clock-history me-2"></i>Recent Activity</h5>
                    <?php if(empty($activities)): ?>
                        <p class="text-muted small italic">No activity recorded.</p>
                    <?php else: ?>
                        <div class="ps-3 border-start border-2 border-light">
                            <?php foreach($activities as $act): ?>
                                <div class="mb-4 position-relative">
                                    <div class="ms-2">
                                        <div class="small fw-bold">
                                            <?= htmlspecialchars($act['type']) ?>: 
                                            <span class="text-primary">"<?= htmlspecialchars($act['detail']) ?>"</span>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.7rem;"><?= date('d M Y, H:i', strtotime($act['activity_date'])) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($currentUserRole == 1): ?>
<div class="modal fade" id="adminEditUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="backend/profile_controller.php" method="POST" class="modal-content border-0 shadow-lg">
            <input type="hidden" name="action" value="admin_update_user">
            <input type="hidden" name="target_user_id" value="<?= $targetID ?>">
            <input type="hidden" name="redirect_to" value="../view_profile.php?id=<?= $targetID ?>">
            
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Manage User: <?= htmlspecialchars($targetData['username']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="small fw-bold">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($targetData['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($targetData['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">Account Status</label>
                    <select name="active" class="form-select border-<?= $targetData['active'] == 0 ? 'danger' : 'success' ?>">
                        <option value="1" <?= $targetData['active'] == 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= $targetData['active'] == 0 ? 'selected' : '' ?>>Banned</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">Role</label>
                    <select name="roleID" class="form-select">
                        <option value="2" <?= $targetData['roleID'] == 2 ? 'selected' : '' ?>>Member</option>
                        <option value="1" <?= $targetData['roleID'] == 1 ? 'selected' : '' ?>>Administrator</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">Favorite Genre</label>
                    <select name="genreID" class="form-select">
                        <?php foreach($genres as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= $g['id'] == $targetData['genreID'] ? 'selected' : '' ?>><?= htmlspecialchars($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold shadow">Update Info</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include 'frontend/includes/footer.php'; ?>