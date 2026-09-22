<?php
if (!isset($_SESSION['admin_id'])) {
    return;
}
?>
<nav class="top-navbar">
    <div class="container-fluid d-flex justify-content-between align-items-center py-3 px-4">
        <div>
            <button class="btn btn-link d-lg-none text-dark p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                <i class="fa-solid fa-bars fs-4"></i>
            </button>
            <span class="fw-bold text-primary">College Hostel Dashboard</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted">Welcome, <?= htmlspecialchars(getAdminName()) ?></span>
            <a href="logout.php" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <?php include __DIR__ . '/sidebar.php'; ?>
    </div>
</div>
<script src="assets/js/app.js"></script>
