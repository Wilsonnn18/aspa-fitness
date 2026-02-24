<?php
// admin/manage_users.php
require_once __DIR__ . '/../config/app.php';
require_login('admin');

if (($_POST['action'] ?? '') === 'add') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
    $password = $_POST['password'] ?? '';

    if ($name !== '' && $email !== '' && $password !== '') {
        $pass = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (name,email,phone,password,role) VALUES (?,?,?,?,?)');
        $stmt->bind_param('sssss', $name, $email, $phone, $pass, $role);
        $stmt->execute();
    }
}

$deleteId = (int)($_GET['delete'] ?? 0);
if ($deleteId > 0) {
    $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
    $stmt->bind_param('i', $deleteId);
    $stmt->execute();
}

$users = [];
$res = $conn->query('SELECT id,name,email,phone,role,created_at FROM users');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<h2>Manage Users</h2>
<h4>Add new user</h4>
<form method="post" class="mb-4">
    <input type="hidden" name="action" value="add">
    <div class="form-row">
        <div class="col"><input class="form-control" name="name" placeholder="Name" required></div>
        <div class="col"><input type="email" class="form-control" name="email" placeholder="Email" required></div>
        <div class="col"><input class="form-control" name="phone" placeholder="Phone"></div>
        <div class="col">
            <select name="role" class="form-control">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="col"><input type="password" class="form-control" name="password" placeholder="Password" required></div>
        <div class="col"><button class="btn btn-success">Add</button></div>
    </div>
</form>
<table class="table table-bordered">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Created</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['phone']) ?></td>
            <td><?= $u['role'] ?></td>
            <td><?= $u['created_at'] ?></td>
            <td><a class="btn btn-sm btn-danger" href="?delete=<?= $u['id'] ?>" onclick="return confirm('Delete user?');">Delete</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
