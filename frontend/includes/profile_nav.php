<?php
/**
 * profile_nav.php - Η μπάρα πλοήγησης του προφίλ
 */
// Συνάρτηση για να επιστρέφει το σωστό χρώμα button
function getBtnClass($tabName, $currentTab) {
    return ($tabName === $currentTab) ? 'btn-danger' : 'btn-warning';
}
?>

<div class="d-flex flex-wrap gap-2 mb-5 p-3 border rounded-3 bg-white shadow-sm">
    <a href="profile.php?tab=activity" class="btn <?= getBtnClass('activity', $currentTab) ?> rounded-pill px-4 fw-bold">Activity</a>
    <a href="profile.php?tab=watchlist" class="btn <?= getBtnClass('watchlist', $currentTab) ?> rounded-pill px-3 fw-bold">My Watchlist</a>
    <a href="profile.php?tab=lists" class="btn <?= getBtnClass('lists', $currentTab) ?> rounded-pill px-3 fw-bold">My Lists</a>
    <a href="profile.php?tab=reviews" class="btn <?= getBtnClass('reviews', $currentTab) ?> rounded-pill px-3 fw-bold">My Reviews</a>
    <a href="profile.php?tab=talks" class="btn <?= getBtnClass('talks', $currentTab) ?> rounded-pill px-3 fw-bold">My talks</a>
    <a href="profile.php?tab=likes" class="btn <?= getBtnClass('likes', $currentTab) ?> rounded-pill px-3 fw-bold">Likes</a>
    <a href="profile.php?tab=friends" class="btn <?= getBtnClass('friends', $currentTab) ?> rounded-pill px-3 fw-bold">My Friends</a>
    
    <?php if ($userData['roleID'] == 1): ?>
        <button class="btn btn-dark rounded-pill px-3 fw-bold ms-auto" data-bs-toggle="modal" data-bs-target="#adminPanelModal">
            <i class="bi bi-shield-lock"></i> Admin Panel
        </button>
    <?php endif; ?>
</div>