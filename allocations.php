<?php
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['admin_id'])) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $room_id = (int)($_POST['room_id'] ?? 0);
    $allocated_date = safeValue($_POST['allocated_date'] ?? '');
    $remarks = safeValue($_POST['remarks'] ?? '');

    $stmt = $conn->prepare('INSERT INTO room_allocations (student_id, room_id, allocated_date, status, remarks) VALUES (?, ?, ?, "Active", ?)');
    $stmt->bind_param('iiss', $student_id, $room_id, $allocated_date, $remarks);
    $stmt->execute();

    $stmt2 = $conn->prepare('UPDATE rooms SET available_beds = available_beds - 1 WHERE id = ? AND available_beds > 0');
    $stmt2->bind_param('i', $room_id);
    $stmt2->execute();

    flash('success', 'Room allocated successfully.');
    redirect('allocations.php');
}

$allocations = $conn->query('SELECT ra.*, s.full_name, s.student_id, r.room_number, r.room_type FROM room_allocations ra JOIN students s ON s.id = ra.student_id JOIN rooms r ON r.id = ra.room_id ORDER BY ra.id DESC')->fetch_all(MYSQLI_ASSOC);
$students = $conn->query('SELECT id, full_name, student_id FROM students ORDER BY full_name')->fetch_all(MYSQLI_ASSOC);
$rooms = $conn->query('SELECT * FROM rooms WHERE available_beds > 0 ORDER BY room_number')->fetch_all(MYSQLI_ASSOC);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Allocation | Hostel Management System</title>
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
                    <h5 class="mb-0">Room Allocation</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#allocateRoomModal"><i class="fa-solid fa-plus me-1"></i>Assign Room</button>
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light"><tr><th>Student</th><th>Room</th><th>Type</th><th>Date</th><th>Status</th><th>Remarks</th></tr></thead>
                            <tbody>
                                <?php foreach ($allocations as $allocation): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($allocation['full_name']) ?> (<?= htmlspecialchars($allocation['student_id']) ?>)</td>
                                        <td><?= htmlspecialchars($allocation['room_number']) ?></td>
                                        <td><?= htmlspecialchars($allocation['room_type']) ?></td>
                                        <td><?= htmlspecialchars($allocation['allocated_date']) ?></td>
                                        <td><span class="badge bg-success"><?= htmlspecialchars($allocation['status']) ?></span></td>
                                        <td><?= htmlspecialchars($allocation['remarks']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="allocateRoomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header"><h5 class="modal-title">Assign Student</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body row g-3">
                        <div class="col-md-12"><label class="form-label">Student</label><select name="student_id" class="form-select" required><?php foreach ($students as $student): ?><option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['full_name']) ?> (<?= htmlspecialchars($student['student_id']) ?>)</option><?php endforeach; ?></select></div>
                        <div class="col-md-12"><label class="form-label">Available Room</label><select name="room_id" class="form-select" required><?php foreach ($rooms as $room): ?><option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['room_number']) ?> - <?= htmlspecialchars($room['room_type']) ?> (Beds: <?= htmlspecialchars($room['available_beds']) ?>)</option><?php endforeach; ?></select></div>
                        <div class="col-md-6"><label class="form-label">Allocated Date</label><input type="date" name="allocated_date" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Remarks</label><input type="text" name="remarks" class="form-control"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Allocate</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
