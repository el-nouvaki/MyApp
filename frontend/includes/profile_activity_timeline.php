<?php
/**
 * frontend/includes/profile_activity_timeline.php
 */
?>

<?php if(empty($activities)): ?>
    <div class="text-center py-4">
        <p class="text-muted mt-2">No recent activity recorded yet.</p>
    </div>
<?php else: ?>
    <div class="timeline-container ps-3 border-start border-2 border-light">
        <?php foreach($activities as $act): ?>
            <?php 
                $icon = 'bi-circle-fill'; $color = 'text-secondary'; $actionText = '';
                $linkPage = ($act['type'] === 'list') ? 'list_details.php' : 'movie_details.php';

                switch($act['type']) {
                    case 'watchlist': 
                        $icon = 'bi-bookmark-plus-fill'; $color = 'text-primary'; 
                        $actionText = 'Πρόσθεσε στη watchlist τη ταινία:'; break;
                    case 'review': 
                        $icon = 'bi-chat-quote-fill'; $color = 'text-success'; 
                        $actionText = 'Έγραψε κριτική για το:'; break;
                    case 'list': 
                        $icon = 'bi-list-stars'; $color = 'text-warning'; 
                        $actionText = 'Δημιούργησε τη λίστα:'; break;
                    case 'like': 
                        $icon = 'bi-heart-fill'; $color = 'text-danger'; 
                        $actionText = 'Του άρεσε η ταινία:'; break;
                }
            ?>
            <div class="mb-4 position-relative">
                <i class="bi <?= $icon ?> <?= $color ?> position-absolute bg-white" 
                   style="left: -25px; top: 0; font-size: 1.2rem; padding: 2px;"></i>
                
                <div class="ms-3">
                    <span class="small fw-bold text-dark">
                        <?= $actionText ?> 
                        <a href="<?= $linkPage ?>?id=<?= $act['item_id'] ?>" class="text-danger text-decoration-none fw-bold">
                            "<?= htmlspecialchars($act['detail']) ?>"
                        </a>
                    </span>
                    <div class="text-muted" style="font-size: 0.75rem;">
                        <?= date('d M Y, H:i', strtotime($act['activity_date'])) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
/* Μικρό CSS για να φαίνεται όμορφο το timeline */
.timeline-container {
    margin-left: 10px;
}
.timeline-container i {
    z-index: 2;
}
</style>