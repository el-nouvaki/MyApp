<?php 
require_once 'backend/get_members.php'; 
include 'frontend/includes/header.php'; 
$activePage = 'members'; 
include 'frontend/includes/navbar.php'; 
?>

<div class="container pb-5 mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            
            <div class="row align-items-center mb-4">
                <div class="col-md-4">
                    <h2 class="fw-bold m-0">Members</h2>
                </div>
                <div class="col-md-5">
                    <form action="members.php" method="GET" class="d-flex">
                        <input type="hidden" name="limit" value="<?= $limit ?>">
                        <input class="form-control me-2 rounded-pill px-4 shadow-sm" type="search" name="q" placeholder="Search..." value="<?= htmlspecialchars($search); ?>">
                        <button class="btn btn-dark rounded-pill px-4" type="submit">Search</button>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    <select class="form-select form-select-sm d-inline-block shadow-sm" style="width: 110px;" onchange="location.href='members.php?q=<?= urlencode($search) ?>&limit=' + this.value;">
                        <?php foreach ([10, 25, 50, 100] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $opt == $limit ? 'selected' : '' ?>><?= $opt ?> rows</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="members-list bg-white shadow-sm rounded-4 overflow-hidden mb-4">
                <?php foreach ($members as $member): ?>
                    <div class="member-row d-flex align-items-center p-3 border-bottom position-relative">
                        
                        <div class="me-3">
                            <a href="view_profile.php?id=<?= $member['id'] ?>">
                                <img src="<?= $member['avatar_url'] ?: './frontend/assets/images/noavatar.png'; ?>" 
                                     class="rounded-circle border member-avatar-img" 
                                     style="width: 55px; height: 55px; object-fit: cover;">
                            </a>
                        </div>

                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold">
								<a href="view_profile.php?id=<?= $member['id'] ?>" class="text-dark text-decoration-none hover-danger">
									<?= htmlspecialchars($member['username']) ?>
									<?php if($member['id'] == $currentUserID): ?> <span class="badge bg-light text-dark border ms-1 small">You</span> <?php endif; ?>
									
									<?php if($member['active'] == 0): ?>
										<span class="badge bg-danger ms-1 small">BANNED</span>
									<?php endif; ?>
								</a>
							</h6>
                            <a href="view_profile.php?id=<?= $member['id'] ?>&tab=reviews" class="text-muted small text-decoration-underline" style="font-size: 0.75rem;">
                                <?= $member['review_count'] ?> reviews
                            </a>
                        </div>

                        <div class="d-flex align-items-center gap-4 me-4">
                            <div class="text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-film fs-5 text-muted"></i>
                                <span class="fw-bold"><?= $member['list_count'] ?></span>
                            </div>
                            <div class="text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-chat-left-dots text-danger fs-5"></i>
                                <span class="fw-bold"><?= $member['review_count'] ?></span>
                            </div>
                        </div>

                        <div style="min-width: 110px;" class="text-end">
                            <?php if ($currentUserID && $currentUserID != $member['id']): ?>
                                <form action="backend/social_controller.php" method="POST" class="m-0">
                                    <input type="hidden" name="action" value="toggle_follow">
                                    <input type="hidden" name="followed_id" value="<?= $member['id'] ?>">
                                    <input type="hidden" name="redirect_to" value="../members.php?q=<?= urlencode($search) ?>&limit=<?= $limit ?>&offset=<?= $offset ?>">
                                    
                                    <?php if ($member['is_following']): ?>
                                        <button type="submit" class="btn btn-secondary btn-sm rounded-pill px-3 w-100 fw-bold">Following</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 w-100 fw-bold">Follow</button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($hasMore): ?>
                <div class="text-center mt-4">
                    <a href="members.php?q=<?= urlencode($search) ?>&limit=<?= $limit ?>&offset=<?= $totalToFetch ?>" 
                       class="btn btn-danger px-5 rounded-pill fw-bold shadow">
                        View More
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
    /* Hover Effect: Αλλάζει το background σε απαλό γκρι */
    .member-row { 
        transition: background 0.2s ease; 
    }
    .member-row:hover { 
        background-color: #f8f9fa !important; 
    }
    
    /* Διαχωριστική γραμμή που δεν χτυπάει το avatar */
    .member-row::after {
        content: ""; position: absolute; bottom: 0; left: 85px; right: 0; height: 1px; background-color: #f0f0f0;
    }
    
    .hover-danger:hover { color: #dc3545 !important; }
    .member-avatar-img:hover { opacity: 0.8; }
</style>

<?php include 'frontend/includes/footer.php'; ?>