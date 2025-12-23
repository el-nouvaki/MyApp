<?php
/**
 * frontend/includes/profile_header.php
 * Το οριζόντιο πλαίσιο με το Avatar, το όνομα και τα στατιστικά.
 */
?>

<div class="card border-danger mb-4 rounded-3 shadow-sm bg-white">
    <div class="card-body p-4">
        <div class="row align-items-center">
            
            <div class="col-md-3 text-center border-end">
                <div class="position-relative d-inline-block">
                    <img src="<?= $userData['avatar_url'] ?: './frontend/assets/images/noavatar.png' ?>" 
                         class="rounded-circle border shadow-sm" 
                         style="width:140px; height:140px; object-fit:cover;"
                         alt="User Avatar">
                </div>
                <div class="mt-3">
                    <button class="btn btn-warning btn-sm rounded-pill fw-bold px-3 shadow-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editAvatarModal">
                        Edit avatar
                    </button>
                </div>
            </div>

            <div class="col-md-5 ps-md-4">
                <h3 class="fw-bold mb-1">
                    Profile name: <span class="text-danger"><?= htmlspecialchars($userData['username']) ?></span>
                </h3>
                
                <button class="btn btn-warning btn-sm rounded-pill fw-bold px-4 mt-2 shadow-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editProfileModal">
                    Edit Profile
                </button>

                <?php if ($userData['roleID'] == 1): ?>
                    <div class="mt-2">
                        <span class="badge bg-danger rounded-pill px-3 py-2">
                            <i class="bi bi-shield-lock me-1"></i> Administrator
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
                <div class="d-flex justify-content-center justify-content-md-end gap-4">
                    <div class="stat-box">
                        <h4 class="fw-bold mb-0 text-dark"><?= $followersCount ?? 0 ?></h4>
                        <small class="text-muted fw-bold">Followers</small>
                    </div>
                    <div class="stat-box border-start ps-4">
                        <h4 class="fw-bold mb-0 text-dark"><?= $followingCount ?? 0 ?></h4>
                        <small class="text-muted fw-bold">Following</small>
                    </div>
                    <div class="stat-box border-start ps-4">
                        <h4 class="fw-bold mb-0 text-dark"><?= $reviewsCount ?? 0 ?></h4>
                        <small class="text-muted fw-bold">Reviews</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* Μικρή βελτίωση για τα στατιστικά */
.stat-box h4 {
    font-size: 1.5rem;
}
.stat-box small {
    text-transform: none;
    letter-spacing: 0.5px;
}
</style>