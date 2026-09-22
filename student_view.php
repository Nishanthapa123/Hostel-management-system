<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('index.php');
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT * FROM students WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    flash('error', 'Student not found.');
    redirect('students.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details | Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include __DIR__ . '/includes/navbar.php'; ?>

        <div class="container-fluid py-4">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 py-3">
                    <h5 class="mb-0">Student Profile</h5>
                    <a href="students.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3 text-center">
                            <?php if (!empty($student['photo'])): ?>
                                <img src="<?= htmlspecialchars($student['photo']) ?>" class="img-fluid rounded-circle border" style="width:180px;height:180px;object-fit:cover;">
                            <?php else: ?>
                                <div class="rounded-circle border d-flex align-items-center justify-content-center" style="width:180px;height:180px;margin:0 auto;background:#f5f5f5;font-size:56px;color:#6c757d;"><i class="fa-solid fa-user"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-6"><p class="detail-label">Student ID</p><p><?= htmlspecialchars($student['student_id']) ?></p></div>
                                <div class="col-md-6"><p class="detail-label">Full Name</p><p><?= htmlspecialchars($student['full_name']) ?></p></div>
                                <div class="col-md-6"><p class="detail-label">Gender</p><p><?= htmlspecialchars($student['gender']) ?></p></div>
                                <div class="col-md-6"><p class="detail-label">Date of Birth</p><p><?= htmlspecialchars($student['dob']) ?></p></div>
                                <div class="col-md-6"><p class="detail-label">Phone Number</p><p><?= htmlspecialchars($student['phone']) ?></p></div>
                                <div class="col-md-6"><p class="detail-label">Email</p><p><?= htmlspecialchars($student['email']) ?></p></div>
                                <div class="col-md-12"><p class="detail-label">Address</p><p><?= htmlspecialchars($student['address']) ?></p></div>
                                <div class="col-md-6"><p class="detail-label">Guardian Name</p><p><?= htmlspecialchars($student['guardian_name']) ?></p></div>
                                <div class="col-md-6"><p class="detail-label">Guardian Contact</p><p><?= htmlspecialchars($student['guardian_contact']) ?></p></div>
                                <div class="col-md-6"><p class="detail-label">Admission Date</p><p><?= htmlspecialchars($student['admission_date']) ?></p></div>
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
