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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = safeValue($_POST['full_name'] ?? '');
    $gender = safeValue($_POST['gender'] ?? '');
    $dob = safeValue($_POST['dob'] ?? '');
    $phone = safeValue($_POST['phone'] ?? '');
    $email = safeValue($_POST['email'] ?? '');
    $address = safeValue($_POST['address'] ?? '');
    $guardianName = safeValue($_POST['guardian_name'] ?? '');
    $guardianContact = safeValue($_POST['guardian_contact'] ?? '');
    $admissionDate = safeValue($_POST['admission_date'] ?? '');

    $photoPath = $student['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $_FILES['photo']['name']);
        $target = __DIR__ . '/uploads/students/' . $fileName;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $photoPath = 'uploads/students/' . $fileName;
        }
    }

    $stmt = $conn->prepare('UPDATE students SET full_name = ?, gender = ?, dob = ?, phone = ?, email = ?, address = ?, guardian_name = ?, guardian_contact = ?, admission_date = ?, photo = ? WHERE id = ?');
    $stmt->bind_param('ssssssssssi', $fullName, $gender, $dob, $phone, $email, $address, $guardianName, $guardianContact, $admissionDate, $photoPath, $id);
    $stmt->execute();
    flash('success', 'Student information updated successfully.');
    redirect('students.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student | Hostel Management System</title>
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
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Edit Student</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-6"><label class="form-label">Student ID</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['student_id']) ?>" readonly></div>
                        <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($student['full_name']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Gender</label><select name="gender" class="form-select" required>
                            <option value="Male" <?= $student['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $student['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= $student['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                        </select></div>
                        <div class="col-md-6"><label class="form-label">Date of Birth</label><input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($student['dob']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Phone Number</label><input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" required></div>
                        <div class="col-md-12"><label class="form-label">Address</label><textarea name="address" class="form-control" required><?= htmlspecialchars($student['address']) ?></textarea></div>
                        <div class="col-md-6"><label class="form-label">Guardian Name</label><input type="text" name="guardian_name" class="form-control" value="<?= htmlspecialchars($student['guardian_name']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Guardian Contact</label><input type="text" name="guardian_contact" class="form-control" value="<?= htmlspecialchars($student['guardian_contact']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Admission Date</label><input type="date" name="admission_date" class="form-control" value="<?= htmlspecialchars($student['admission_date']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Profile Photo</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Student</button>
                            <a href="students.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
