/**
 * frontend/assets/js/likes.js
 */
function initLikeButtons() {
    // 1. ΔΙΑΧΕΙΡΙΣΗ LIKES ΓΙΑ ΛΙΣΤΕΣ (.like-btn)
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.onclick = function() {
            const listId = this.getAttribute('data-id');
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.count');

            const formData = new FormData();
            formData.append('list_id', listId);

            fetch('backend/like_list.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.status === 'liked') {
                        icon.classList.replace('bi-heart', 'bi-heart-fill');
                        icon.classList.add('text-danger');
                    } else {
                        icon.classList.replace('bi-heart-fill', 'bi-heart');
                        icon.classList.remove('text-danger');
                    }
                    if (countSpan) countSpan.textContent = data.newCount;
                } else {
                    handleLikeErrors(data.message);
                }
            })
            .catch(err => console.error('List like error:', err));
        };
    });

    // 2. ΔΙΑΧΕΙΡΙΣΗ LIKES ΓΙΑ ΤΑΙΝΙΕΣ (.movie-like-btn)
    document.querySelectorAll('.movie-like-btn').forEach(btn => {
        btn.onclick = function() {
            const movieId = this.getAttribute('data-movie-id');
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.count');

            const formData = new FormData();
            formData.append('movie_id', movieId);

            fetch('backend/toggle_movie_like.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.status === 'liked') {
                        icon.classList.replace('bi-heart', 'bi-heart-fill');
                        icon.classList.replace('text-muted', 'text-danger');
                    } else {
                        icon.classList.replace('bi-heart-fill', 'bi-heart');
                        icon.classList.replace('text-danger', 'text-muted');
                    }
                    if (countSpan) countSpan.textContent = data.newCount;
                } else {
                    handleLikeErrors(data.message);
                }
            })
            .catch(err => console.error('Movie like error:', err));
        };
    });
}

/**
 * Κοινή διαχείριση σφαλμάτων
 */
function handleLikeErrors(message) {
    if (message === 'NOT_LOGGED_IN') {
        alert('Παρακαλώ συνδεθείτε για να κάνετε like!');
    } else {
        alert('Σφάλμα: ' + message);
    }
}

// Εκκίνηση όταν φορτώσει το DOM
document.addEventListener('DOMContentLoaded', initLikeButtons);