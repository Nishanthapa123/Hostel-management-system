<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('index.php');
}

$studentsCount = $conn->query('SELECT COUNT(*) AS total FROM students')->fetch_assoc()['total'];
$roomsCount = $conn->query('SELECT COUNT(*) AS total FROM rooms')->fetch_assoc()['total'];
$occupiedRooms = $conn->query("SELECT COUNT(*) AS total FROM room_allocations WHERE status = 'Active'")->fetch_assoc()['total'];
$vacantRooms = $roomsCount - $occupiedRooms;
$monthlyRevenue = $conn->query('SELECT COALESCE(SUM(amount), 0) AS total FROM payments')->fetch_assoc()['total'];
$recentActivities = $conn->query("SELECT 'Student' AS type, full_name AS title, admission_date AS date_text FROM students ORDER BY created_at DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include __DIR__ . '/includes/navbar.php'; ?>

        <div class="container-fluid py-4">
            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6>Total Students</h6>
                                    <h3 data-count-to="<?= (int)$studentsCount ?>">0</h3>
                                </div>
                                <div class="icon-box"><i class="fa-solid fa-user-graduate"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card info">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6>Total Rooms</h6>
                                    <h3 data-count-to="<?= (int)$roomsCount ?>">0</h3>
                                </div>
                                <div class="icon-box"><i class="fa-solid fa-bed"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card success">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6>Occupied Rooms</h6>
                                    <h3 data-count-to="<?= (int)$occupiedRooms ?>">0</h3>
                                </div>
                                <div class="icon-box"><i class="fa-solid fa-house-user"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6>Vacant Rooms</h6>
                                    <h3 data-count-to="<?= (int)$vacantRooms ?>">0</h3>
                                </div>
                                <div class="icon-box"><i class="fa-solid fa-door-open"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="card shadow-sm border-0 interactive-card">
                        <div class="card-header bg-white border-0 py-3 dashboard-section-header">
                            <h5 class="mb-0">Recent Activities</h5>
                            <div class="activity-search"><i class="fa-solid fa-magnifying-glass"></i><input type="search" data-table-filter="#recentActivitiesTable" placeholder="Filter activity" aria-label="Filter recent activity"></div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="recentActivitiesTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Title</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentActivities as $activity): ?>
                                            <tr>
                                                <td><span class="badge bg-primary"><?= htmlspecialchars($activity['type']) ?></span></td>
                                                <td><?= htmlspecialchars($activity['title']) ?></td>
                                                <td><?= htmlspecialchars($activity['date_text']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0">Monthly Revenue</h5>
                        </div>
                        <div class="card-body">
                            <div class="revenue-box">
                                <div class="amount"><?= formatCurrency($monthlyRevenue) ?></div>
                                <small class="text-muted">Collected from hostel fees</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
