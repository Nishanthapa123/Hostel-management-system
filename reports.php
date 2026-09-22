<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['admin_id'])) redirect('index.php');

$studentReport = $conn->query('SELECT COUNT(*) AS total FROM students')->fetch_assoc();
$roomReport = $conn->query('SELECT COUNT(*) AS total, SUM(available_beds) AS available_beds FROM rooms')->fetch_assoc();
$feeReport = $conn->query('SELECT COALESCE(SUM(amount),0) AS total FROM payments')->fetch_assoc();
$dueReport = $conn->query('SELECT COUNT(*) AS total FROM payments WHERE status = "Due"')->fetch_assoc();
$complaintReport = $conn->query('SELECT COUNT(*) AS total FROM complaints')->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include __DIR__ . '/includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="row g-4">
                <div class="col-md-6 col-xl-4"><div class="card shadow-sm border-0"><div class="card-body"><h6>Student Report</h6><h3><?= htmlspecialchars($studentReport['total']) ?></h3></div></div></div>
                <div class="col-md-6 col-xl-4"><div class="card shadow-sm border-0"><div class="card-body"><h6>Room Occupancy</h6><h3><?= htmlspecialchars($roomReport['total']) ?> Rooms</h3></div></div></div>
                <div class="col-md-6 col-xl-4"><div class="card shadow-sm border-0"><div class="card-body"><h6>Fee Collection</h6><h3><?= formatCurrency($feeReport['total']) ?></h3></div></div></div>
                <div class="col-md-6 col-xl-4"><div class="card shadow-sm border-0"><div class="card-body"><h6>Due Fee Report</h6><h3><?= htmlspecialchars($dueReport['total']) ?></h3></div></div></div>
                <div class="col-md-6 col-xl-4"><div class="card shadow-sm border-0"><div class="card-body"><h6>Complaint Report</h6><h3><?= htmlspecialchars($complaintReport['total']) ?></h3></div></div></div>
                <div class="col-md-6 col-xl-4"><div class="card shadow-sm border-0"><div class="card-body"><h6>Available Beds</h6><h3><?= htmlspecialchars($roomReport['available_beds']) ?></h3></div></div></div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
