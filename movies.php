<?php
	require_once 'backend/get_movies.php';
	include 'frontend/includes/header.php';
	$activePage = 'movies';
    include 'frontend/includes/navbar.php'; 
?>

<div class="container pb-5">
    
    <div class="row align-items-center mb-4 mt-4">
        <div class="col-md-4">
            <h2 class="fw-bold m-0">
                <?php echo $search_query ? "Search Results" : "Latest Movies"; ?>
            </h2>
            <span class="badge bg-secondary"><?php echo $totalMovies; ?> movies total</span>
        </div>
        
        <div class="col-md-5">
            <form action="movies.php" method="GET" class="d-flex">
                <input type="hidden" name="limit" value="<?php echo $limit; ?>">
                <input class="form-control me-2 rounded-pill px-4 shadow-sm" type="search" name="q" placeholder="Search movies..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="btn btn-dark rounded-pill px-4 shadow-sm" type="submit">Search</button>
            </form>
        </div>

        <div class="col-md-3 text-end">
            <div class="d-inline-flex align-items-center gap-2">
                <span class="small text-muted">Show:</span>
                <select class="form-select form-select-sm shadow-sm" style="width: 130px;" onchange="location.href='movies.php?q=<?php echo urlencode($search_query); ?>&page=1&limit=' + this.value;">
                    <?php foreach ([20, 50, 100] as $opt): ?>
                        <option value="<?php echo $opt; ?>" <?php echo $opt == $limit ? 'selected' : ''; ?>><?php echo $opt; ?> per page</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4 mb-5">
        <?php foreach ($movies as $movie): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm movie-card" style="transition: transform 0.2s; border-radius: 10px; overflow: hidden;">
                    
                    <div style="overflow: hidden;">
                        <?php 
                            $poster = $movie['poster_path'] 
                                ? $img_base_url . $movie['poster_path'] 
                                : "https://placehold.co/500x750?text=No+Image";
                        ?>
                        <a href="movie_details.php?id=<?php echo $movie['id']; ?>">
                            <img src="<?php echo $poster; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>" 
                                 style="height: 350px; object-fit: cover; transition: transform 0.3s;"
                                 onmouseover="this.style.transform='scale(1.05)'" 
                                 onmouseout="this.style.transform='scale(1)'">
                        </a>
                    </div>

                    <div class="card-body p-2 mt-2">
                        <h6 class="card-title fw-bold text-truncate" title="<?php echo htmlspecialchars($movie['title']); ?>">
                            <?php echo htmlspecialchars($movie['title']); ?>
                        </h6>
                        <div class="d-flex justify-content-between align-items-center small text-muted">
							<span><?php echo date('Y', strtotime($movie['release_date'])); ?></span>
							<div class="d-flex align-items-center gap-2">
								<span class="badge <?php echo $movie['TMDB_vote_average'] >= 7 ? 'bg-success' : ($movie['TMDB_vote_average'] >= 5 ? 'bg-warning' : 'bg-danger'); ?>">
									<?php echo number_format($movie['TMDB_vote_average'], 1); ?> <i class="bi bi-star-fill" style="font-size: 0.8em;"></i>
								</span>
								<span class="d-flex align-items-center">
									<i class="bi <?php echo ($movie['likes_count'] > 0) ? 'bi-heart-fill text-danger' : 'bi-heart'; ?>"></i>
									<small class="ms-1"><?php echo $movie['likes_count']; ?></small>
								</span>
							</div>
						</div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link shadow-sm rounded-start-pill px-3" href="movies.php?q=<?php echo urlencode($search_query); ?>&page=<?php echo $page-1; ?>&limit=<?php echo $limit; ?>">Previous</a>
                </li>

                <?php 
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++): 
                ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link shadow-sm" href="movies.php?q=<?php echo urlencode($search_query); ?>&page=<?php echo $i; ?>&limit=<?php echo $limit; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link shadow-sm rounded-end-pill px-3" href="movies.php?q=<?php echo urlencode($search_query); ?>&page=<?php echo $page+1; ?>&limit=<?php echo $limit; ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

    <?php if (count($movies) === 0): ?>
        <div class="text-center mt-5">
            <h4 class="text-muted">No movies found.</h4>
            <a href="movies.php" class="btn btn-outline-dark mt-3">View All Movies</a>
        </div>
    <?php endif; ?>

</div>

<style>
    .movie-card:hover { transform: translateY(-5px); }
    .pagination .page-link { color: #212529; border: none; margin: 0 2px; }
    .pagination .page-item.active .page-link { background-color: #dc3545; color: white; }
</style>

<?php include 'frontend/includes/footer.php'; ?>