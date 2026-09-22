<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('index.php');
}

$studentSearch = $_GET['search'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $conn->prepare('DELETE FROM students WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    flash('success', 'Student deleted successfully.');
    redirect('students.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $studentId = safeValue($_POST['student_id'] ?? '');
    $fullName = safeValue($_POST['full_name'] ?? '');
    $gender = safeValue($_POST['gender'] ?? '');
    $dob = safeValue($_POST['dob'] ?? '');
    $phone = safeValue($_POST['phone'] ?? '');
    $email = safeValue($_POST['email'] ?? '');
    $address = safeValue($_POST['address'] ?? '');
    $guardianName = safeValue($_POST['guardian_name'] ?? '');
    $guardianContact = safeValue($_POST['guardian_contact'] ?? '');
    $admissionDate = safeValue($_POST['admission_date'] ?? '');

    $uploadPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $_FILES['photo']['name']);
        $target = __DIR__ . '/uploads/students/' . $fileName;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $uploadPath = 'uploads/students/' . $fileName;
        }
    }

    $stmt = $conn->prepare('INSERT INTO students (student_id, full_name, gender, dob, phone, email, address, guardian_name, guardian_contact, admission_date, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssssssssss', $studentId, $fullName, $gender, $dob, $phone, $email, $address, $guardianName, $guardianContact, $admissionDate, $uploadPath);
    $stmt->execute();
    flash('success', 'Student added successfully.');
    redirect('students.php');
}

$query = "SELECT * FROM students WHERE full_name LIKE ? OR student_id LIKE ? ORDER BY id DESC";
$searchTerm = '%' . $studentSearch . '%';
$stmt = $conn->prepare($query);
$stmt->bind_param('ss', $searchTerm, $searchTerm);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students | Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include __DIR__ . '/includes/navbar.php'; ?>

        <div class="container-fluid py-4">
            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">Student Management</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="fa-solid fa-plus me-1"></i>Add Student
                    </button>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Search Student</label>
                            <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($studentSearch) ?>" placeholder="Search by name or student ID">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-primary w-100"><i class="fa-solid fa-search me-2"></i>Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Admission</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($student['photo'])): ?>
                                                <img src="<?= htmlspecialchars($student['photo']) ?>" class="student-thumb" alt="Student photo">
                                            <?php else: ?>
                                                <div class="student-thumb placeholder-user"><i class="fa-solid fa-user"></i></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($student['student_id']) ?></td>
                                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                                        <td><?= htmlspecialchars($student['gender']) ?></td>
                                        <td><?= htmlspecialchars($student['phone']) ?></td>
                                        <td><?= htmlspecialchars($student['email']) ?></td>
                                        <td><?= htmlspecialchars($student['admission_date']) ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="student_view.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-eye"></i></a>
                                                <a href="student_edit.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></a>
                                                <form method="POST" onsubmit="return confirm('Delete this student?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $student['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <input type="hidden" name="action" value="add">
                        <div class="col-md-6"><label class="form-label">Student ID</label><input type="text" name="student_id" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Gender</label><select name="gender" class="form-select" required><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option></select></div>
                        <div class="col-md-6"><label class="form-label">Date of Birth</label><input type="date" name="dob" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Phone Number</label><input type="tel" name="phone" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="col-md-12"><label class="form-label">Address</label><textarea name="address" class="form-control" required></textarea></div>
                        <div class="col-md-6"><label class="form-label">Guardian Name</label><input type="text" name="guardian_name" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Guardian Contact</label><input type="text" name="guardian_contact" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Admission Date</label><input type="date" name="admission_date" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Profile Photo</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
