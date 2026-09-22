<?php
require_once __DIR__ . '/config.php';

if (isset($_SESSION['student_id'])) {
    redirect('student_portal.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = safeValue($_POST['student_id'] ?? '');
    $phone = safeValue($_POST['phone'] ?? '');

    $stmt = $conn->prepare('SELECT id, student_id, full_name FROM students WHERE student_id = ? AND phone = ?');
    $stmt->bind_param('ss', $studentId, $phone);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

    if ($student) {
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['full_name'];
        redirect('student_portal.php');
    }

    flash('error', 'Student ID or phone number is incorrect.');
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Access | Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">
    <div class="container login-shell">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card login-card shadow-lg border-0 resident-login-card">
                    <div class="card-body p-4 p-md-5">
                        <div class="login-form-heading mb-4">
                            <span class="eyebrow"><i class="fa-solid fa-user me-2"></i>Resident portal</span>
                            <h2 class="fw-bold">Welcome, resident</h2>
                            <p class="text-muted mb-0">Check your hostel information and fee activity.</p>
                        </div>

                        <?php if ($flash): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($flash['message']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student ID</label>
                                <input type="text" class="form-control" id="student_id" name="student_id" placeholder="e.g. STU-1001" required>
                            </div>
                            <div class="mb-4">
                                <label for="phone" class="form-label">Registered phone number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your registered phone" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Open my portal
                            </button>
                        </form>

                        <div class="portal-switch mt-4">
                            <span>Hostel staff?</span> <a href="index.php">Admin login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
