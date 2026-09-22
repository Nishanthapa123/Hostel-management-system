<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('index.php');
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT * FROM rooms WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();

if (!$room) {
    flash('error', 'Room not found.');
    redirect('rooms.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = safeValue($_POST['room_number'] ?? '');
    $room_type = safeValue($_POST['room_type'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 0);
    $available_beds = (int)($_POST['available_beds'] ?? 0);
    $floor_number = (int)($_POST['floor_number'] ?? 0);
    $status = safeValue($_POST['status'] ?? 'Available');

    $stmt = $conn->prepare('UPDATE rooms SET room_number = ?, room_type = ?, capacity = ?, available_beds = ?, floor_number = ?, status = ? WHERE id = ?');
    $stmt->bind_param('ssiiisi', $room_number, $room_type, $capacity, $available_beds, $floor_number, $status, $id);
    $stmt->execute();
    flash('success', 'Room updated successfully.');
    redirect('rooms.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room | Hostel Management System</title>
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
                    <h5 class="mb-0">Edit Room</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-6"><label class="form-label">Room Number</label><input type="text" name="room_number" class="form-control" value="<?= htmlspecialchars($room['room_number']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Room Type</label><select name="room_type" class="form-select" required>
                            <option value="Single" <?= $room['room_type'] == 'Single' ? 'selected' : '' ?>>Single</option>
                            <option value="Double" <?= $room['room_type'] == 'Double' ? 'selected' : '' ?>>Double</option>
                            <option value="Triple" <?= $room['room_type'] == 'Triple' ? 'selected' : '' ?>>Triple</option>
                            <option value="Dormitory" <?= $room['room_type'] == 'Dormitory' ? 'selected' : '' ?>>Dormitory</option>
                        </select></div>
                        <div class="col-md-6"><label class="form-label">Capacity</label><input type="number" name="capacity" class="form-control" min="1" value="<?= htmlspecialchars($room['capacity']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Available Beds</label><input type="number" name="available_beds" class="form-control" min="0" value="<?= htmlspecialchars($room['available_beds']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Floor Number</label><input type="number" name="floor_number" class="form-control" min="1" value="<?= htmlspecialchars($room['floor_number']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Status</label><select name="status" class="form-select" required>
                            <option value="Available" <?= $room['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                            <option value="Occupied" <?= $room['status'] == 'Occupied' ? 'selected' : '' ?>>Occupied</option>
                            <option value="Maintenance" <?= $room['status'] == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        </select></div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Room</button>
                            <a href="rooms.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
