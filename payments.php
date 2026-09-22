<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['admin_id'])) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $amount = (float)($_POST['amount'] ?? 0);
    $payment_month = safeValue($_POST['payment_month'] ?? '');
    $payment_date = safeValue($_POST['payment_date'] ?? '');
    $mode = safeValue($_POST['mode'] ?? 'Cash');
    $receipt_number = safeValue($_POST['receipt_number'] ?? '');
    $status = safeValue($_POST['status'] ?? 'Paid');

    $stmt = $conn->prepare('INSERT INTO payments (student_id, amount, payment_month, payment_date, mode, receipt_number, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('idsssss', $student_id, $amount, $payment_month, $payment_date, $mode, $receipt_number, $status);
    $stmt->execute();
    flash('success', 'Payment recorded successfully.');
    redirect('payments.php');
}

$payments = $conn->query('SELECT p.*, s.full_name, s.student_id FROM payments p JOIN students s ON s.id = p.student_id ORDER BY p.id DESC')->fetch_all(MYSQLI_ASSOC);
$students = $conn->query('SELECT id, full_name, student_id FROM students ORDER BY full_name')->fetch_all(MYSQLI_ASSOC);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments | Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include __DIR__ . '/includes/navbar.php'; ?>
        <div class="container-fluid py-4 payments-page">
            <?php if ($flash): ?><div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show"><?= htmlspecialchars($flash['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <div class="payments-heading mb-4">
                <div>
                    <span class="eyebrow"><i class="fa-solid fa-wallet me-2"></i>Finance workspace</span>
                    <h1>Fee Management</h1>
                    <p class="mb-0">Track hostel payments and keep every receipt in one place.</p>
                </div>
                <button class="btn btn-primary record-payment-btn" data-bs-toggle="modal" data-bs-target="#addPaymentModal"><i class="fa-solid fa-plus me-2"></i>Record Payment</button>
            </div>
            <div class="payment-summary mb-4">
                <div class="summary-item"><span class="summary-icon"><i class="fa-solid fa-receipt"></i></span><div><small>Total records</small><strong><?= count($payments) ?></strong></div></div>
                <div class="summary-item"><span class="summary-icon summary-icon-green"><i class="fa-solid fa-circle-check"></i></span><div><small>Latest activity</small><strong><?= !empty($payments) ? htmlspecialchars($payments[0]['payment_date']) : 'No payments' ?></strong></div></div>
                <div class="summary-note"><i class="fa-solid fa-shield-halved"></i><span>Payment records are stored securely for reporting.</span></div>
            </div>
            <div class="card payments-table-card">
                <div class="card-header">
                    <div><h2>Recent payments</h2><p>Review the latest fee transactions</p></div>
                    <span class="record-count"><?= count($payments) ?> records</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table payments-table mb-0 align-middle">
                            <thead><tr><th>Receipt</th><th>Student</th><th>Month</th><th>Amount</th><th>Mode</th><th>Status</th><th>Payment Date</th></tr></thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><span class="receipt-number">#<?= htmlspecialchars($payment['receipt_number']) ?></span></td>
                                        <td><strong><?= htmlspecialchars($payment['full_name']) ?></strong><small class="student-id d-block"><?= htmlspecialchars($payment['student_id']) ?></small></td>
                                        <td><?= htmlspecialchars($payment['payment_month']) ?></td>
                                        <td><strong class="amount-value"><?= formatCurrency($payment['amount']) ?></strong></td>
                                        <td><span class="mode-pill"><i class="fa-solid fa-wallet"></i><?= htmlspecialchars($payment['mode']) ?></span></td>
                                        <td><span class="status-pill status-<?= strtolower(htmlspecialchars($payment['status'])) ?>"><i class="fa-solid fa-circle"></i><?= htmlspecialchars($payment['status']) ?></span></td>
                                        <td><?= htmlspecialchars($payment['payment_date']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($payments)): ?><tr><td colspan="7" class="empty-state"><i class="fa-solid fa-receipt"></i><strong>No payments recorded yet</strong><span>Use "Record Payment" to add the first transaction.</span></td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header"><h5 class="modal-title">Record Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body row g-3">
                        <div class="col-md-12"><label class="form-label">Student</label><select name="student_id" class="form-select" required>
                            <?php foreach ($students as $student): ?><option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['full_name']) ?> (<?= htmlspecialchars($student['student_id']) ?>)</option><?php endforeach; ?>
                        </select></div>
                        <div class="col-md-6"><label class="form-label">Amount</label><input type="number" step="0.01" name="amount" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Month</label><input type="text" name="payment_month" class="form-control" placeholder="e.g. January 2025" required></div>
                        <div class="col-md-6"><label class="form-label">Date</label><input type="date" name="payment_date" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Mode</label><select name="mode" class="form-select"><option>Cash</option><option>Card</option><option>UPI</option><option>Bank Transfer</option></select></div>
                        <div class="col-md-6"><label class="form-label">Receipt No</label><input type="text" name="receipt_number" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Status</label><select name="status" class="form-select"><option value="Paid">Paid</option><option value="Due">Due</option></select></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
