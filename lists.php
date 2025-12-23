<?php require_once 'backend/get_lists.php'; ?>
<?php include 'frontend/includes/header.php'; ?>
<?php $activePage = 'lists'; include 'frontend/includes/navbar.php'; ?>

<div class="container mt-5">
    <h2 class="fw-bold mb-5">Recently Liked Lists</h2>

    <div class="row">
        <div class="col-12">
            <?php foreach ($lists as $list): ?>
                <?php include 'frontend/includes/components/list_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="frontend/assets/js/likes.js"></script>

<?php include 'frontend/includes/footer.php'; ?>