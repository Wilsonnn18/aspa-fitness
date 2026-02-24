<?php
// admin/manage_workouts.php
require_once __DIR__ . '/../config/app.php';
require_login('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $level = $_POST['level'] ?? 'beginner';

    if ($title !== '') {
        if ($id > 0) {
            $stmt = $conn->prepare('UPDATE workout_plans SET title = ?, description = ?, level = ? WHERE id = ?');
            $stmt->bind_param('sssi', $title, $desc, $level, $id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare('INSERT INTO workout_plans (title,description,level) VALUES (?,?,?)');
            $stmt->bind_param('sss', $title, $desc, $level);
            $stmt->execute();
        }
    }
}

$deleteId = (int)($_GET['delete'] ?? 0);
if ($deleteId > 0) {
    $stmt = $conn->prepare('DELETE FROM workout_plans WHERE id = ?');
    $stmt->bind_param('i', $deleteId);
    $stmt->execute();
}

$plans = [];
$res = $conn->query('SELECT * FROM workout_plans');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $plans[] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<h2>Manage Workout Plans</h2>
<form method="post" class="mb-4">
    <input type="hidden" name="id" id="wp-id">
    <div class="form-row">
        <div class="col"><input class="form-control" name="title" id="wp-title" placeholder="Title" required></div>
        <div class="col">
            <select name="level" id="wp-level" class="form-control">
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </div>
    </div>
    <div class="form-group mt-2">
        <textarea class="form-control" name="description" id="wp-desc" placeholder="Description"></textarea>
    </div>
    <button class="btn btn-primary" type="submit">Save</button>
</form>
<table class="table table-bordered">
    <thead><tr><th>ID</th><th>Title</th><th>Level</th><th>Description</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach ($plans as $p): ?>
        <tr data-id="<?= $p['id'] ?>" data-title="<?= htmlspecialchars($p['title'], ENT_QUOTES) ?>" data-level="<?= $p['level'] ?>" data-desc="<?= htmlspecialchars($p['description'], ENT_QUOTES) ?>">
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['title']) ?></td>
            <td><?= $p['level'] ?></td>
            <td><?= htmlspecialchars($p['description']) ?></td>
            <td><a class="btn btn-sm btn-secondary edit-wp" href="#">Edit</a> <a class="btn btn-sm btn-danger" href="?delete=<?= $p['id'] ?>" onclick="return confirm('Delete?');">Delete</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<script>
document.querySelectorAll('.edit-wp').forEach(function (el) {
    el.addEventListener('click', function (e) {
        e.preventDefault();
        var tr = this.closest('tr');
        document.getElementById('wp-id').value = tr.dataset.id;
        document.getElementById('wp-title').value = tr.dataset.title;
        document.getElementById('wp-level').value = tr.dataset.level;
        document.getElementById('wp-desc').value = tr.dataset.desc;
    });
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
