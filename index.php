<?php
require_once __DIR__ . '/config.php';

if (isset($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = safeValue($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT id, full_name, password FROM admins WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        flash('success', 'Login successful. Welcome back!');
        redirect('dashboard.php');
    } else {
        flash('error', 'Invalid username or password.');
    }
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Management System | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">
    <div class="container login-shell">
        <div class="row min-vh-100 align-items-center justify-content-center g-0">
            <div class="col-xl-10">
                <div class="login-layout">
                    <section class="login-intro">
                        <div class="intro-mark"><i class="fa-solid fa-building-columns"></i></div>
                        <span class="intro-kicker">Hostel operations platform</span>
                        <h1>Run your residence with clarity.</h1>
                        <p>One calm workspace for students, rooms, payments, visitors, and everyday hostel operations.</p>
                        <div class="intro-points">
                            <span><i class="fa-solid fa-circle-check"></i> Manage residents and room allocation</span>
                            <span><i class="fa-solid fa-circle-check"></i> Keep fees and reports organized</span>
                        </div>
                    </section>
                    <section class="card login-card shadow-lg border-0">
                        <div class="card-body p-4 p-md-5">
                            <div class="login-form-heading mb-4">
                                <span class="eyebrow">Secure access</span>
                                <h2 class="fw-bold">Welcome back</h2>
                                <p class="text-muted mb-0">Sign in to your admin dashboard.</p>
                            </div>

                        <?php if ($flash): ?>
                            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($flash['message']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa-solid fa-right-to-bracket me-2"></i>Login
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 small text-muted text-center">
                            Demo credentials: <strong>admin</strong> / <strong>admin123</strong>
                        </div>
                        <div class="portal-switch mt-3">
                            <span>Student or resident?</span> <a href="student_login.php">Open resident portal</a>
                        </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
