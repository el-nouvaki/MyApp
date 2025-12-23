<?php
/**
 * frontend/includes/tabs/lists.php
 * Εμφάνιση των λιστών στο προφίλ με την οριζόντια μορφή.
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0 text-dark">My Movie Lists</h4>
    <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createListModal">
        <i class="bi bi-plus-circle me-1"></i> Create New List
    </button>
</div>

<div class="row">
    <div class="col-12">
        <?php if (empty($userLists)): ?>
            <p class="text-muted italic">You haven't created any lists yet.</p>
        <?php else: ?>
            <?php foreach ($userLists as $list): ?>
                <?php 
                    // Χειροκίνητη προσθήκη των στοιχείων που λείπουν από το query του προφίλ
                    // ώστε να μπορεί να τα δείξει το list_card.php
                    $list['username'] = $userData['username']; 
                    $list['avatar_url'] = $userData['avatar_url']; 
                ?>
                <?php include 'frontend/includes/components/list_card.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>