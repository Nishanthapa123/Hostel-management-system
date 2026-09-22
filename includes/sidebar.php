<?php
if (!isset($_SESSION['admin_id'])) {
    return;
}
?>
<div class="sidebar">
    <div class="brand">
        <i class="fa-solid fa-building-columns"></i>
        <span>HostelMS</span>
    </div>
    <nav class="nav flex-column px-3">
        <a class="nav-link active" href="dashboard.php"><i class="fa-solid fa-gauge-high me-2"></i>Dashboard</a>
        <a class="nav-link" href="students.php"><i class="fa-solid fa-user-graduate me-2"></i>Students</a>
        <a class="nav-link" href="rooms.php"><i class="fa-solid fa-bed me-2"></i>Rooms</a>
        <a class="nav-link" href="allocations.php"><i class="fa-solid fa-key me-2"></i>Allocations</a>
        <a class="nav-link" href="payments.php"><i class="fa-solid fa-wallet me-2"></i>Fees</a>
        <a class="nav-link" href="visitors.php"><i class="fa-solid fa-users me-2"></i>Visitors</a>
        <a class="nav-link" href="complaints.php"><i class="fa-solid fa-triangle-exclamation me-2"></i>Complaints</a>
        <a class="nav-link" href="staff.php"><i class="fa-solid fa-user-tie me-2"></i>Staff</a>
        <a class="nav-link" href="reports.php"><i class="fa-solid fa-chart-column me-2"></i>Reports</a>
    </nav>
</div>
