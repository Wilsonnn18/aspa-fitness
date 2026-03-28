<?php
// admin/manage_users.php
require_once __DIR__ . '/../config/app.php';
require_login('admin');

$successMsg = '';
if (($_POST['action'] ?? '') === 'add') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
    $password = $_POST['password'] ?? '';

    if ($name !== '' && $email !== '' && $password !== '') {
        $pass = password_hash($password, PASSWORD_DEFAULT);
        if ($role === 'admin') {
            $check = $conn->prepare('SELECT admin_id FROM admin WHERE email = ? LIMIT 1');
            $check->bind_param('s', $email);
            $check->execute();
            $exists = $check->get_result()->num_rows > 0;

            if (!$exists) {
                $stmt = $conn->prepare('INSERT INTO admin (name,email,password) VALUES (?,?,?)');
                $stmt->bind_param('sss', $name, $email, $pass);
                $stmt->execute();
                $successMsg = 'Admin added successfully.';
            }
        } else {
            $check = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $check->bind_param('s', $email);
            $check->execute();
            $exists = $check->get_result()->num_rows > 0;

            if (!$exists) {
                $stmt = $conn->prepare('INSERT INTO users (name,email,phone,password) VALUES (?,?,?,?)');
                $stmt->bind_param('ssss', $name, $email, $phone, $pass);
                $stmt->execute();
                $successMsg = 'User added successfully.';
            }
        }
    }
}

$deleteId = (int)($_GET['delete'] ?? 0);
if ($deleteId > 0) {
    $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
    $stmt->bind_param('i', $deleteId);
    $stmt->execute();
}

$users = [];
$res = $conn->query('SELECT id,name,email,phone,created_at FROM users ORDER BY id DESC');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $row['role'] = 'user';
        $users[] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="page-section">
    <div class="page-header">
        <div>
            <h2>Manage Users</h2>
            <p class="page-header-sub"><?= count($users) ?> registered member<?= count($users) !== 1 ? 's' : '' ?></p>
        </div>
    </div>

    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>

    <!-- Add User -->
    <div class="card mb-4">
        <div class="card-header">Add New User</div>
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group col-md">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" placeholder="Full Name" required>
                    </div>
                    <div class="form-group col-md">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group col-md">
                        <label class="form-label">Phone</label>
                        <input class="form-control" name="phone" placeholder="Phone">
                    </div>
                    <div class="form-group col-md">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-control">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group col-md">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                </div>
                <button class="btn btn-primary">Add User</button>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">All Users</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['phone']) ?></td>
                            <td>
                                <span class="status-pill <?= $u['role'] === 'admin' ? 'active' : 'inactive' ?>">
                                    <?= $u['role'] ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <a class="btn btn-sm btn-danger"
                                   href="?delete=<?= $u['id'] ?>"
                                   onclick="return confirm('Delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
