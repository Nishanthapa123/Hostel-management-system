<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['student_id'])) {
    redirect('student_login.php');
}

$studentDbId = (int)$_SESSION['student_id'];
$stmt = $conn->prepare('SELECT * FROM students WHERE id = ?');
$stmt->bind_param('i', $studentDbId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    unset($_SESSION['student_id'], $_SESSION['student_name']);
    redirect('student_login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'message_admin') {
    $category = safeValue($_POST['category'] ?? 'General');
    $description = safeValue($_POST['description'] ?? '');

    if ($description !== '') {
        $stmt = $conn->prepare('INSERT INTO complaints (student_id, category, description, status) VALUES (?, ?, ?, "Open")');
        $stmt->bind_param('iss', $studentDbId, $category, $description);
        $stmt->execute();
        flash('success', 'Your message was sent to the hostel admin.');
    }

    redirect('student_portal.php');
}

$flash = getFlash();

$stmt = $conn->prepare('SELECT r.room_number, r.room_type, ra.allocated_date FROM room_allocations ra JOIN rooms r ON r.id = ra.room_id WHERE ra.student_id = ? AND ra.status = "Active" LIMIT 1');
$stmt->bind_param('i', $studentDbId);
$stmt->execute();
$allocation = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare('SELECT amount, payment_month, payment_date, mode, receipt_number, status FROM payments WHERE student_id = ? ORDER BY payment_date DESC, id DESC');
$stmt->bind_param('i', $studentDbId);
$stmt->execute();
$payments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare('SELECT category, description, status, submitted_at FROM complaints WHERE student_id = ? ORDER BY submitted_at DESC LIMIT 5');
$stmt->bind_param('i', $studentDbId);
$stmt->execute();
$complaints = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portal | Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="student-portal-body">
    <nav class="portal-navbar">
        <div class="portal-brand"><span class="portal-brand-icon"><i class="fa-solid fa-building-columns"></i></span>HostelMS</div>
        <div class="d-flex align-items-center gap-3"><span class="portal-welcome">Welcome, <?= htmlspecialchars($student['full_name']) ?></span><a href="student_logout.php" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</a></div>
    </nav>
    <main class="container-fluid portal-content">
        <?php if ($flash): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?= htmlspecialchars($flash['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>
        <div class="portal-heading">
            <div><span class="eyebrow">Resident portal</span><h1>Your hostel overview</h1><p>View your stay details, payment history, and support requests.</p></div>
            <span class="student-id-badge"><i class="fa-solid fa-id-card me-2"></i><?= htmlspecialchars($student['student_id']) ?></span>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-4"><section class="portal-card profile-card h-100"><div class="profile-avatar"><i class="fa-solid fa-user"></i></div><h2><?= htmlspecialchars($student['full_name']) ?></h2><p class="text-muted mb-4"><?= htmlspecialchars($student['email']) ?></p><div class="profile-details"><div><small>Phone</small><strong><?= htmlspecialchars($student['phone']) ?></strong></div><div><small>Admission date</small><strong><?= htmlspecialchars($student['admission_date']) ?></strong></div></div></section></div>
            <div class="col-xl-4"><section class="portal-card h-100"><div class="portal-card-title"><span class="portal-icon blue"><i class="fa-solid fa-bed"></i></span><div><h2>Room allocation</h2><p>Your current accommodation</p></div></div><?php if ($allocation): ?><div class="room-number"><?= htmlspecialchars($allocation['room_number']) ?></div><div class="room-meta"><?= htmlspecialchars($allocation['room_type']) ?> room <span>Allocated <?= htmlspecialchars($allocation['allocated_date']) ?></span></div><?php else: ?><div class="portal-empty"><i class="fa-solid fa-bed"></i><span>No active room allocation</span></div><?php endif; ?></section></div>
            <div class="col-xl-4"><section class="portal-card h-100"><div class="portal-card-title"><span class="portal-icon green"><i class="fa-solid fa-wallet"></i></span><div><h2>Payment summary</h2><p>Your latest fee activity</p></div></div><?php if ($payments): ?><div class="room-number"><?= formatCurrency($payments[0]['amount']) ?></div><div class="room-meta"><?= htmlspecialchars($payments[0]['payment_month']) ?> <span class="status-pill status-<?= strtolower(htmlspecialchars($payments[0]['status'])) ?>"><?= htmlspecialchars($payments[0]['status']) ?></span></div><?php else: ?><div class="portal-empty"><i class="fa-solid fa-receipt"></i><span>No payments recorded</span></div><?php endif; ?></section></div>
        </div>

        <div class="row g-4"><div class="col-xl-7"><section class="portal-card p-0 overflow-hidden"><div class="portal-section-heading"><div><h2>Payment history</h2><p>Your recent transactions</p></div></div><div class="table-responsive"><table class="table portal-table mb-0"><thead><tr><th>Receipt</th><th>Month</th><th>Amount</th><th>Status</th></tr></thead><tbody><?php foreach ($payments as $payment): ?><tr><td>#<?= htmlspecialchars($payment['receipt_number']) ?></td><td><?= htmlspecialchars($payment['payment_month']) ?></td><td><strong><?= formatCurrency($payment['amount']) ?></strong></td><td><span class="status-pill status-<?= strtolower(htmlspecialchars($payment['status'])) ?>"><?= htmlspecialchars($payment['status']) ?></span></td></tr><?php endforeach; ?><?php if (!$payments): ?><tr><td colspan="4" class="portal-empty">No payment history available.</td></tr><?php endif; ?></tbody></table></div></section></div><div class="col-xl-5"><section class="portal-card p-0 overflow-hidden"><div class="portal-section-heading"><div><h2>Support requests</h2><p>Your latest messages to admin</p></div></div><div class="complaint-list"><?php foreach ($complaints as $complaint): ?><div class="complaint-item"><div><strong><?= htmlspecialchars($complaint['category']) ?></strong><span><?= htmlspecialchars($complaint['description']) ?></span></div><span class="complaint-status"><?= htmlspecialchars($complaint['status']) ?></span></div><?php endforeach; ?><?php if (!$complaints): ?><div class="portal-empty">No support requests yet.</div><?php endif; ?></div><form method="POST" class="message-admin-form"><input type="hidden" name="action" value="message_admin"><label class="form-label">Send a message to admin</label><select name="category" class="form-select mb-2"><option>General</option><option>Room issue</option><option>Payment question</option><option>Maintenance</option></select><textarea name="description" class="form-control mb-2" rows="3" placeholder="Write your message..." required></textarea><button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-paper-plane me-1"></i>Send message</button></form></section></div></div>
    </main>
</body>
</html>
