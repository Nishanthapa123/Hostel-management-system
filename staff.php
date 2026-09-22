<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['admin_id'])) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $full_name = safeValue($_POST['full_name'] ?? '');
    $role = safeValue($_POST['role'] ?? '');
    $phone = safeValue($_POST['phone'] ?? '');
    $email = safeValue($_POST['email'] ?? '');
    $salary = (float)($_POST['salary'] ?? 0);
    $address = safeValue($_POST['address'] ?? '');
    $hired_date = safeValue($_POST['hired_date'] ?? '');

    $stmt = $conn->prepare('INSERT INTO staff (full_name, role, phone, email, salary, address, hired_date) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('ssssdss', $full_name, $role, $phone, $email, $salary, $address, $hired_date);
    $stmt->execute();
    flash('success', 'Staff member added successfully.');
    redirect('staff.php');
}

$staff = $conn->query('SELECT * FROM staff ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff | Hostel Management System</title>
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
                    <h5 class="mb-0">Staff Management</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal"><i class="fa-solid fa-plus me-1"></i>Add Staff</button>
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light"><tr><th>Name</th><th>Role</th><th>Phone</th><th>Email</th><th>Salary</th><th>Hired Date</th></tr></thead>
                            <tbody>
                                <?php foreach ($staff as $member): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($member['full_name']) ?></td>
                                        <td><?= htmlspecialchars($member['role']) ?></td>
                                        <td><?= htmlspecialchars($member['phone']) ?></td>
                                        <td><?= htmlspecialchars($member['email']) ?></td>
                                        <td><?= formatCurrency($member['salary']) ?></td>
                                        <td><?= htmlspecialchars($member['hired_date']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header"><h5 class="modal-title">Add Staff</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body row g-3">
                        <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Role</label><input type="text" name="role" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Salary</label><input type="number" step="0.01" name="salary" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Hired Date</label><input type="date" name="hired_date" class="form-control" required></div>
                        <div class="col-md-12"><label class="form-label">Address</label><textarea name="address" class="form-control" required></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
