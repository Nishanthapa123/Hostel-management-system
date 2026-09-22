<?php
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_id'])) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $conn->prepare('DELETE FROM rooms WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    flash('success', 'Room deleted successfully.');
    redirect('rooms.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $room_number = safeValue($_POST['room_number'] ?? '');
    $room_type = safeValue($_POST['room_type'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 0);
    $available_beds = (int)($_POST['available_beds'] ?? 0);
    $floor_number = (int)($_POST['floor_number'] ?? 0);
    $status = safeValue($_POST['status'] ?? 'Available');

    $stmt = $conn->prepare('INSERT INTO rooms (room_number, room_type, capacity, available_beds, floor_number, status) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('ssiiis', $room_number, $room_type, $capacity, $available_beds, $floor_number, $status);
    $stmt->execute();
    flash('success', 'Room added successfully.');
    redirect('rooms.php');
}

$rooms = $conn->query('SELECT * FROM rooms ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms | Hostel Management System</title>
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
                    <h5 class="mb-0">Room Management</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal"><i class="fa-solid fa-plus me-1"></i>Add Room</button>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Room No</th>
                                    <th>Type</th>
                                    <th>Capacity</th>
                                    <th>Available Beds</th>
                                    <th>Floor</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rooms as $room): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($room['room_number']) ?></td>
                                        <td><?= htmlspecialchars($room['room_type']) ?></td>
                                        <td><?= htmlspecialchars($room['capacity']) ?></td>
                                        <td><?= htmlspecialchars($room['available_beds']) ?></td>
                                        <td><?= htmlspecialchars($room['floor_number']) ?></td>
                                        <td><span class="badge bg-<?= $room['status'] === 'Available' ? 'success' : ($room['status'] === 'Occupied' ? 'primary' : 'warning') ?>"><?= htmlspecialchars($room['status']) ?></span></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="room_edit.php?id=<?= $room['id'] ?>" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></a>
                                                <form method="POST" onsubmit="return confirm('Delete room?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $room['id'] ?>">
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

    <div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Room</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <input type="hidden" name="action" value="add">
                        <div class="col-md-6"><label class="form-label">Room Number</label><input type="text" name="room_number" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Room Type</label><select name="room_type" class="form-select" required>
                            <option value="Single">Single</option>
                            <option value="Double">Double</option>
                            <option value="Triple">Triple</option>
                            <option value="Dormitory">Dormitory</option>
                        </select></div>
                        <div class="col-md-6"><label class="form-label">Capacity</label><input type="number" name="capacity" class="form-control" min="1" required></div>
                        <div class="col-md-6"><label class="form-label">Available Beds</label><input type="number" name="available_beds" class="form-control" min="0" required></div>
                        <div class="col-md-6"><label class="form-label">Floor Number</label><input type="number" name="floor_number" class="form-control" min="1" required></div>
                        <div class="col-md-6"><label class="form-label">Status</label><select name="status" class="form-select" required>
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Maintenance">Maintenance</option>
                        </select></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
