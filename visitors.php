<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['admin_id'])) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $visitor_name = safeValue($_POST['visitor_name'] ?? '');
    $purpose = safeValue($_POST['purpose'] ?? '');
    $entry_time = safeValue($_POST['entry_time'] ?? '');
    $exit_time = safeValue($_POST['exit_time'] ?? '');

    $stmt = $conn->prepare('INSERT INTO visitors (student_id, visitor_name, purpose, entry_time, exit_time) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('issss', $student_id, $visitor_name, $purpose, $entry_time, $exit_time);
    $stmt->execute();
    flash('success', 'Visitor entry recorded.');
    redirect('visitors.php');
}

$visitors = $conn->query('SELECT v.*, s.full_name, s.student_id FROM visitors v JOIN students s ON s.id = v.student_id ORDER BY v.id DESC')->fetch_all(MYSQLI_ASSOC);
$students = $conn->query('SELECT id, full_name, student_id FROM students ORDER BY full_name')->fetch_all(MYSQLI_ASSOC);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitors | Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include __DIR__ . '/includes/navbar.php'; ?>
        <div class="container-fluid py-4">
            <?php if ($flash): ?><div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show"><?= htmlspecialchars($flash['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">Visitor Management</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVisitorModal"><i class="fa-solid fa-plus me-1"></i>Entry Visitor</button>
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light"><tr><th>Student</th><th>Visitor</th><th>Purpose</th><th>Entry</th><th>Exit</th></tr></thead>
                            <tbody>
                                <?php foreach ($visitors as $visitor): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($visitor['full_name']) ?> (<?= htmlspecialchars($visitor['student_id']) ?>)</td>
                                        <td><?= htmlspecialchars($visitor['visitor_name']) ?></td>
                                        <td><?= htmlspecialchars($visitor['purpose']) ?></td>
                                        <td><?= htmlspecialchars($visitor['entry_time']) ?></td>
                                        <td><?= htmlspecialchars($visitor['exit_time'] ?: 'Still on campus') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addVisitorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header"><h5 class="modal-title">Record Visitor</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body row g-3">
                        <div class="col-md-12"><label class="form-label">Student</label><select name="student_id" class="form-select" required><?php foreach ($students as $student): ?><option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['full_name']) ?> (<?= htmlspecialchars($student['student_id']) ?>)</option><?php endforeach; ?></select></div>
                        <div class="col-md-12"><label class="form-label">Visitor Name</label><input type="text" name="visitor_name" class="form-control" required></div>
                        <div class="col-md-12"><label class="form-label">Purpose</label><input type="text" name="purpose" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Entry Time</label><input type="datetime-local" name="entry_time" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Exit Time</label><input type="datetime-local" name="exit_time" class="form-control"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
