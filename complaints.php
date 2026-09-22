<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['admin_id'])) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $category = safeValue($_POST['category'] ?? '');
    $description = safeValue($_POST['description'] ?? '');
    $status = safeValue($_POST['status'] ?? 'Open');

    $stmt = $conn->prepare('INSERT INTO complaints (student_id, category, description, status) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('isss', $student_id, $category, $description, $status);
    $stmt->execute();
    flash('success', 'Complaint submitted successfully.');
    redirect('complaints.php');
}

$complaints = $conn->query('SELECT c.*, s.full_name, s.student_id FROM complaints c LEFT JOIN students s ON s.id = c.student_id ORDER BY c.id DESC')->fetch_all(MYSQLI_ASSOC);
$students = $conn->query('SELECT id, full_name, student_id FROM students ORDER BY full_name')->fetch_all(MYSQLI_ASSOC);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints | Hostel Management System</title>
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
                    <h5 class="mb-0">Complaint Management</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addComplaintModal"><i class="fa-solid fa-plus me-1"></i>Submit Complaint</button>
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light"><tr><th>Student</th><th>Category</th><th>Description</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php foreach ($complaints as $complaint): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($complaint['full_name'] ?? 'General') ?> <?= !empty($complaint['student_id']) ? '(' . htmlspecialchars($complaint['student_id']) . ')' : '' ?></td>
                                        <td><?= htmlspecialchars($complaint['category']) ?></td>
                                        <td><?= htmlspecialchars($complaint['description']) ?></td>
                                        <td><span class="badge bg-<?= $complaint['status'] === 'Resolved' ? 'success' : ($complaint['status'] === 'In Progress' ? 'warning' : 'primary') ?>"><?= htmlspecialchars($complaint['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addComplaintModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header"><h5 class="modal-title">Submit Complaint</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body row g-3">
                        <div class="col-md-12"><label class="form-label">Student</label><select name="student_id" class="form-select"><option value="0">General / No student</option><?php foreach ($students as $student): ?><option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['full_name']) ?> (<?= htmlspecialchars($student['student_id']) ?>)</option><?php endforeach; ?></select></div>
                        <div class="col-md-12"><label class="form-label">Category</label><input type="text" name="category" class="form-control" required></div>
                        <div class="col-md-12"><label class="form-label">Description</label><textarea name="description" class="form-control" required></textarea></div>
                        <div class="col-md-12"><label class="form-label">Status</label><select name="status" class="form-select"><option value="Open">Open</option><option value="In Progress">In Progress</option><option value="Resolved">Resolved</option></select></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
