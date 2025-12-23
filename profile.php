<?php 
require_once 'backend/get_user_profile.php';
// ΣΗΜΕΙΩΣΗ: Το get_user_profile.php κάνει include το db.php, άρα το $dataLoader υπάρχει ήδη!

include 'frontend/includes/header.php'; 
include 'frontend/includes/navbar.php'; 

// Ορισμός των IDs για να λειτουργούν τα tabs (όπως το likes.php)
$currentUserID = $_SESSION['user_id'] ?? 0;
$targetID = $currentUserID; // Στο δικό μας προφίλ, ο στόχος (target) είμαστε εμείς οι ίδιοι

// Προσδιορισμός ενεργού tab (Default: activity)
$currentTab = $_GET['tab'] ?? 'activity'; 

// Λίστα με τα επιτρεπόμενα tabs για ασφάλεια
$allowedTabs = ['activity', 'watchlist', 'lists', 'reviews', 'talks', 'likes', 'friends'];
if (!in_array($currentTab, $allowedTabs)) { $currentTab = 'activity'; }
?>

<div class="container mt-4 mb-5">
    <?php include 'frontend/includes/profile_header.php'; ?>

    <?php include 'frontend/includes/profile_nav.php'; ?>

    <div class="tab-content-area">
        <?php include "frontend/includes/tabs/{$currentTab}.php"; ?>
    </div>
</div>

<div class="modal fade" id="editAvatarModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="backend/profile_controller.php" method="POST" class="modal-content">
            <input type="hidden" name="action" value="update_avatar">
            <div class="modal-header border-0"><h5 class="fw-bold">Update Avatar URL</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="url" name="avatar_url" class="form-control" value="<?= $userData['avatar_url'] ?>" required>
            </div>
            <div class="modal-footer border-0"><button type="submit" class="btn btn-danger rounded-pill px-4">Save</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="backend/profile_controller.php" method="POST" class="modal-content border-0 shadow-lg">
            <input type="hidden" name="action" value="update_profile">
            
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Edit My Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="small fw-bold text-muted text-uppercase">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($userData['username']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-muted text-uppercase">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($userData['email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-muted text-uppercase">New Password (leave blank to keep current)</label>
                    <input type="password" name="password" class="form-control" placeholder="********">
                </div>

                <div class="mb-3">
                    <label class="small fw-bold text-muted text-uppercase">Favorite Movie Genre</label>
                    <select name="genreID" class="form-select">
                        <?php // Καλώντας το getGenres() εδώ, το query τρέχει ΤΩΡΑ, και μόνο μία φορά.
							$genres = $dataLoader->getGenres();
							foreach($genres as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= $g['id'] == $userData['genreID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="createListModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="backend/profile_controller.php" method="POST" class="modal-content border-0 shadow-lg">
            <input type="hidden" name="action" value="create_list">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">New Movie List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="small fw-bold mb-1">List Name</label>
                    <input type="text" name="list_name" class="form-control rounded-3" placeholder="e.g. My Favorite Thrillers" required>
                </div>
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_private" id="privateSwitch">
                    <label class="form-check-label small fw-bold" for="privateSwitch">Private List (Only you can see it)</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">Create List</button>
            </div>
        </form>
    </div>
</div>

<?php if ($userData['roleID'] == 1): ?>
<div class="modal fade" id="adminPanelModal" tabindex="-1" aria-labelledby="adminPanelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold" id="adminPanelLabel"><i class="bi bi-shield-lock me-2"></i>Global User Management</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">User</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $usersList = $dataLoader->getAllUsersForAdmin($currentUserID);

                            foreach($usersList as $u): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= htmlspecialchars($u['username']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($u['email']) ?></div>
                                </td>
                                <td>
                                    <?= $u['roleID'] == 1 ? '<span class="badge bg-danger">Admin</span>' : '<span class="badge bg-secondary">User</span>' ?>
                                </td>
                                <td>
                                    <?= $u['active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Banned</span>' ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="view_profile.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill">View</a>
                                        
                                        <form action="backend/profile_controller.php" method="POST">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="target_user_id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="new_status" value="<?= $u['active'] ? 0 : 1 ?>">
                                            <button type="submit" class="btn btn-sm <?= $u['active'] ? 'btn-danger' : 'btn-success' ?> rounded-pill">
                                                <?= $u['active'] ? 'Ban' : 'Unban' ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="frontend/assets/js/likes.js"></script>

<?php include 'frontend/includes/footer.php'; ?>